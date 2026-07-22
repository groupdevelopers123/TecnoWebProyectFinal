<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PagoCuotaRequest;
use App\Models\Credito;
use App\Models\PagoCuota;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Throwable;

class PagoCuotaController extends Controller
{
    public function index(Request $request)
    {
        $cuotas = PagoCuota::query()
            ->with([
                'credito.inscripcion.alumnoDetalle.user',
                'credito.inscripcion.ofertaAcademica.carrera',
                'credito.conceptoPago',
            ])
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('estado_cuota', 'ILIKE', "%{$buscar}%")
                        ->orWhere('metodo_pago', 'ILIKE', "%{$buscar}%")
                        ->orWhere('payment_number', 'ILIKE', "%{$buscar}%")
                        ->orWhereHas('credito.inscripcion.alumnoDetalle.user', function ($sub) use ($buscar) {
                            $sub->where('nombres', 'ILIKE', "%{$buscar}%")
                                ->orWhere('apellidos', 'ILIKE', "%{$buscar}%")
                                ->orWhere('ci', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('credito.conceptoPago', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%");
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        if (($request->ajax() || $request->wantsJson()) && ! $request->header('X-Inertia')) {
            return response()->json([
                'data' => $cuotas->getCollection()->map(fn ($cuota) => $this->serializeCuota($cuota))->values(),
                'pagination' => [
                    'current_page' => $cuotas->currentPage(),
                    'last_page' => $cuotas->lastPage(),
                    'per_page' => $cuotas->perPage(),
                    'total' => $cuotas->total(),
                    'prev_page_url' => $cuotas->previousPageUrl(),
                    'next_page_url' => $cuotas->nextPageUrl(),
                ],
            ]);
        }

        return Inertia::render('admin/pagos/pago-cuotas/Index', [
            'cuotas' => [
                'data' => $cuotas->getCollection()->map(fn ($cuota) => $this->serializeCuota($cuota))->values(),
                'pagination' => [
                    'current_page' => $cuotas->currentPage(),
                    'last_page' => $cuotas->lastPage(),
                    'per_page' => $cuotas->perPage(),
                    'total' => $cuotas->total(),
                    'prev_page_url' => $cuotas->previousPageUrl(),
                    'next_page_url' => $cuotas->nextPageUrl(),
                ],
            ],
            'request' => [
                'buscar' => $request->buscar,
            ],
        ]);
    }

    public function cuotasPorCredito(Credito $credito, Request $request)
    {
        $credito->load([
            'inscripcion.alumnoDetalle.user',
            'conceptoPago',
            'pagoCuotas' => function ($query) {
                $query->orderBy('numero_cuota');
            },
        ]);

        if (($request->ajax() || $request->wantsJson()) && ! $request->header('X-Inertia')) {
            return response()->json([
                'credito' => [
                    'id' => $credito->id,
                    'monto_total' => (float) $credito->monto_total,
                    'saldo_pendiente' => (float) $credito->saldo_pendiente,
                    'inscripcion' => [
                        'alumnoDetalle' => [
                            'user' => [
                                'nombres' => $credito->inscripcion?->alumnoDetalle?->user?->nombres,
                                'apellidos' => $credito->inscripcion?->alumnoDetalle?->user?->apellidos,
                                'ci' => $credito->inscripcion?->alumnoDetalle?->user?->ci,
                            ],
                        ],
                    ],
                    'conceptoPago' => [
                        'nombre' => $credito->conceptoPago?->nombre,
                    ],
                ],
                'cuotas' => $credito->pagoCuotas->map(function ($cuota) {
                    return [
                        'id' => $cuota->id,
                        'numero_cuota' => $cuota->numero_cuota,
                        'monto' => (float) $cuota->monto,
                        'fecha_vencimiento' => $cuota->fecha_vencimiento?->format('Y-m-d'),
                        'fecha_pago' => $cuota->fecha_pago?->format('Y-m-d'),
                        'estado_cuota' => $cuota->estado_cuota,
                    ];
                })->values(),
            ]);
        }

        return Inertia::render('admin/pagos/pago-cuotas/ModalIndex', [
            'credito' => $credito,
            'cuotas' => $credito->pagoCuotas->map(fn ($cuota) => [
                'id' => $cuota->id,
                'numero_cuota' => $cuota->numero_cuota,
                'monto' => (float) $cuota->monto,
                'fecha_vencimiento' => $cuota->fecha_vencimiento?->format('Y-m-d'),
                'fecha_pago' => $cuota->fecha_pago?->format('Y-m-d'),
                'estado_cuota' => $cuota->estado_cuota,
            ])->values(),
        ]);
    }

    public function show(PagoCuota $pago_cuota)
    {
        $pago_cuota->load([
            'credito.inscripcion.alumnoDetalle.user',
            'credito.inscripcion.ofertaAcademica.carrera',
            'credito.conceptoPago',
        ]);

        return Inertia::render('admin/pagos/pago-cuotas/Show', [
            'cuota' => $this->serializeCuota($pago_cuota),
        ]);
    }

    public function edit(PagoCuota $pago_cuota)
    {
        if ($pago_cuota->estado_cuota === 'pagado') {
            return redirect()
                ->route('admin.pago-cuotas.show', $pago_cuota)
                ->with('error', 'Esta cuota ya fue pagada y no puede modificarse.');
        }

        $pago_cuota->load([
            'credito.inscripcion.alumnoDetalle.user',
            'credito.inscripcion.ofertaAcademica.carrera',
            'credito.conceptoPago',
        ]);

        return Inertia::render('admin/pagos/pago-cuotas/Edit', [
            'cuota' => $this->serializeCuota($pago_cuota),
        ]);
    }

    public function update(PagoCuotaRequest $request, PagoCuota $pago_cuota, PagoFacilService $pagoFacilService)
    {
        if ($pago_cuota->estado_cuota === 'pagado') {
            if (($request->ajax() || $request->wantsJson()) && ! $request->header('X-Inertia')) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Esta cuota ya fue pagada y no puede modificarse.',
                ], 422);
            }

            return redirect()
                ->route('admin.pago-cuotas.show', $pago_cuota)
                ->with('error', 'Esta cuota ya fue pagada y no puede modificarse.');
        }

        $data = $request->validated();
        $accion = $request->input('accion', 'guardar');

        if ($data['metodo_pago'] === 'QR') {
            $data['estado_cuota'] = 'pendiente';
            $data['fecha_pago'] = null;
        }

        if ($data['metodo_pago'] !== 'QR' && $data['estado_cuota'] === 'pagado') {
            $data['fecha_pago'] = $data['fecha_pago'] ?: now()->format('Y-m-d');
            $data['fecha_confirmacion'] = now();
        }

        $pago_cuota->update($data);

        if ($data['metodo_pago'] === 'QR' && $accion === 'generar_qr') {
            try {
                $pagoFacilService->generarQrPagoCuota($pago_cuota);

                $pago_cuota->refresh();

                if (($request->ajax() || $request->wantsJson()) && ! $request->header('X-Inertia')) {
                    return response()->json([
                        'ok' => true,
                        'message' => 'QR de cuota generado correctamente.',
                        'cuota' => [
                            'id' => $pago_cuota->id,
                            'estado' => $pago_cuota->estado_cuota,
                            'payment_number' => $pago_cuota->payment_number,
                            'qr_url' => $pago_cuota->qr_path ? Storage::url($pago_cuota->qr_path) : null,
                            'show_url' => route('admin.pago-cuotas.show', $pago_cuota),
                            'estado_url' => route('admin.pago-cuotas.estado', $pago_cuota),
                            'consultar_url' => route('admin.pago-cuotas.consultar-json', $pago_cuota),
                        ],
                    ]);
                }

                return redirect()
                    ->route('admin.pago-cuotas.show', $pago_cuota)
                    ->with('success', 'QR de cuota generado correctamente.');
            } catch (Throwable $e) {
                $pago_cuota->update([
                    'estado_cuota' => 'fallido',
                    'observacion' => trim(($pago_cuota->observacion ?? '') . "\nError PagoFácil: " . $e->getMessage()),
                ]);

                if (($request->ajax() || $request->wantsJson()) && ! $request->header('X-Inertia')) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'No se pudo generar el QR: ' . $e->getMessage(),
                    ], 500);
                }

                return redirect()
                    ->route('admin.pago-cuotas.show', $pago_cuota)
                    ->with('error', 'No se pudo generar el QR: ' . $e->getMessage());
            }
        }

        $pago_cuota->credito->recalcularSaldo();

        return redirect()
            ->route('admin.pago-cuotas.show', $pago_cuota)
            ->with('success', 'Cuota actualizada correctamente.');
    }

    public function estado(PagoCuota $pago_cuota)
    {
        $pago_cuota->refresh();

        return response()->json([
            'ok' => true,
            'cuota' => [
                'id' => $pago_cuota->id,
                'estado' => $pago_cuota->estado_cuota,
                'fecha_confirmacion' => $pago_cuota->fecha_confirmacion?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function consultarJson(PagoCuota $pago_cuota, PagoFacilService $pagoFacilService)
    {
        try {
            $respuesta = $pagoFacilService->consultarTransaccionPagoCuota($pago_cuota);

            $estadoPagoFacil = data_get($respuesta, 'values.paymentStatus');

            if ((string) $estadoPagoFacil === '2') {
                $pago_cuota->update([
                    'estado_cuota' => 'pagado',
                    'fecha_pago' => now()->format('Y-m-d'),
                    'fecha_confirmacion' => now(),
                ]);

                $pago_cuota->credito->recalcularSaldo();
            }

            $pago_cuota->refresh();

            return response()->json([
                'ok' => true,
                'message' => 'Consulta manual realizada correctamente.',
                'cuota' => [
                    'id' => $pago_cuota->id,
                    'estado' => $pago_cuota->estado_cuota,
                    'fecha_confirmacion' => $pago_cuota->fecha_confirmacion?->format('Y-m-d H:i:s'),
                ],
                'respuesta_pagofacil' => $respuesta,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo consultar la cuota: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function serializeCuota(PagoCuota $pagoCuota): array
    {
        return [
            'id' => $pagoCuota->id,
            'numero_cuota' => $pagoCuota->numero_cuota,
            'monto' => (float) $pagoCuota->monto,
            'fecha_vencimiento' => $pagoCuota->fecha_vencimiento?->format('Y-m-d'),
            'fecha_pago' => $pagoCuota->fecha_pago?->format('Y-m-d'),
            'estado_cuota' => $pagoCuota->estado_cuota,
            'metodo_pago' => $pagoCuota->metodo_pago,
            'observacion' => $pagoCuota->observacion,
            'codigo_transaccion' => $pagoCuota->codigo_transaccion,
            'correo_solicitante' => $pagoCuota->correo_solicitante,
            'payment_number' => $pagoCuota->payment_number,
            'qr_path' => $pagoCuota->qr_path,
            'qr_url' => $pagoCuota->qr_path ? Storage::url($pagoCuota->qr_path) : null,
            'fecha_confirmacion' => $pagoCuota->fecha_confirmacion?->format('Y-m-d H:i:s'),
            'concepto' => [
                'nombre' => $pagoCuota->credito?->conceptoPago?->nombre,
            ],
            'alumno' => [
                'nombres' => $pagoCuota->credito?->inscripcion?->alumnoDetalle?->user?->nombres,
                'apellidos' => $pagoCuota->credito?->inscripcion?->alumnoDetalle?->user?->apellidos,
                'ci' => $pagoCuota->credito?->inscripcion?->alumnoDetalle?->user?->ci,
            ],
            'carrera' => [
                'codigo' => $pagoCuota->credito?->inscripcion?->ofertaAcademica?->carrera?->codigo,
                'nombre' => $pagoCuota->credito?->inscripcion?->ofertaAcademica?->carrera?->nombre,
            ],
        ];
    }
}