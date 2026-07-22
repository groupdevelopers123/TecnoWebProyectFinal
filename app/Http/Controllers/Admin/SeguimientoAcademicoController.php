<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SeguimientoAcademicoRequest;
use App\Models\DocenteDetalle;
use App\Models\InscripcionMateria;
use App\Models\SeguimientoAcademico;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeguimientoAcademicoController extends Controller
{
    public function index(Request $request)
    {
        $seguimientos = SeguimientoAcademico::query()
            ->with([
                'inscripcionMateria.inscripcion.alumnoDetalle.user',
                'inscripcionMateria.inscripcion.ofertaAcademica.carrera',
                'inscripcionMateria.inscripcion.ofertaAcademica.periodoAcademico',
                'inscripcionMateria.carreraMateria.materia',
                'docenteDetalle.user',
            ])
            ->when($request->buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('estado_academico', 'ILIKE', "%{$buscar}%")
                        ->orWhere('observacion', 'ILIKE', "%{$buscar}%")
                        ->orWhereHas('inscripcionMateria.inscripcion.alumnoDetalle.user', function ($sub) use ($buscar) {
                            $sub->where('nombres', 'ILIKE', "%{$buscar}%")
                                ->orWhere('apellidos', 'ILIKE', "%{$buscar}%")
                                ->orWhere('ci', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('inscripcionMateria.carreraMateria.materia', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%")
                                ->orWhere('codigo', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('inscripcionMateria.inscripcion.ofertaAcademica.carrera', function ($sub) use ($buscar) {
                            $sub->where('nombre', 'ILIKE', "%{$buscar}%")
                                ->orWhere('codigo', 'ILIKE', "%{$buscar}%");
                        })
                        ->orWhereHas('docenteDetalle.user', function ($sub) use ($buscar) {
                            $sub->where('nombres', 'ILIKE', "%{$buscar}%")
                                ->orWhere('apellidos', 'ILIKE', "%{$buscar}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax() && ! $request->header('X-Inertia')) {
            return response()->json([
                'data' => $seguimientos->getCollection()->map(function ($seguimiento) {
                    return [
                        'id' => $seguimiento->id,
                        'nota_final' => $seguimiento->nota_final,
                        'porcentaje_asistencia' => $seguimiento->porcentaje_asistencia,
                        'observacion' => $seguimiento->observacion,
                        'estado_academico' => $seguimiento->estado_academico,
                        'fecha_registro' => $seguimiento->fecha_registro?->format('Y-m-d'),

                        'alumno' => [
                            'nombres' => $seguimiento->inscripcionMateria?->inscripcion?->alumnoDetalle?->user?->nombres,
                            'apellidos' => $seguimiento->inscripcionMateria?->inscripcion?->alumnoDetalle?->user?->apellidos,
                            'ci' => $seguimiento->inscripcionMateria?->inscripcion?->alumnoDetalle?->user?->ci,
                        ],

                        'materia' => [
                            'codigo' => $seguimiento->inscripcionMateria?->carreraMateria?->materia?->codigo,
                            'nombre' => $seguimiento->inscripcionMateria?->carreraMateria?->materia?->nombre,
                        ],

                        'carrera' => [
                            'codigo' => $seguimiento->inscripcionMateria?->inscripcion?->ofertaAcademica?->carrera?->codigo,
                            'nombre' => $seguimiento->inscripcionMateria?->inscripcion?->ofertaAcademica?->carrera?->nombre,
                        ],

                        'docente' => [
                            'nombres' => $seguimiento->docenteDetalle?->user?->nombres,
                            'apellidos' => $seguimiento->docenteDetalle?->user?->apellidos,
                        ],
                    ];
                })->values(),

                'pagination' => [
                    'current_page' => $seguimientos->currentPage(),
                    'last_page' => $seguimientos->lastPage(),
                    'per_page' => $seguimientos->perPage(),
                    'total' => $seguimientos->total(),
                    'prev_page_url' => $seguimientos->previousPageUrl(),
                    'next_page_url' => $seguimientos->nextPageUrl(),
                ],
            ]);
        }

        return Inertia::render('admin/seguimientos-academicos/Index', [
            'seguimientos' => [
                'data' => $seguimientos->getCollection()->map(function ($seguimiento) {
                    return [
                        'id' => $seguimiento->id,
                        'nota_final' => $seguimiento->nota_final,
                        'porcentaje_asistencia' => $seguimiento->porcentaje_asistencia,
                        'observacion' => $seguimiento->observacion,
                        'estado_academico' => $seguimiento->estado_academico,
                        'fecha_registro' => $seguimiento->fecha_registro?->format('Y-m-d'),

                        'alumno' => [
                            'nombres' => $seguimiento->inscripcionMateria?->inscripcion?->alumnoDetalle?->user?->nombres,
                            'apellidos' => $seguimiento->inscripcionMateria?->inscripcion?->alumnoDetalle?->user?->apellidos,
                            'ci' => $seguimiento->inscripcionMateria?->inscripcion?->alumnoDetalle?->user?->ci,
                        ],

                        'materia' => [
                            'codigo' => $seguimiento->inscripcionMateria?->carreraMateria?->materia?->codigo,
                            'nombre' => $seguimiento->inscripcionMateria?->carreraMateria?->materia?->nombre,
                        ],

                        'carrera' => [
                            'codigo' => $seguimiento->inscripcionMateria?->inscripcion?->ofertaAcademica?->carrera?->codigo,
                            'nombre' => $seguimiento->inscripcionMateria?->inscripcion?->ofertaAcademica?->carrera?->nombre,
                        ],

                        'docente' => [
                            'nombres' => $seguimiento->docenteDetalle?->user?->nombres,
                            'apellidos' => $seguimiento->docenteDetalle?->user?->apellidos,
                        ],
                    ];
                })->values(),
                'pagination' => [
                    'current_page' => $seguimientos->currentPage(),
                    'last_page' => $seguimientos->lastPage(),
                    'per_page' => $seguimientos->perPage(),
                    'total' => $seguimientos->total(),
                    'prev_page_url' => $seguimientos->previousPageUrl(),
                    'next_page_url' => $seguimientos->nextPageUrl(),
                ],
            ],
            'request' => [
                'buscar' => $request->buscar,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/seguimientos-academicos/Create', [
            ...$this->formData(),
            'action' => route('admin.seguimientos-academicos.store'),
            'method' => 'post',
            'cancelUrl' => route('admin.seguimientos-academicos.index'),
            'seguimiento' => [],
        ]);
    }

    public function store(SeguimientoAcademicoRequest $request)
    {
        SeguimientoAcademico::create($request->validated());

        return redirect()
            ->route('admin.seguimientos-academicos.index')
            ->with('success', 'Seguimiento académico registrado correctamente.');
    }

    public function show(SeguimientoAcademico $seguimiento)
    {
        $seguimiento->load([
            'inscripcionMateria.inscripcion.alumnoDetalle.user',
            'inscripcionMateria.inscripcion.ofertaAcademica.carrera',
            'inscripcionMateria.inscripcion.ofertaAcademica.periodoAcademico',
            'inscripcionMateria.carreraMateria.materia',
            'docenteDetalle.user',
        ]);

        return Inertia::render('admin/seguimientos-academicos/Show', [
            'seguimiento' => $this->serializeSeguimiento($seguimiento),
        ]);
    }

    public function edit(SeguimientoAcademico $seguimiento)
    {
        return Inertia::render('admin/seguimientos-academicos/Edit', [
            ...$this->formData($seguimiento),
            'seguimiento' => $this->serializeSeguimiento($seguimiento),
            'action' => route('admin.seguimientos-academicos.update', $seguimiento),
            'method' => 'put',
            'cancelUrl' => route('admin.seguimientos-academicos.index'),
        ]);
    }

    public function update(SeguimientoAcademicoRequest $request, SeguimientoAcademico $seguimiento)
    {
        $seguimiento->update($request->validated());

        return redirect()
            ->route('admin.seguimientos-academicos.index')
            ->with('success', 'Seguimiento académico actualizado correctamente.');
    }

    public function destroy(SeguimientoAcademico $seguimiento)
    {
        $seguimiento->delete();

        return redirect()
            ->route('admin.seguimientos-academicos.index')
            ->with('success', 'Seguimiento académico eliminado correctamente.');
    }

    private function formData(?SeguimientoAcademico $seguimiento = null): array
    {
        return [
            'inscripcionMaterias' => InscripcionMateria::query()
                ->with([
                    'inscripcion.alumnoDetalle.user',
                    'inscripcion.ofertaAcademica.carrera',
                    'carreraMateria.materia',
                    'seguimientoAcademico',
                ])
                ->where(function ($query) use ($seguimiento) {
                    $query->whereDoesntHave('seguimientoAcademico');

                    if ($seguimiento) {
                        $query->orWhere('id', $seguimiento->inscripcion_materia_id);
                    }
                })
                ->orderByDesc('id')
                ->get()
                ->map(fn (InscripcionMateria $item) => [
                    'id' => $item->id,
                    'inscripcion_materia_id' => $item->id,
                    'inscripcion' => [
                        'alumnoDetalle' => [
                            'user' => $item->inscripcion?->alumnoDetalle?->user ? [
                                'nombres' => $item->inscripcion->alumnoDetalle->user->nombres,
                                'apellidos' => $item->inscripcion->alumnoDetalle->user->apellidos,
                            ] : null,
                        ],
                        'ofertaAcademica' => [
                            'carrera' => [
                                'nombre' => $item->inscripcion?->ofertaAcademica?->carrera?->nombre,
                            ],
                        ],
                    ],
                    'carreraMateria' => [
                        'materia' => [
                            'codigo' => $item->carreraMateria?->materia?->codigo,
                            'nombre' => $item->carreraMateria?->materia?->nombre,
                        ],
                    ],
                ])->values(),

            'docentes' => DocenteDetalle::query()
                ->with('user.role')
                ->whereHas('user', function ($query) {
                    $query->where('estado', true)
                        ->whereHas('role', function ($roleQuery) {
                            $roleQuery->where('nombre', 'docente');
                        });
                })
                ->orderBy('codigo')
                ->get()
                ->map(fn (DocenteDetalle $docente) => [
                    'id' => $docente->id,
                    'codigo' => $docente->codigo,
                    'especialidad' => $docente->especialidad,
                    'user' => $docente->user ? [
                        'nombres' => $docente->user->nombres,
                        'apellidos' => $docente->user->apellidos,
                    ] : null,
                ])->values(),
        ];
    }

    private function serializeSeguimiento(SeguimientoAcademico $seguimiento): array
    {
        return [
            'id' => $seguimiento->id,
            'inscripcion_materia_id' => $seguimiento->inscripcion_materia_id,
            'docente_detalle_id' => $seguimiento->docente_detalle_id,
            'nota_final' => $seguimiento->nota_final,
            'porcentaje_asistencia' => $seguimiento->porcentaje_asistencia,
            'estado_academico' => $seguimiento->estado_academico,
            'observacion' => $seguimiento->observacion,
            'fecha_registro' => $seguimiento->fecha_registro?->format('Y-m-d'),
            'inscripcionMateria' => [
                'inscripcion' => [
                    'alumnoDetalle' => [
                        'user' => $seguimiento->inscripcionMateria?->inscripcion?->alumnoDetalle?->user ? [
                            'nombres' => $seguimiento->inscripcionMateria->inscripcion->alumnoDetalle->user->nombres,
                            'apellidos' => $seguimiento->inscripcionMateria->inscripcion->alumnoDetalle->user->apellidos,
                        ] : null,
                    ],
                    'ofertaAcademica' => [
                        'carrera' => [
                            'codigo' => $seguimiento->inscripcionMateria?->inscripcion?->ofertaAcademica?->carrera?->codigo,
                            'nombre' => $seguimiento->inscripcionMateria?->inscripcion?->ofertaAcademica?->carrera?->nombre,
                        ],
                    ],
                ],
                'carreraMateria' => [
                    'materia' => [
                        'codigo' => $seguimiento->inscripcionMateria?->carreraMateria?->materia?->codigo,
                        'nombre' => $seguimiento->inscripcionMateria?->carreraMateria?->materia?->nombre,
                    ],
                ],
            ],
            'docenteDetalle' => [
                'user' => $seguimiento->docenteDetalle?->user ? [
                    'nombres' => $seguimiento->docenteDetalle->user->nombres,
                    'apellidos' => $seguimiento->docenteDetalle->user->apellidos,
                ] : null,
            ],
        ];
    }
}