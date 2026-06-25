<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\AlumnoDetalle;
use App\Models\ConceptoPago;
use App\Models\Inscripcion;
use App\Models\PagoContado;
use App\Services\PagoFacilService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class AlumnoPagoContadoController extends Controller
{
    public function index(Request $request): Response
    {
        $alumno = AlumnoDetalle::query()
            ->where('user_id', $request->user()->id)
            ->first();

        $inscripciones = collect();

        if ($alumno) {
            $inscripciones = Inscripcion::query()
                ->with([
                    'ofertaAcademica.carrera',
                    'ofertaAcademica.periodoAcademico',
                ])
                ->where('alumno_detalle_id', $alumno->id)
                ->latest('fecha_inscripcion')
                ->get()
                ->map(function (Inscripcion $inscripcion) {
                    return [
                        'id' => $inscripcion->id,
                        'carrera' => $inscripcion->ofertaAcademica?->carrera?->nombre,
                        'carrera_codigo' => $inscripcion->ofertaAcademica?->carrera?->codigo,
                        'oferta' => $inscripcion->ofertaAcademica?->nombre,
                        'periodo' => $inscripcion->ofertaAcademica?->periodoAcademico?->nombre,
                        'gestion' => $inscripcion->ofertaAcademica?->periodoAcademico?->gestion,
                        'precio_mensualidad' => (float) ($inscripcion->ofertaAcademica?->precio_mensualidad ?? 0),
                        'fecha_inscripcion' => $inscripcion->fecha_inscripcion?->format('Y-m-d'),
                    ];
                })
                ->values();
        }

        $conceptos = ConceptoPago::query()
            ->where('estado', 'Activo')
            ->where(function ($query) {
                $query->where('nombre', 'NOT ILIKE', '%matrícula%')
                    ->where('nombre', 'NOT ILIKE', '%matricula%');
            })
            ->orderBy('nombre')
            ->get()
            ->map(function (ConceptoPago $concepto) {
                return [
                    'id' => $concepto->id,
                    'nombre' => $concepto->nombre,
                    'descripcion' => $concepto->descripcion,
                ];
            })
            ->values();

        $pagosQuery = PagoContado::query()
            ->with([
                'inscripcion.ofertaAcademica.carrera',
                'inscripcion.ofertaAcademica.periodoAcademico',
                'conceptoPago',
            ])
            ->whereHas('inscripcion.alumnoDetalle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($subquery) use ($buscar) {
                    $subquery->where('metodo_pago', 'ILIKE', "%{$buscar}%")
                        ->orWhere('estado', 'ILIKE', "%{$buscar}%")
                        ->orWhere('codigo_transaccion', 'ILIKE', "%{$buscar}%")
                        ->orWhere('payment_number', 'ILIKE', "%{$buscar}%")
                        ->orWhere('monto_pagado', 'ILIKE', "%{$buscar}%")
                        ->orWhereHas('conceptoPago', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('inscripcion.ofertaAcademica.carrera', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%")
                                ->orWhere('codigo', 'ILIKE', "%{$buscar}%");
                        });
                });
            })
            ->latest('fecha_pago')
            ->latest('id');

        $pagos = $pagosQuery
            ->paginate(10)
            ->withQueryString();

        $pagos->getCollection()->transform(function (PagoContado $pago) {
            return [
                'id' => $pago->id,
                'monto_pagado' => (float) $pago->monto_pagado,
                'fecha_pago' => $pago->fecha_pago?->format('Y-m-d'),
                'metodo_pago' => $pago->metodo_pago,
                'estado' => $pago->estado,
                'codigo_transaccion' => $pago->codigo_transaccion,
                'payment_number' => $pago->payment_number,
                'qr_url' => $pago->qr_path ? Storage::url($pago->qr_path) : null,
                'fecha_confirmacion' => $pago->fecha_confirmacion?->format('Y-m-d H:i:s'),
                'inscripcion' => [
                    'id' => $pago->inscripcion?->id,
                    'carrera' => $pago->inscripcion?->ofertaAcademica?->carrera?->nombre,
                    'carrera_codigo' => $pago->inscripcion?->ofertaAcademica?->carrera?->codigo,
                    'oferta' => $pago->inscripcion?->ofertaAcademica?->nombre,
                    'periodo' => $pago->inscripcion?->ofertaAcademica?->periodoAcademico?->nombre,
                    'gestion' => $pago->inscripcion?->ofertaAcademica?->periodoAcademico?->gestion,
                ],
                'concepto_pago' => [
                    'nombre' => $pago->conceptoPago?->nombre,
                ],
            ];
        });

        return Inertia::render('alumno/misPagos', [
            'pagos' => $pagos,
            'inscripciones' => $inscripciones,
            'conceptos' => $conceptos,
            'buscar' => (string) $request->query('buscar', ''),
        ]);
    }

    public function store(Request $request, PagoFacilService $pagoFacilService): RedirectResponse
    {
        $datos = $request->validate([
            'inscripcion_id' => [
                'required',
                'integer',
                'exists:inscripciones,id',
            ],
            'concepto_pago_id' => [
                'required',
                'integer',
                'exists:concepto_pagos,id',
            ],
            'monto_pagado' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'fecha_pago' => [
                'required',
                'date',
            ],
            'metodo_pago' => [
                'required',
                'string',
                'in:Efectivo,Transferencia,QR',
            ],
            'correo_solicitante' => [
                'nullable',
                'email',
                'max:255',
            ],
            'observacion' => [
                'nullable',
                'string',
            ],
        ]);

        $inscripcion = $this->obtenerInscripcionDelAlumno($request, (int) $datos['inscripcion_id']);

        $concepto = ConceptoPago::query()
            ->where('id', $datos['concepto_pago_id'])
            ->where('estado', 'Activo')
            ->where('nombre', 'NOT ILIKE', '%matrícula%')
            ->where('nombre', 'NOT ILIKE', '%matricula%')
            ->first();

        if (! $concepto) {
            throw ValidationException::withMessages([
                'concepto_pago_id' => 'El concepto seleccionado no está disponible o corresponde a matrícula.',
            ]);
        }

        $montoMensualidad = (float) ($inscripcion->ofertaAcademica?->precio_mensualidad ?? 0);

        if ($montoMensualidad <= 0) {
            throw ValidationException::withMessages([
                'monto_pagado' => 'La oferta académica seleccionada no tiene precio de mensualidad configurado.',
            ]);
        }

        $pago = PagoContado::create([
            'inscripcion_id' => $inscripcion->id,
            'concepto_pago_id' => $concepto->id,
            'monto_pagado' => $montoMensualidad,
            'fecha_pago' => $datos['fecha_pago'],
            'metodo_pago' => $datos['metodo_pago'],
            'estado' => 'Pendiente',
            'correo_solicitante' => $datos['correo_solicitante'] ?? $request->user()->email,
            'observacion' => $datos['observacion'] ?? null,
        ]);

        if ($datos['metodo_pago'] === 'QR') {
            try {
                $pagoFacilService->generarQr($pago);
                $pago->refresh();

                return back()->with([
                    'success' => 'El pago fue registrado y el QR de PagoFácil fue generado correctamente.',
                    'pago_generado' => $this->formatearPagoGenerado($pago),
                ]);
            } catch (Throwable $error) {
                $pago->update([
                    'estado' => 'Fallido',
                    'observacion' => trim(
                        ($pago->observacion ?? '') . "\nError PagoFácil: " . $error->getMessage()
                    ),
                ]);

                return back()->with('error', 'El pago fue registrado, pero no se pudo generar el QR de PagoFácil.');
            }
        }

        return back()->with('success', 'El pago fue registrado correctamente.');
    }

    public function generarQr(Request $request, PagoFacilService $pagoFacilService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'inscripcion_id' => [
                'required',
                'integer',
                'exists:inscripciones,id',
            ],
            'concepto_pago_id' => [
                'required',
                'integer',
                'exists:concepto_pagos,id',
            ],
            'fecha_pago' => [
                'required',
                'date',
            ],
            'metodo_pago' => [
                'required',
                'string',
                'in:QR',
            ],
            'correo_solicitante' => [
                'nullable',
                'email',
                'max:255',
            ],
            'observacion' => [
                'nullable',
                'string',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Revisa los datos del pago.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $datos = $validator->validated();

        $inscripcion = $this->obtenerInscripcionDelAlumno($request, (int) $datos['inscripcion_id']);

        $concepto = ConceptoPago::query()
            ->where('id', $datos['concepto_pago_id'])
            ->where('estado', 'Activo')
            ->where('nombre', 'NOT ILIKE', '%matrícula%')
            ->where('nombre', 'NOT ILIKE', '%matricula%')
            ->first();

        if (! $concepto) {
            return response()->json([
                'ok' => false,
                'message' => 'El concepto seleccionado no está disponible o corresponde a matrícula.',
            ], 422);
        }

        $montoMensualidad = (float) ($inscripcion->ofertaAcademica?->precio_mensualidad ?? 0);

        if ($montoMensualidad <= 0) {
            return response()->json([
                'ok' => false,
                'message' => 'La oferta académica seleccionada no tiene precio de mensualidad configurado.',
            ], 422);
        }

        $pago = PagoContado::create([
            'inscripcion_id' => $inscripcion->id,
            'concepto_pago_id' => $concepto->id,
            'monto_pagado' => $montoMensualidad,
            'fecha_pago' => $datos['fecha_pago'],
            'metodo_pago' => 'QR',
            'estado' => 'Pendiente',
            'correo_solicitante' => $datos['correo_solicitante'] ?? $request->user()->email,
            'observacion' => $datos['observacion'] ?? null,
        ]);

        try {
            $pagoFacilService->generarQr($pago);
            $pago->refresh();

            return response()->json([
                'ok' => true,
                'message' => 'QR PagoFácil generado correctamente.',
                'pago' => $this->formatearPagoGenerado($pago),
            ]);
        } catch (Throwable $error) {
            $pago->update([
                'estado' => 'Fallido',
                'observacion' => trim(
                    ($pago->observacion ?? '') . "\nError PagoFácil: " . $error->getMessage()
                ),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'El pago fue registrado, pero no se pudo generar el QR de PagoFácil: ' . $error->getMessage(),
            ], 500);
        }
    }

    public function estado(Request $request, PagoContado $pago_contado): JsonResponse
    {
        $pago = $this->obtenerPagoDelAlumno($request, $pago_contado);

        return response()->json([
            'ok' => true,
            'pago' => [
                'id' => $pago->id,
                'estado' => $pago->estado,
                'fecha_confirmacion' => $pago->fecha_confirmacion?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function consultarJson(Request $request, PagoContado $pago_contado, PagoFacilService $pagoFacilService): JsonResponse
    {
        $pago = $this->obtenerPagoDelAlumno($request, $pago_contado);

        try {
            $respuesta = $pagoFacilService->consultarTransaccion($pago);

            $estadoPagoFacil = data_get($respuesta, 'values.paymentStatus');

            if (
                (string) $estadoPagoFacil === '2' ||
                (string) $estadoPagoFacil === 'Confirmado' ||
                (string) $estadoPagoFacil === 'Pagado'
            ) {
                $pago->update([
                    'estado' => 'Confirmado',
                    'fecha_confirmacion' => now(),
                ]);
            }

            $pago->refresh();

            return response()->json([
                'ok' => true,
                'message' => 'Consulta manual realizada correctamente.',
                'pago' => [
                    'id' => $pago->id,
                    'estado' => $pago->estado,
                    'fecha_confirmacion' => $pago->fecha_confirmacion?->format('Y-m-d H:i:s'),
                ],
                'respuesta_pagofacil' => $respuesta,
            ]);
        } catch (Throwable $error) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo consultar el pago: ' . $error->getMessage(),
            ], 500);
        }
    }

    private function obtenerInscripcionDelAlumno(Request $request, int $inscripcionId): Inscripcion
    {
        $alumno = AlumnoDetalle::query()
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $alumno) {
            throw ValidationException::withMessages([
                'inscripcion_id' => 'El usuario autenticado no tiene un registro de alumno.',
            ]);
        }

        $inscripcion = Inscripcion::query()
            ->where('id', $inscripcionId)
            ->where('alumno_detalle_id', $alumno->id)
            ->with(['ofertaAcademica.carrera', 'ofertaAcademica.periodoAcademico'])
            ->first();

        if (! $inscripcion) {
            throw ValidationException::withMessages([
                'inscripcion_id' => 'La inscripción seleccionada no pertenece al alumno autenticado.',
            ]);
        }

        return $inscripcion;
    }

    private function obtenerPagoDelAlumno(Request $request, PagoContado $pagoContado): PagoContado
    {
        $pago = PagoContado::query()
            ->with([
                'inscripcion.alumnoDetalle.user',
                'inscripcion.ofertaAcademica.carrera',
                'inscripcion.ofertaAcademica.periodoAcademico',
                'conceptoPago',
            ])
            ->whereKey($pagoContado->id)
            ->whereHas('inscripcion.alumnoDetalle', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->firstOrFail();

        return $pago;
    }

    private function formatearPagoGenerado(PagoContado $pago): array
    {
        return [
            'id' => $pago->id,
            'estado' => $pago->estado,
            'metodo_pago' => $pago->metodo_pago,
            'monto_pagado' => (float) $pago->monto_pagado,
            'payment_number' => $pago->payment_number,
            'codigo_transaccion' => $pago->codigo_transaccion,
            'qr_url' => $pago->qr_path ? Storage::url($pago->qr_path) : null,
            'show_url' => route('admin.pago-contados.show', $pago),
            'estado_url' => route('alumno.mis-pagos.estado', $pago),
            'consultar_url' => route('alumno.mis-pagos.consultar-json', $pago),
        ];
    }
}