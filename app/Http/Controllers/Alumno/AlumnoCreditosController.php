<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\AlumnoDetalle;
use App\Models\Credito;
use App\Models\PagoCuota;
use App\Services\PagoFacilService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class AlumnoCreditosController extends Controller
{
    public function index(Request $request): Response
    {
        $alumno = AlumnoDetalle::query()
            ->where('user_id', $request->user()->id)
            ->first();

        $creditos = collect();

        if ($alumno) {
            $creditosQuery = Credito::query()
                ->with([
                    'inscripcion.ofertaAcademica.carrera',
                    'inscripcion.ofertaAcademica.periodoAcademico',
                    'conceptoPago',
                    'pagoCuotas',
                ])
                ->whereHas('inscripcion', function ($query) use ($alumno) {
                    $query->where('alumno_detalle_id', $alumno->id);
                })
                ->when($request->buscar, function ($query, $buscar) {
                    $query->where(function ($subquery) use ($buscar) {
                        $subquery->where('estado', 'ILIKE', "%{$buscar}%")
                            ->orWhere('tipo_pago', 'ILIKE', "%{$buscar}%")
                            ->orWhere('cantidad_cuotas', 'ILIKE', "%{$buscar}%")
                            ->orWhere('monto_total', 'ILIKE', "%{$buscar}%")
                            ->orWhereHas('conceptoPago', function ($sub) use ($buscar) {
                                $sub->where('nombre', 'ILIKE', "%{$buscar}%");
                            })
                            ->orWhereHas('inscripcion.ofertaAcademica.carrera', function ($sub) use ($buscar) {
                                $sub->where('nombre', 'ILIKE', "%{$buscar}%")
                                    ->orWhere('codigo', 'ILIKE', "%{$buscar}%");
                            });
                    });
                })
                ->latest('id');

            $creditos = $creditosQuery
                ->paginate(10)
                ->withQueryString();

            $creditos->getCollection()->transform(function (Credito $credito) {
                $inscripcion = $credito->inscripcion;

                return [
                    'id' => $credito->id,
                    'monto_total' => (float) $credito->monto_total,
                    'saldo_pendiente' => (float) $credito->saldo_pendiente,
                    'cantidad_cuotas' => (int) $credito->cantidad_cuotas,
                    'fecha_otorgamiento' => $credito->fecha_otorgamiento?->format('Y-m-d'),
                    'fecha_vencimiento' => $credito->fecha_vencimiento?->format('Y-m-d'),
                    'estado' => $credito->estado,
                    'tipo_pago' => $credito->tipo_pago,
                    'concepto_pago' => [
                        'nombre' => $credito->conceptoPago?->nombre,
                    ],
                    'inscripcion' => [
                        'id' => $inscripcion?->id,
                        'carrera' => $inscripcion?->ofertaAcademica?->carrera?->nombre,
                        'carrera_codigo' => $inscripcion?->ofertaAcademica?->carrera?->codigo,
                        'oferta' => $inscripcion?->ofertaAcademica?->nombre,
                        'periodo' => $inscripcion?->ofertaAcademica?->periodoAcademico?->nombre,
                        'gestion' => $inscripcion?->ofertaAcademica?->periodoAcademico?->gestion,
                    ],
                    'cuotas_pagadas' => $credito->pagoCuotas->where('estado_cuota', 'pagado')->count(),
                    'total_cuotas' => $credito->pagoCuotas->count(),
                ];
            });
        }

        return Inertia::render('alumno/misCreditos', [
            'creditos' => $creditos,
            'buscar' => (string) $request->query('buscar', ''),
        ]);
    }

    public function show(Request $request, Credito $credito): Response
    {
        $this->validarPropiedadCredito($request, $credito);

        $credito->load([
            'inscripcion.ofertaAcademica.carrera',
            'inscripcion.ofertaAcademica.periodoAcademico',
            'inscripcion.alumnoDetalle.user',
            'conceptoPago',
            'pagoCuotas',
        ]);

        return Inertia::render('alumno/creditoDetalle', [
            'credito' => [
                'id' => $credito->id,
                'monto_total' => (float) $credito->monto_total,
                'saldo_pendiente' => (float) $credito->saldo_pendiente,
                'cantidad_cuotas' => (int) $credito->cantidad_cuotas,
                'fecha_otorgamiento' => $credito->fecha_otorgamiento?->format('Y-m-d'),
                'fecha_vencimiento' => $credito->fecha_vencimiento?->format('Y-m-d'),
                'estado' => $credito->estado,
                'tipo_pago' => $credito->tipo_pago,
                'concepto_pago' => [
                    'nombre' => $credito->conceptoPago?->nombre,
                ],
                'inscripcion' => [
                    'carrera' => $credito->inscripcion?->ofertaAcademica?->carrera?->nombre,
                    'carrera_codigo' => $credito->inscripcion?->ofertaAcademica?->carrera?->codigo,
                    'oferta' => $credito->inscripcion?->ofertaAcademica?->nombre,
                    'periodo' => $credito->inscripcion?->ofertaAcademica?->periodoAcademico?->nombre,
                    'gestion' => $credito->inscripcion?->ofertaAcademica?->periodoAcademico?->gestion,
                ],
                'pago_cuotas' => $credito->pagoCuotas->map(function (PagoCuota $cuota) {
                    return [
                        'id' => $cuota->id,
                        'numero_cuota' => (int) $cuota->numero_cuota,
                        'monto' => (float) $cuota->monto,
                        'fecha_vencimiento' => $cuota->fecha_vencimiento?->format('Y-m-d'),
                        'fecha_pago' => $cuota->fecha_pago?->format('Y-m-d'),
                        'estado_cuota' => $cuota->estado_cuota,
                        'metodo_pago' => $cuota->metodo_pago,
                    ];
                })->values(),
            ],
        ]);
    }

    public function cuotas(Request $request, Credito $credito)
    {
        $this->validarPropiedadCredito($request, $credito);

        $credito->load([
            'conceptoPago',
            'pagoCuotas' => function ($query) {
                $query->orderBy('numero_cuota');
            },
        ]);

        return response()->json([
            'credito' => [
                'id' => $credito->id,
                'monto_total' => (float) $credito->monto_total,
                'saldo_pendiente' => (float) $credito->saldo_pendiente,
                'cantidad_cuotas' => (int) $credito->cantidad_cuotas,
                'estado' => $credito->estado,
                'tipo_pago' => $credito->tipo_pago,
                'concepto_pago' => [
                    'nombre' => $credito->conceptoPago?->nombre,
                ],
            ],
            'cuotas' => $credito->pagoCuotas->map(function (PagoCuota $cuota) {
                return [
                    'id' => $cuota->id,
                    'numero_cuota' => (int) $cuota->numero_cuota,
                    'monto' => (float) $cuota->monto,
                    'fecha_vencimiento' => $cuota->fecha_vencimiento?->format('Y-m-d'),
                    'fecha_pago' => $cuota->fecha_pago?->format('Y-m-d'),
                    'estado_cuota' => $cuota->estado_cuota,
                    'metodo_pago' => $cuota->metodo_pago,
                ];
            })->values(),
        ]);
    }

    public function showCuota(Request $request, PagoCuota $pagoCuota): Response
    {
        $credito = $pagoCuota->credito;

        $this->validarPropiedadCredito($request, $credito);

        $pagoCuota->load([
            'credito.inscripcion.ofertaAcademica.carrera',
            'credito.inscripcion.ofertaAcademica.periodoAcademico',
            'credito.inscripcion.alumnoDetalle.user',
            'credito.conceptoPago',
        ]);

        return Inertia::render('alumno/cuotaDetalle', [
            'cuota' => [
                'id' => $pagoCuota->id,
                'numero_cuota' => (int) $pagoCuota->numero_cuota,
                'monto' => (float) $pagoCuota->monto,
                'fecha_vencimiento' => $pagoCuota->fecha_vencimiento?->format('Y-m-d'),
                'fecha_pago' => $pagoCuota->fecha_pago?->format('Y-m-d'),
                'estado_cuota' => $pagoCuota->estado_cuota,
                'metodo_pago' => $pagoCuota->metodo_pago,
                'credito' => [
                    'id' => $credito->id,
                    'monto_total' => (float) $credito->monto_total,
                    'saldo_pendiente' => (float) $credito->saldo_pendiente,
                    'cantidad_cuotas' => (int) $credito->cantidad_cuotas,
                    'estado' => $credito->estado,
                    'concepto_pago' => [
                        'nombre' => $credito->conceptoPago?->nombre,
                    ],
                ],
            ],
        ]);
    }

    public function pagarCuota(Request $request, PagoCuota $pagoCuota): JsonResponse
    {
        $this->validarPropiedadCredito($request, $pagoCuota->credito);

        if ($pagoCuota->estado_cuota === 'pagado') {
            return response()->json([
                'ok' => false,
                'message' => 'Esta cuota ya fue pagada.',
            ], 422);
        }

        $datos = $request->validate([
            'metodo_pago' => ['required', 'string', 'in:Efectivo,Transferencia,QR'],
            'fecha_pago' => ['nullable', 'date'],
            'correo_solicitante' => ['nullable', 'email', 'max:255'],
            'observacion' => ['nullable', 'string'],
            'codigo_transaccion' => ['nullable', 'string', 'max:255'],
        ]);

        if ($datos['metodo_pago'] === 'QR') {
            return $this->generarQrCuota($request, $pagoCuota, app(PagoFacilService::class));
        }

        $pagoCuota->update([
            'metodo_pago' => $datos['metodo_pago'],
            'estado_cuota' => 'pagado',
            'fecha_pago' => $datos['fecha_pago'] ?? now()->format('Y-m-d'),
            'correo_solicitante' => $datos['correo_solicitante'] ?? $request->user()->email,
            'observacion' => $datos['observacion'] ?? null,
            'codigo_transaccion' => $datos['codigo_transaccion'] ?? null,
        ]);

        $pagoCuota->credito->recalcularSaldo();

        return response()->json([
            'ok' => true,
            'message' => 'Cuota pagada correctamente.',
            'cuota' => [
                'id' => $pagoCuota->id,
                'estado_cuota' => $pagoCuota->estado_cuota,
                'fecha_pago' => $pagoCuota->fecha_pago?->format('Y-m-d'),
            ],
        ]);
    }

    public function generarQrCuota(Request $request, PagoCuota $pagoCuota, PagoFacilService $pagoFacilService): JsonResponse
    {
        $this->validarPropiedadCredito($request, $pagoCuota->credito);

        if ($pagoCuota->estado_cuota === 'pagado') {
            return response()->json([
                'ok' => false,
                'message' => 'Esta cuota ya fue pagada.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'metodo_pago' => ['required', 'string', 'in:QR'],
            'fecha_pago' => ['nullable', 'date'],
            'correo_solicitante' => ['nullable', 'email', 'max:255'],
            'observacion' => ['nullable', 'string'],
            'codigo_transaccion' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Revisa los datos del pago.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $datos = $validator->validated();

        $pagoCuota->update([
            'metodo_pago' => 'QR',
            'estado_cuota' => 'pendiente',
            'fecha_pago' => null,
            'correo_solicitante' => $datos['correo_solicitante'] ?? $request->user()->email,
            'observacion' => $datos['observacion'] ?? null,
            'codigo_transaccion' => $datos['codigo_transaccion'] ?? null,
        ]);

        try {
            $pagoFacilService->generarQrPagoCuota($pagoCuota);

            $pagoCuota->refresh();

            return response()->json([
                'ok' => true,
                'message' => 'QR de cuota generado correctamente.',
                'cuota' => [
                    'id' => $pagoCuota->id,
                    'payment_number' => $pagoCuota->payment_number,
                    'qr_url' => $pagoCuota->qr_path ? Storage::url($pagoCuota->qr_path) : null,
                    'estado_url' => route('alumno.mis-creditos.cuotas.estado', $pagoCuota),
                    'consultar_url' => route('alumno.mis-creditos.cuotas.consultar-json', $pagoCuota),
                    'show_url' => route('alumno.mis-creditos.cuotas.show', $pagoCuota),
                    'estado' => $pagoCuota->estado_cuota,
                ],
            ]);
        } catch (Throwable $error) {
            $pagoCuota->update([
                'estado_cuota' => 'fallido',
                'observacion' => trim(($pagoCuota->observacion ?? '') . "\nError PagoFácil: " . $error->getMessage()),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'No se pudo generar el QR de PagoFácil: ' . $error->getMessage(),
            ], 500);
        }
    }

    public function estadoCuota(Request $request, PagoCuota $pagoCuota): JsonResponse
    {
        $this->validarPropiedadCredito($request, $pagoCuota->credito);

        $pagoCuota->refresh();

        return response()->json([
            'ok' => true,
            'pago' => [
                'id' => $pagoCuota->id,
                'estado' => $pagoCuota->estado_cuota,
                'fecha_confirmacion' => $pagoCuota->fecha_confirmacion?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function consultarCuotaJson(Request $request, PagoCuota $pagoCuota, PagoFacilService $pagoFacilService): JsonResponse
    {
        $this->validarPropiedadCredito($request, $pagoCuota->credito);

        try {
            $respuesta = $pagoFacilService->consultarTransaccionPagoCuota($pagoCuota);

            $estadoPagoFacil = data_get($respuesta, 'values.paymentStatus');

            if ((string) $estadoPagoFacil === '2') {
                $pagoCuota->update([
                    'estado_cuota' => 'pagado',
                    'fecha_pago' => now()->format('Y-m-d'),
                    'fecha_confirmacion' => now(),
                ]);

                $pagoCuota->credito->recalcularSaldo();
            }

            $pagoCuota->refresh();

            return response()->json([
                'ok' => true,
                'message' => 'Consulta manual realizada correctamente.',
                'pago' => [
                    'id' => $pagoCuota->id,
                    'estado' => $pagoCuota->estado_cuota,
                    'fecha_confirmacion' => $pagoCuota->fecha_confirmacion?->format('Y-m-d H:i:s'),
                ],
                'respuesta_pagofacil' => $respuesta,
            ]);
        } catch (Throwable $error) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo consultar la cuota: ' . $error->getMessage(),
            ], 500);
        }
    }

    private function validarPropiedadCredito(Request $request, Credito $credito): void
    {
        $alumno = $this->obtenerAlumnoDetalle($request);

        if (! $alumno || $credito->inscripcion?->alumno_detalle_id !== $alumno->id) {
            abort(403);
        }
    }

    private function obtenerAlumnoDetalle(Request $request): ?AlumnoDetalle
    {
        return AlumnoDetalle::query()
            ->where('user_id', $request->user()->id)
            ->first();
    }
}
