<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InscripcionRequest;
use App\Models\AlumnoDetalle;
use App\Models\Inscripcion;
use App\Models\OfertaAcademica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CarreraMateria;
use Illuminate\Database\QueryException;
use Inertia\Inertia;

class InscripcionController extends Controller
{
    private function inscripcionPayload(Inscripcion $inscripcion): array
    {
        $inscripcion->loadMissing([
            'alumnoDetalle.user',
            'ofertaAcademica.carrera',
            'ofertaAcademica.periodoAcademico',
            'usuarioRegistro',
            'inscripcionMaterias.carreraMateria.materia',
            'inscripcionMaterias.carreraMateria.carrera',
        ]);

        return [
            'id' => $inscripcion->id,
            'alumno_detalle_id' => $inscripcion->alumno_detalle_id,
            'oferta_academica_id' => $inscripcion->oferta_academica_id,
            'user_id_registro' => $inscripcion->user_id_registro,
            'periodo_numero' => $inscripcion->periodo_numero,
            'fecha_inscripcion' => $inscripcion->fecha_inscripcion?->format('Y-m-d'),
            'observacion' => $inscripcion->observacion,
            'alumno' => [
                'id' => $inscripcion->alumnoDetalle?->id,
                'codigo' => $inscripcion->alumnoDetalle?->codigo,
                'nombres' => $inscripcion->alumnoDetalle?->user?->nombres,
                'apellidos' => $inscripcion->alumnoDetalle?->user?->apellidos,
                'ci' => $inscripcion->alumnoDetalle?->user?->ci,
            ],
            'oferta_academica' => [
                'id' => $inscripcion->ofertaAcademica?->id,
                'nombre' => $inscripcion->ofertaAcademica?->nombre,
                'carrera_id' => $inscripcion->ofertaAcademica?->carrera_id,
                'carrera' => [
                    'codigo' => $inscripcion->ofertaAcademica?->carrera?->codigo,
                    'nombre' => $inscripcion->ofertaAcademica?->carrera?->nombre,
                ],
                'periodo_academico' => [
                    'nombre' => $inscripcion->ofertaAcademica?->periodoAcademico?->nombre,
                    'gestion' => $inscripcion->ofertaAcademica?->periodoAcademico?->gestion,
                ],
            ],
            'usuario_registro' => [
                'nombres' => $inscripcion->usuarioRegistro?->nombres,
                'apellidos' => $inscripcion->usuarioRegistro?->apellidos,
            ],
            'inscripcion_materias' => $inscripcion->inscripcionMaterias->map(function ($detalle) {
                return [
                    'id' => $detalle->id,
                    'estado' => $detalle->estado,
                    'carrera_materia_id' => $detalle->carrera_materia_id,
                    'materia' => [
                        'codigo' => $detalle->carreraMateria?->materia?->codigo,
                        'nombre' => $detalle->carreraMateria?->materia?->nombre,
                        'carga_horaria' => $detalle->carreraMateria?->materia?->carga_horaria,
                    ],
                    'periodo_numero' => $detalle->carreraMateria?->periodo_numero,
                ];
            })->values(),
            'materias_disponibles' => CarreraMateria::query()
                ->with(['materia', 'carrera'])
                ->where('estado', true)
                ->where('carrera_id', $inscripcion->ofertaAcademica?->carrera_id)
                ->whereNotIn('id', $inscripcion->inscripcionMaterias->pluck('carrera_materia_id'))
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'periodo_numero' => $item->periodo_numero,
                        'materia' => [
                            'codigo' => $item->materia?->codigo,
                            'nombre' => $item->materia?->nombre,
                            'carga_horaria' => $item->materia?->carga_horaria,
                        ],
                    ];
                })->values(),
        ];
    }

    private function formData(?Inscripcion $inscripcion = null): array
    {
        return [
            'alumnos' => AlumnoDetalle::query()
                ->with('user.role')
                ->whereHas('user', function ($query) {
                    $query->where('estado', true)
                        ->whereHas('role', function ($roleQuery) {
                            $roleQuery->where('nombre', 'alumno');
                        });
                })
                ->orderBy('codigo')
                ->get()
                ->map(function ($alumno) {
                    return [
                        'id' => $alumno->id,
                        'codigo' => $alumno->codigo,
                        'ci' => $alumno->user?->ci,
                        'nombre_completo' => $alumno->user?->nombreCompleto(),
                    ];
                })
                ->values(),

            'ofertas' => OfertaAcademica::query()
                ->with(['carrera', 'periodoAcademico'])
                ->where('estado', true)
                ->where(function ($query) use ($inscripcion) {
                    $query->where('cupos_disponibles', '>', 0);

                    if ($inscripcion) {
                        $query->orWhere('id', $inscripcion->oferta_academica_id);
                    }
                })
                ->orderByDesc('fecha_inicio')
                ->get()
                ->map(function ($oferta) {
                    return [
                        'id' => $oferta->id,
                        'nombre' => $oferta->nombre,
                        'cantidad_cupos' => $oferta->cantidad_cupos,
                        'cupos_disponibles' => $oferta->cupos_disponibles,
                        'carrera' => [
                            'codigo' => $oferta->carrera?->codigo,
                            'nombre' => $oferta->carrera?->nombre,
                        ],
                        'periodo_academico' => [
                            'nombre' => $oferta->periodoAcademico?->nombre,
                            'gestion' => $oferta->periodoAcademico?->gestion,
                        ],
                    ];
                })
                ->values(),
        ];
    }

    public function index(Request $request)
    {
        $inscripciones = Inscripcion::query()
            ->with([
                'alumnoDetalle.user',
                'ofertaAcademica.carrera',
                'ofertaAcademica.periodoAcademico',
                'usuarioRegistro',
                'inscripcionMaterias.carreraMateria.materia',
                'inscripcionMaterias.carreraMateria.carrera',
            ])
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('observacion', 'ILIKE', "%{$buscar}%")
                        ->orWhereHas('alumnoDetalle.user', function ($sub) use ($buscar) {
                            $sub->where('nombres', 'ILIKE', "%{$buscar}%")
                                ->orWhere('apellidos', 'ILIKE', "%{$buscar}%")
                                ->orWhere('ci', 'ILIKE', "%{$buscar}%")
                                ->orWhere('email', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('ofertaAcademica', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('ofertaAcademica.carrera', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%")
                                ->orWhere('codigo', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('ofertaAcademica.periodoAcademico', function ($sub) use ($buscar) {
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
                'data' => $inscripciones->getCollection()->map(function ($inscripcion) {
                    return [
                        'id' => $inscripcion->id,
                        'periodo_numero' => $inscripcion->periodo_numero,
                        'fecha_inscripcion' => $inscripcion->fecha_inscripcion?->format('Y-m-d'),
                        'observacion' => $inscripcion->observacion,

                        'alumno' => [
                            'id' => $inscripcion->alumnoDetalle?->id,
                            'codigo' => $inscripcion->alumnoDetalle?->codigo,
                            'nombres' => $inscripcion->alumnoDetalle?->user?->nombres,
                            'apellidos' => $inscripcion->alumnoDetalle?->user?->apellidos,
                            'ci' => $inscripcion->alumnoDetalle?->user?->ci,
                        ],

                        'oferta_academica' => [
                            'id' => $inscripcion->ofertaAcademica?->id,
                            'nombre' => $inscripcion->ofertaAcademica?->nombre,
                            'carrera_id' => $inscripcion->ofertaAcademica?->carrera_id,
                            'carrera' => [
                                'codigo' => $inscripcion->ofertaAcademica?->carrera?->codigo,
                                'nombre' => $inscripcion->ofertaAcademica?->carrera?->nombre,
                            ],
                            'periodo_academico' => [
                                'nombre' => $inscripcion->ofertaAcademica?->periodoAcademico?->nombre,
                                'gestion' => $inscripcion->ofertaAcademica?->periodoAcademico?->gestion,
                            ],
                        ],

                        'usuario_registro' => [
                            'nombres' => $inscripcion->usuarioRegistro?->nombres,
                            'apellidos' => $inscripcion->usuarioRegistro?->apellidos,
                        ],

                        'inscripcion_materias' => $inscripcion->inscripcionMaterias->map(function ($detalle) {
                            return [
                                'id' => $detalle->id,
                                'estado' => $detalle->estado,
                                'carrera_materia_id' => $detalle->carrera_materia_id,
                                'materia' => [
                                    'codigo' => $detalle->carreraMateria?->materia?->codigo,
                                    'nombre' => $detalle->carreraMateria?->materia?->nombre,
                                    'carga_horaria' => $detalle->carreraMateria?->materia?->carga_horaria,
                                ],
                                'periodo_numero' => $detalle->carreraMateria?->periodo_numero,
                            ];
                        })->values(),

                        'materias_disponibles' => CarreraMateria::query()
                            ->with(['materia', 'carrera'])
                            ->where('estado', true)
                            ->where('carrera_id', $inscripcion->ofertaAcademica?->carrera_id)
                            ->whereNotIn('id', $inscripcion->inscripcionMaterias->pluck('carrera_materia_id'))
                            ->get()
                            ->map(function ($item) {
                                return [
                                    'id' => $item->id,
                                    'periodo_numero' => $item->periodo_numero,
                                    'materia' => [
                                        'codigo' => $item->materia?->codigo,
                                        'nombre' => $item->materia?->nombre,
                                        'carga_horaria' => $item->materia?->carga_horaria,
                                    ],
                                ];
                            })->values(),
                    ];
                })->values(),

                'pagination' => [
                    'current_page' => $inscripciones->currentPage(),
                    'last_page' => $inscripciones->lastPage(),
                    'per_page' => $inscripciones->perPage(),
                    'total' => $inscripciones->total(),
                    'prev_page_url' => $inscripciones->previousPageUrl(),
                    'next_page_url' => $inscripciones->nextPageUrl(),
                ],
            ]);
        }

        return Inertia::render('admin/inscripciones/Index', [
            'inscripciones' => [
                'data' => $inscripciones->getCollection()->map(fn ($inscripcion) => $this->inscripcionPayload($inscripcion))->values(),
                'pagination' => [
                    'current_page' => $inscripciones->currentPage(),
                    'last_page' => $inscripciones->lastPage(),
                    'per_page' => $inscripciones->perPage(),
                    'total' => $inscripciones->total(),
                    'prev_page_url' => $inscripciones->previousPageUrl(),
                    'next_page_url' => $inscripciones->nextPageUrl(),
                ],
            ],
            'request' => [
                'buscar' => $request->buscar,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/inscripciones/Create', [
            ...$this->formData(),
            'inscripcion' => [],
            'action' => route('admin.inscripciones.store'),
            'cancelUrl' => route('admin.inscripciones.index'),
        ]);
    }

    public function store(InscripcionRequest $request)
    {
        DB::transaction(function () use ($request) {
            $oferta = OfertaAcademica::lockForUpdate()
                ->findOrFail($request->oferta_academica_id);

            Inscripcion::create([
                ...$request->validated(),
                'user_id_registro' => auth()->id(),
            ]);

            $oferta->decrement('cupos_disponibles');
        });

        return redirect()
            ->route('admin.inscripciones.index')
            ->with('success', 'Inscripción registrada correctamente.');
    }

    public function show(Inscripcion $inscripcion)
    {
        return Inertia::render('admin/inscripciones/Show', [
            'inscripcion' => $this->inscripcionPayload($inscripcion),
        ]);
    }

    public function edit(Inscripcion $inscripcion)
    {
        return Inertia::render('admin/inscripciones/Edit', [
            ...$this->formData($inscripcion),
            'inscripcion' => $this->inscripcionPayload($inscripcion),
            'action' => route('admin.inscripciones.update', $inscripcion),
            'cancelUrl' => route('admin.inscripciones.index'),
        ]);
    }

    public function update(InscripcionRequest $request, Inscripcion $inscripcion)
    {
        DB::transaction(function () use ($request, $inscripcion) {
            $ofertaAnteriorId = $inscripcion->oferta_academica_id;
            $ofertaNuevaId = (int) $request->oferta_academica_id;

            if ((int) $ofertaAnteriorId !== $ofertaNuevaId) {
                $ofertaAnterior = OfertaAcademica::lockForUpdate()
                    ->findOrFail($ofertaAnteriorId);

                $ofertaNueva = OfertaAcademica::lockForUpdate()
                    ->findOrFail($ofertaNuevaId);

                $ofertaAnterior->increment('cupos_disponibles');
                $ofertaNueva->decrement('cupos_disponibles');
            }

            $inscripcion->update([
                ...$request->validated(),
                'user_id_registro' => $inscripcion->user_id_registro,
            ]);
        });

        return redirect()
            ->route('admin.inscripciones.index')
            ->with('success', 'Inscripción actualizada correctamente.');
    }

    public function destroy(Inscripcion $inscripcion)
    {
        try {
            DB::transaction(function () use ($inscripcion) {
                $oferta = OfertaAcademica::lockForUpdate()
                    ->findOrFail($inscripcion->oferta_academica_id);

                $inscripcion->delete();

                if ($oferta->cupos_disponibles < $oferta->cantidad_cupos) {
                    $oferta->increment('cupos_disponibles');
                }
            });

            return redirect()
                ->route('admin.inscripciones.index')
                ->with('success', 'Inscripción eliminada correctamente.');
        } catch (QueryException $e) {
            // Detectar posible restricción de integridad referencial (pagos asociados u otros)
            $message = 'No se puede eliminar la inscripción porque existen registros relacionados (por ejemplo, pagos).';

            // Loguear la excepción para debugging (opcional)
            report($e);

            return redirect()
                ->route('admin.inscripciones.index')
                ->with('error', $message);
        } catch (\Exception $e) {
            report($e);

            return redirect()
                ->route('admin.inscripciones.index')
                ->with('error', 'Ocurrió un error al intentar eliminar la inscripción.');
        }
    }
}