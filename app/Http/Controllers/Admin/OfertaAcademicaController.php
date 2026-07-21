<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OfertaAcademicaRequest;
use App\Models\Carrera;
use App\Models\DocenteDetalle;
use App\Models\OfertaAcademica;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OfertaAcademicaController extends Controller
{
    private function ofertaPayload(OfertaAcademica $oferta): array
    {
        $oferta->loadMissing(['carrera', 'periodoAcademico', 'docenteDetalle.user']);

        return [
            'id' => $oferta->id,
            'carrera_id' => $oferta->carrera_id,
            'periodo_academico_id' => $oferta->periodo_academico_id,
            'docente_detalle_id' => $oferta->docente_detalle_id,
            'nombre' => $oferta->nombre,
            'cantidad_cupos' => $oferta->cantidad_cupos,
            'cupos_disponibles' => $oferta->cupos_disponibles,
            'fecha_inicio' => $oferta->fecha_inicio?->format('Y-m-d'),
            'fecha_fin' => $oferta->fecha_fin?->format('Y-m-d'),
            'precio_matricula' => $oferta->precio_matricula,
            'precio_mensualidad' => $oferta->precio_mensualidad,
            'precio_carrera_completa' => $oferta->precio_carrera_completa,
            'estado' => (bool) $oferta->estado,
            'carrera' => [
                'id' => $oferta->carrera?->id,
                'codigo' => $oferta->carrera?->codigo,
                'nombre' => $oferta->carrera?->nombre,
            ],
            'periodo_academico' => [
                'id' => $oferta->periodoAcademico?->id,
                'nombre' => $oferta->periodoAcademico?->nombre,
                'gestion' => $oferta->periodoAcademico?->gestion,
            ],
            'docente_detalle' => $oferta->docenteDetalle ? [
                'id' => $oferta->docenteDetalle->id,
                'codigo' => $oferta->docenteDetalle->codigo,
                'especialidad' => $oferta->docenteDetalle->especialidad,
                'user' => [
                    'nombres' => $oferta->docenteDetalle->user?->nombres,
                    'apellidos' => $oferta->docenteDetalle->user?->apellidos,
                ],
            ] : null,
        ];
    }

    public function index(Request $request)
    {
        $ofertas = OfertaAcademica::query()
            ->with(['carrera', 'periodoAcademico', 'docenteDetalle.user'])
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'ILIKE', "%{$buscar}%")
                        ->orWhereHas('carrera', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%")
                                ->orWhere('codigo', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('periodoAcademico', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%")
                                ->orWhere('gestion', 'ILIKE', "%{$buscar}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax() && ! $request->header('X-Inertia')) {
            return response()->json([
                'data' => $ofertas->getCollection()->map(function ($oferta) {
                    return [
                        'id' => $oferta->id,
                        'nombre' => $oferta->nombre,
                        'cantidad_cupos' => $oferta->cantidad_cupos,
                        'cupos_disponibles' => $oferta->cupos_disponibles,
                        'fecha_inicio' => $oferta->fecha_inicio?->format('Y-m-d'),
                        'fecha_fin' => $oferta->fecha_fin?->format('Y-m-d'),

                        'precio_matricula' => $oferta->precio_matricula,
                        'precio_mensualidad' => $oferta->precio_mensualidad,
                        'precio_carrera_completa' => $oferta->precio_carrera_completa,

                        'estado' => (bool) $oferta->estado,

                        'carrera' => [
                            'id' => $oferta->carrera?->id,
                            'codigo' => $oferta->carrera?->codigo,
                            'nombre' => $oferta->carrera?->nombre,
                        ],

                        'periodo_academico' => [
                            'id' => $oferta->periodoAcademico?->id,
                            'nombre' => $oferta->periodoAcademico?->nombre,
                            'gestion' => $oferta->periodoAcademico?->gestion,
                        ],

                        'docente_detalle' => $oferta->docenteDetalle ? [
                            'id' => $oferta->docenteDetalle->id,
                            'codigo' => $oferta->docenteDetalle->codigo,
                            'especialidad' => $oferta->docenteDetalle->especialidad,
                            'user' => [
                                'nombres' => $oferta->docenteDetalle->user?->nombres,
                                'apellidos' => $oferta->docenteDetalle->user?->apellidos,
                            ],
                        ] : null,
                    ];
                })->values(),

                'pagination' => [
                    'current_page' => $ofertas->currentPage(),
                    'last_page' => $ofertas->lastPage(),
                    'per_page' => $ofertas->perPage(),
                    'total' => $ofertas->total(),
                    'prev_page_url' => $ofertas->previousPageUrl(),
                    'next_page_url' => $ofertas->nextPageUrl(),
                ],
            ]);
        }

        return Inertia::render('admin/ofertas-academicas/Index', [
            'ofertas' => [
                'data' => $ofertas->getCollection()->map(fn ($oferta) => $this->ofertaPayload($oferta))->values(),
                'pagination' => [
                    'current_page' => $ofertas->currentPage(),
                    'last_page' => $ofertas->lastPage(),
                    'per_page' => $ofertas->perPage(),
                    'total' => $ofertas->total(),
                    'prev_page_url' => $ofertas->previousPageUrl(),
                    'next_page_url' => $ofertas->nextPageUrl(),
                ],
            ],
            'request' => [
                'buscar' => $request->buscar,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/ofertas-academicas/Create', [
            ...$this->formData(),
            'oferta' => [],
            'action' => route('admin.ofertas-academicas.store'),
            'cancelUrl' => route('admin.ofertas-academicas.index'),
        ]);
    }

    public function store(OfertaAcademicaRequest $request)
    {
        OfertaAcademica::create($request->validated());

        return redirect()
            ->route('admin.ofertas-academicas.index')
            ->with('success', 'Oferta académica registrada correctamente.');
    }

    public function show(OfertaAcademica $ofertas_academica)
    {
        return Inertia::render('admin/ofertas-academicas/Show', [
            'oferta' => $this->ofertaPayload($ofertas_academica),
        ]);
    }

    public function edit(OfertaAcademica $ofertas_academica)
    {
        return Inertia::render('admin/ofertas-academicas/Edit', [
            ...$this->formData(),
            'oferta' => $this->ofertaPayload($ofertas_academica),
            'action' => route('admin.ofertas-academicas.update', $ofertas_academica),
            'cancelUrl' => route('admin.ofertas-academicas.index'),
        ]);
    }

    public function update(
        OfertaAcademicaRequest $request,
        OfertaAcademica $ofertas_academica
    ) {
        $ofertas_academica->update($request->validated());

        return redirect()
            ->route('admin.ofertas-academicas.index')
            ->with('success', 'Oferta académica actualizada correctamente.');
    }

    public function destroy(OfertaAcademica $ofertas_academica)
    {
        $ofertas_academica->update([
            'estado' => ! $ofertas_academica->estado,
        ]);

        return redirect()
            ->route('admin.ofertas-academicas.index')
            ->with(
                'success',
                'Estado de la oferta académica actualizado correctamente.'
            );
    }

    private function formData(): array
    {
        return [
            'carreras' => Carrera::query()
                ->where('estado', true)
                ->orderBy('nombre')
                ->get()
                ->map(function ($carrera) {
                    return [
                        'id' => $carrera->id,
                        'codigo' => $carrera->codigo,
                        'nombre' => $carrera->nombre,
                    ];
                })
                ->values(),

            'periodos' => PeriodoAcademico::query()
                ->where('estado', true)
                ->orderByDesc('gestion')
                ->orderBy('nombre')
                ->get()
                ->map(function ($periodo) {
                    return [
                        'id' => $periodo->id,
                        'nombre' => $periodo->nombre,
                        'gestion' => $periodo->gestion,
                    ];
                })
                ->values(),

            'docentes' => DocenteDetalle::query()
                ->with('user')
                ->whereHas('user', function ($query) {
                    $query->where('estado', true);
                })
                ->orderBy('codigo')
                ->get()
                ->map(function ($docente) {
                    return [
                        'id' => $docente->id,
                        'codigo' => $docente->codigo,
                        'especialidad' => $docente->especialidad,
                        'user' => [
                            'nombres' => $docente->user?->nombres,
                            'apellidos' => $docente->user?->apellidos,
                        ],
                    ];
                })
                ->values(),
        ];
    }
}