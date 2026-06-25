<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SeguimientoAcademicoRequest;
use App\Models\DocenteDetalle;
use App\Models\InscripcionMateria;
use App\Models\SeguimientoAcademico;
use Illuminate\Http\Request;

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

        if ($request->ajax()) {
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

        return view('admin.seguimientos-academicos.index', compact('seguimientos'));
    }

    public function create()
    {
        return view('admin.seguimientos-academicos.create', $this->formData());
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

        return view('admin.seguimientos-academicos.show', compact('seguimiento'));
    }

    public function edit(SeguimientoAcademico $seguimiento)
    {
        return view('admin.seguimientos-academicos.edit', [
            ...$this->formData($seguimiento),
            'seguimiento' => $seguimiento,
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
                ->get(),

            'docentes' => DocenteDetalle::query()
                ->with('user.role')
                ->whereHas('user', function ($query) {
                    $query->where('estado', true)
                        ->whereHas('role', function ($roleQuery) {
                            $roleQuery->where('nombre', 'docente');
                        });
                })
                ->orderBy('codigo')
                ->get(),
        ];
    }
}