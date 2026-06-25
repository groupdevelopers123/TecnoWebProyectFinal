<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PagoContadoRequest;
use App\Models\ConceptoPago;
use App\Models\Inscripcion;
use App\Models\PagoContado;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PagoContadoController extends Controller
{
    public function index(Request $request)
    {
        $pagos = PagoContado::query()
            ->with([
                'inscripcion.alumnoDetalle.user',
                'inscripcion.ofertaAcademica.carrera',
                'conceptoPago',
            ])
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('metodo_pago', 'ILIKE', "%{$buscar}%")
                        ->orWhere('estado', 'ILIKE', "%{$buscar}%")
                        ->orWhere('codigo_transaccion', 'ILIKE', "%{$buscar}%")
                        ->orWhere('payment_number', 'ILIKE', "%{$buscar}%")
                        ->orWhereHas('conceptoPago', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('inscripcion.alumnoDetalle.user', function ($sub) use ($buscar) {
                            $sub->where('nombres', 'ILIKE', "%{$buscar}%")
                                ->orWhere('apellidos', 'ILIKE', "%{$buscar}%")
                                ->orWhere('ci', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('inscripcion.ofertaAcademica.carrera', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%")
                                ->orWhere('codigo', 'ILIKE', "%{$buscar}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'data' => $pagos->getCollection()->map(function ($pago) {
                    return [
                        'id' => $pago->id,
                        'monto_pagado' => $pago->monto_pagado,
                        'fecha_pago' => $pago->fecha_pago?->format('Y-m-d'),
                        'metodo_pago' => $pago->metodo_pago,
                        'estado' => $pago->estado,
                        'codigo_transaccion' => $pago->codigo_transaccion,
                        'payment_number' => $pago->payment_number,
                        'qr_url' => $pago->qr_path ? Storage::url($pago->qr_path) : null,
                        'fecha_confirmacion' => $pago->fecha_confirmacion?->format('Y-m-d H:i:s'),

                        'concepto_pago' => [
                            'nombre' => $pago->conceptoPago?->nombre,
                        ],

                        'alumno' => [
                            'nombres' => $pago->inscripcion?->alumnoDetalle?->user?->nombres,
                            'apellidos' => $pago->inscripcion?->alumnoDetalle?->user?->apellidos,
                            'ci' => $pago->inscripcion?->alumnoDetalle?->user?->ci,
                        ],

                        'carrera' => [
                            'codigo' => $pago->inscripcion?->ofertaAcademica?->carrera?->codigo,
                            'nombre' => $pago->inscripcion?->ofertaAcademica?->carrera?->nombre,
                        ],
                    ];
                })->values(),

                'pagination' => [
                    'current_page' => $pagos->currentPage(),
                    'last_page' => $pagos->lastPage(),
                    'per_page' => $pagos->perPage(),
                    'total' => $pagos->total(),
                    'prev_page_url' => $pagos->previousPageUrl(),
                    'next_page_url' => $pagos->nextPageUrl(),
                ],
            ]);
        }

        return view('admin.pagos.pago-contados.index', compact('pagos'));
    }

    public function create()
    {
        return view('admin.pagos.pago-contados.create', $this->formData());
    }

    public function store(PagoContadoRequest $request, PagoFacilService $pagoFacilService)
    {
        $data = $request->validated();

        $accion = $request->input('accion', 'guardar');

        if ($data['metodo_pago'] === 'QR') {
            $data['estado'] = 'Pendiente';
        }

        $pago = PagoContado::create($data);

        if ($data['metodo_pago'] === 'QR' && $accion === 'generar_qr') {
            try {
                $pagoFacilService->generarQr($pago);

                $pago->refresh();

                if ($request->ajax()) {
                    return response()->json([
                        'ok' => true,
                        'message' => 'QR PagoFácil generado correctamente.',
                        'pago' => [
                            'id' => $pago->id,
                            'estado' => $pago->estado,
                            'monto_pagado' => $pago->monto_pagado,
                            'payment_number' => $pago->payment_number,
                            'codigo_transaccion' => $pago->codigo_transaccion,
                            'qr_url' => $pago->qr_path ? Storage::url($pago->qr_path) : null,
                            'show_url' => route('admin.pago-contados.show', $pago),
                            'estado_url' => route('admin.pago-contados.estado', $pago),
                            'consultar_url' => route('admin.pago-contados.consultar-json', $pago),
                        ],
                    ]);
                }

                return redirect()
                    ->route('admin.pago-contados.show', $pago)
                    ->with('success', 'Pago registrado y QR PagoFácil generado correctamente.');
            } catch (Throwable $e) {
                $pago->update([
                    'estado' => 'Fallido',
                    'observacion' => trim(($pago->observacion ?? '') . "\nError PagoFácil: " . $e->getMessage()),
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'El pago fue registrado, pero no se pudo generar el QR: ' . $e->getMessage(),
                        'show_url' => route('admin.pago-contados.show', $pago),
                    ], 500);
                }

                return redirect()
                    ->route('admin.pago-contados.show', $pago)
                    ->with('error', 'El pago fue registrado, pero no se pudo generar el QR: ' . $e->getMessage());
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Pago al contado registrado correctamente.',
                'redirect_url' => route('admin.pago-contados.show', $pago),
            ]);
        }

        return redirect()
            ->route('admin.pago-contados.show', $pago)
            ->with('success', 'Pago al contado registrado correctamente.');
    }

    public function show(PagoContado $pago_contado)
    {
        $pago_contado->load([
            'inscripcion.alumnoDetalle.user',
            'inscripcion.ofertaAcademica.carrera',
            'conceptoPago',
        ]);

        return view('admin.pagos.pago-contados.show', [
            'pago' => $pago_contado,
        ]);
    }

    public function edit(PagoContado $pago_contado)
    {
        return view('admin.pagos.pago-contados.edit', [
            ...$this->formData(),
            'pago' => $pago_contado,
        ]);
    }

    public function update(PagoContadoRequest $request, PagoContado $pago_contado)
    {
        $pago_contado->update($request->validated());

        return redirect()
            ->route('admin.pago-contados.index')
            ->with('success', 'Pago al contado actualizado correctamente.');
    }

    public function destroy(PagoContado $pago_contado)
    {
        $pago_contado->update([
            'estado' => $pago_contado->estado === 'Anulado' ? 'Pendiente' : 'Anulado',
        ]);

        return redirect()
            ->route('admin.pagos.pago-contados.index')
            ->with('success', 'Estado del pago actualizado correctamente.');
    }

    public function consultar(PagoContado $pago_contado, PagoFacilService $pagoFacilService)
    {
        try {
            $respuesta = $pagoFacilService->consultarTransaccion($pago_contado);

            $estadoPagoFacil = data_get($respuesta, 'values.paymentStatus');

            if ((string) $estadoPagoFacil === '2' || (string) $estadoPagoFacil === 'Confirmado') {
                $pago_contado->update([
                    'estado' => 'Confirmado',
                    'fecha_confirmacion' => now(),
                ]);
            }

            return redirect()
                ->route('admin.pagos.pago-contados.show', $pago_contado)
                ->with('success', 'Consulta realizada correctamente.');
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.pagos.pago-contados.show', $pago_contado)
                ->with('error', 'No se pudo consultar el pago: ' . $e->getMessage());
        }
    }

    private function formData(): array
    {
        return [
            'inscripciones' => Inscripcion::query()
                ->with([
                    'alumnoDetalle.user',
                    'ofertaAcademica.carrera',
                    'ofertaAcademica.periodoAcademico',
                ])
                ->latest()
                ->get(),

            'conceptos' => ConceptoPago::query()
                ->where('estado', 'Activo')
                ->orderBy('nombre')
                ->get(),
        ];
    }

    public function estado(PagoContado $pago_contado)
    {
        $pago_contado->refresh();

        return response()->json([
            'ok' => true,
            'pago' => [
                'id' => $pago_contado->id,
                'estado' => $pago_contado->estado,
                'fecha_confirmacion' => $pago_contado->fecha_confirmacion?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function consultarJson(PagoContado $pago_contado, PagoFacilService $pagoFacilService)
    {
        try {
            $respuesta = $pagoFacilService->consultarTransaccion($pago_contado);

            $estadoPagoFacil = data_get($respuesta, 'values.paymentStatus');

            if (
                (string) $estadoPagoFacil === '2' ||
                (string) $estadoPagoFacil === 'Confirmado' ||
                (string) $estadoPagoFacil === 'Pagado'
            ) {
                $pago_contado->update([
                    'estado' => 'Confirmado',
                    'fecha_confirmacion' => now(),
                ]);
            }

            $pago_contado->refresh();

            return response()->json([
                'ok' => true,
                'message' => 'Consulta manual realizada correctamente.',
                'pago' => [
                    'id' => $pago_contado->id,
                    'estado' => $pago_contado->estado,
                    'fecha_confirmacion' => $pago_contado->fecha_confirmacion?->format('Y-m-d H:i:s'),
                ],
                'respuesta_pagofacil' => $respuesta,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo consultar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }
}