<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PagoCuotaRequest;
use App\Models\Credito;
use App\Models\PagoCuota;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        return view('admin.pagos.pago-cuotas.index', compact('cuotas'));
    }

    public function cuotasPorCredito(Credito $credito)
    {
        $credito->load([
            'inscripcion.alumnoDetalle.user',
            'conceptoPago',
            'pagoCuotas' => function ($query) {
                $query->orderBy('numero_cuota');
            },
        ]);

        return view('admin.pagos.pago-cuotas._modal-index', compact('credito'));
    }

    public function show(PagoCuota $pago_cuota)
    {
        $pago_cuota->load([
            'credito.inscripcion.alumnoDetalle.user',
            'credito.inscripcion.ofertaAcademica.carrera',
            'credito.conceptoPago',
        ]);

        return view('admin.pagos.pago-cuotas.show', [
            'cuota' => $pago_cuota,
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

        return view('admin.pagos.pago-cuotas.edit', [
            'cuota' => $pago_cuota,
        ]);
    }

    public function update(PagoCuotaRequest $request, PagoCuota $pago_cuota, PagoFacilService $pagoFacilService)
    {
        if ($pago_cuota->estado_cuota === 'pagado') {
            if ($request->ajax()) {
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

                if ($request->ajax()) {
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

                if ($request->ajax()) {
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
}