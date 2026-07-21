<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreditoRequest;
use App\Models\ConceptoPago;
use App\Models\Credito;
use App\Models\Inscripcion;
use App\Notifications\CreditoHabilitado;
use App\Services\CreditoCuotaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CreditoController extends Controller
{
    public function index(Request $request)
    {
        $creditos = Credito::query()
            ->with([
                'inscripcion.alumnoDetalle.user',
                'inscripcion.ofertaAcademica.carrera',
                'inscripcion.ofertaAcademica.periodoAcademico',
                'conceptoPago',
                'pagoCuotas',
            ])
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('tipo_pago', 'ILIKE', "%{$buscar}%")
                        ->orWhere('estado', 'ILIKE', "%{$buscar}%")
                        ->orWhereHas('conceptoPago', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('inscripcion.alumnoDetalle.user', function ($sub) use ($buscar) {
                            $sub->where('nombres', 'ILIKE', "%{$buscar}%")
                                ->orWhere('apellidos', 'ILIKE', "%{$buscar}%")
                                ->orWhere('ci', 'ILIKE', "%{$buscar}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/pagos/creditos/Index', [
            'creditos' => [
                'data' => $creditos->getCollection()->map(function (Credito $credito) {
                    return $this->serializeCredito($credito);
                })->values(),
                'pagination' => [
                    'current_page' => $creditos->currentPage(),
                    'last_page' => $creditos->lastPage(),
                    'per_page' => $creditos->perPage(),
                    'total' => $creditos->total(),
                    'prev_page_url' => $creditos->previousPageUrl(),
                    'next_page_url' => $creditos->nextPageUrl(),
                ],
            ],
            'request' => [
                'buscar' => $request->buscar,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/pagos/creditos/Create', [
            ...$this->formData(),
            'credito' => [
                'inscripcion_id' => null,
                'concepto_pago_id' => null,
                'tipo_pago' => 'CREDITO',
                'estado' => 'pendiente',
                'monto_total' => '',
                'saldo_pendiente' => '',
                'cantidad_cuotas' => '',
                'fecha_otorgamiento' => now()->format('Y-m-d'),
                'fecha_vencimiento' => '',
            ],
        ]);
    }

    public function store(CreditoRequest $request, CreditoCuotaService $cuotaService)
    {
        DB::transaction(function () use ($request, $cuotaService) {
            $data = $request->validated();

            $data['saldo_pendiente'] = $data['saldo_pendiente'] ?? $data['monto_total'];
            $data['estado'] = 'activo';

            $credito = Credito::create($data);
            $cuotaService->generarCuotas($credito);

            $alumno = $credito->inscripcion->alumnoDetalle->user;
            if ($alumno) {
                $alumno->notify(new CreditoHabilitado($credito));
            }
        });

        return redirect()
            ->route('admin.creditos.index')
            ->with('success', 'Crédito registrado correctamente y cuotas generadas automáticamente.');
    }

    public function show(Credito $credito)
    {
        $credito->load([
            'inscripcion.alumnoDetalle.user',
            'inscripcion.ofertaAcademica.carrera',
            'inscripcion.ofertaAcademica.periodoAcademico',
            'conceptoPago',
            'pagoCuotas',
        ]);

        return Inertia::render('admin/pagos/creditos/Show', [
            'credito' => $this->serializeCredito($credito),
        ]);
    }

    public function edit(Credito $credito)
    {
        $credito->load([
            'inscripcion.alumnoDetalle.user',
            'inscripcion.ofertaAcademica.carrera',
            'inscripcion.ofertaAcademica.periodoAcademico',
            'conceptoPago',
            'pagoCuotas',
        ]);

        return Inertia::render('admin/pagos/creditos/Edit', [
            ...$this->formData(),
            'credito' => $this->serializeCredito($credito),
        ]);
    }

    public function update(CreditoRequest $request, Credito $credito, CreditoCuotaService $cuotaService)
    {
        DB::transaction(function () use ($request, $credito, $cuotaService) {
            $data = $request->validated();

            $data['saldo_pendiente'] = $data['saldo_pendiente'] ?? $data['monto_total'];

            $debeRegenerar = (
                (float) $credito->monto_total !== (float) $data['monto_total'] ||
                (int) $credito->cantidad_cuotas !== (int) $data['cantidad_cuotas'] ||
                optional($credito->fecha_otorgamiento)->format('Y-m-d') !== $data['fecha_otorgamiento'] ||
                optional($credito->fecha_vencimiento)->format('Y-m-d') !== $data['fecha_vencimiento']
            );

            if ($debeRegenerar && ! $cuotaService->puedeRegenerarCuotas($credito)) {
                abort(422, 'No se pueden modificar monto, fechas o cantidad de cuotas porque ya existen cuotas pagadas.');
            }

            $credito->update($data);

            if ($debeRegenerar) {
                $cuotaService->generarCuotas($credito);
            }

            $credito->recalcularSaldo();
        });

        return redirect()
            ->route('admin.creditos.index')
            ->with('success', 'Crédito actualizado correctamente.');
    }

    public function destroy(Credito $credito)
    {
        $credito->update([
            'estado' => $credito->estado === 'anulado' ? 'activo' : 'anulado',
        ]);

        return redirect()
            ->route('admin.creditos.index')
            ->with('success', 'Estado del crédito actualizado correctamente.');
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
                ->get()
                ->map(function (Inscripcion $inscripcion) {
                    return [
                        'id' => $inscripcion->id,
                        'alumnoDetalle' => [
                            'user' => $inscripcion->alumnoDetalle?->user ? [
                                'nombres' => $inscripcion->alumnoDetalle->user->nombres,
                                'apellidos' => $inscripcion->alumnoDetalle->user->apellidos,
                                'ci' => $inscripcion->alumnoDetalle->user->ci,
                            ] : null,
                        ],
                        'ofertaAcademica' => [
                            'carrera' => [
                                'nombre' => $inscripcion->ofertaAcademica?->carrera?->nombre,
                            ],
                            'periodoAcademico' => [
                                'nombre' => $inscripcion->ofertaAcademica?->periodoAcademico?->nombre,
                                'gestion' => $inscripcion->ofertaAcademica?->periodoAcademico?->gestion,
                            ],
                        ],
                    ];
                })
                ->values(),

            'conceptos' => ConceptoPago::query()
                ->where('estado', 'Activo')
                ->orderBy('nombre')
                ->get()
                ->map(function (ConceptoPago $concepto) {
                    return [
                        'id' => $concepto->id,
                        'nombre' => $concepto->nombre,
                    ];
                })
                ->values(),
        ];
    }

    private function serializeCredito(Credito $credito): array
    {
        return [
            'id' => $credito->id,
            'inscripcion_id' => $credito->inscripcion_id,
            'concepto_pago_id' => $credito->concepto_pago_id,
            'tipo_pago' => $credito->tipo_pago,
            'monto_total' => $credito->monto_total,
            'saldo_pendiente' => $credito->saldo_pendiente,
            'cantidad_cuotas' => $credito->cantidad_cuotas,
            'estado' => $credito->estado,
            'fecha_otorgamiento' => $credito->fecha_otorgamiento?->format('Y-m-d'),
            'fecha_vencimiento' => $credito->fecha_vencimiento?->format('Y-m-d'),
            'inscripcion' => [
                'alumnoDetalle' => [
                    'user' => $credito->inscripcion?->alumnoDetalle?->user ? [
                        'nombres' => $credito->inscripcion->alumnoDetalle->user->nombres,
                        'apellidos' => $credito->inscripcion->alumnoDetalle->user->apellidos,
                        'ci' => $credito->inscripcion->alumnoDetalle->user->ci,
                    ] : null,
                ],
                'ofertaAcademica' => [
                    'carrera' => [
                        'nombre' => $credito->inscripcion?->ofertaAcademica?->carrera?->nombre,
                    ],
                    'periodoAcademico' => [
                        'nombre' => $credito->inscripcion?->ofertaAcademica?->periodoAcademico?->nombre,
                    ],
                ],
            ],
            'conceptoPago' => $credito->conceptoPago ? [
                'nombre' => $credito->conceptoPago->nombre,
            ] : null,
            'pagoCuotas' => $credito->pagoCuotas?->map(function ($cuota) {
                return [
                    'id' => $cuota->id,
                    'numero_cuota' => $cuota->numero_cuota,
                    'monto' => $cuota->monto,
                    'fecha_vencimiento' => $cuota->fecha_vencimiento?->format('Y-m-d'),
                    'estado' => $cuota->estado,
                ];
            })->values()->all() ?? [],
        ];
    }
}