<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\AlumnoDetalle;
use App\Models\InscripcionMateria;
use Inertia\Inertia;
use Inertia\Response;

class AlumnoMateriasInscritasController extends Controller
{
    public function index(): Response
    {
        $alumno = AlumnoDetalle::query()
            ->where('user_id', auth()->id())
            ->first();

        $carrerasConMaterias = collect();

        if ($alumno) {
            $carrerasConMaterias = $alumno->inscripciones()
                ->with([
                    'ofertaAcademica.carrera',
                    'ofertaAcademica.periodoAcademico',
                    'inscripcionMaterias.carreraMateria.materia',
                    'inscripcionMaterias.seguimientoAcademico',
                ])
                ->latest('fecha_inscripcion')
                ->get()
                ->groupBy(fn ($inscripcion) => $inscripcion->ofertaAcademica?->carrera_id ?? $inscripcion->oferta_academica_id)
                ->map(function ($inscripcionesCarrera) {
                    $primeraInscripcion = $inscripcionesCarrera->first();
                    $oferta = $primeraInscripcion?->ofertaAcademica;
                    $carrera = $oferta?->carrera;

                    $materias = $inscripcionesCarrera
                        ->flatMap(function ($inscripcion) use ($carrera) {
                            return $inscripcion->inscripcionMaterias->map(function ($inscripcionMateria) use ($inscripcion, $carrera) {
                                $carreraMateria = $inscripcionMateria->carreraMateria;
                                $materia = $carreraMateria?->materia;

                                $seguimientoAcad = $inscripcionMateria->seguimientoAcademico;

                                return [
                                    'id' => $inscripcionMateria->id,
                                    'periodo_numero' => (int) ($carreraMateria?->periodo_numero ?? 0),
                                    'estado' => $seguimientoAcad?->estado_academico ?? $inscripcionMateria->estado,
                                    'codigo' => $materia?->codigo,
                                    'nombre' => $materia?->nombre,
                                    'carga_horaria' => (int) ($materia?->carga_horaria ?? 0),
                                    'fecha_inscripcion' => $inscripcion->fecha_inscripcion?->format('Y-m-d'),
                                    'carrera_id' => $carrera?->id,
                                    'carrera_codigo' => $carrera?->codigo,
                                    'carrera_nombre' => $carrera?->nombre,
                                    'seguimiento' => $seguimientoAcad ? [
                                        'id' => $seguimientoAcad->id,
                                        'nota_final' => $seguimientoAcad->nota_final,
                                        'porcentaje_asistencia' => $seguimientoAcad->porcentaje_asistencia,
                                        'estado_academico' => $seguimientoAcad->estado_academico,
                                        'observacion' => $seguimientoAcad->observacion,
                                        'fecha_registro' => $seguimientoAcad->fecha_registro?->format('Y-m-d'),
                                    ] : null,
                                ];
                            });
                        })
                        ->values();

                    return [
                        'carrera' => [
                            'id' => $carrera?->id,
                            'codigo' => $carrera?->codigo,
                            'nombre' => $carrera?->nombre,
                            'duracion' => $carrera?->duracion,
                            'regimen_academico' => $carrera?->regimen_academico,
                        ],
                        'ultima_inscripcion' => [
                            'fecha_inscripcion' => $primeraInscripcion?->fecha_inscripcion?->format('Y-m-d'),
                            'periodo' => $oferta?->periodoAcademico?->nombre,
                            'gestion' => $oferta?->periodoAcademico?->gestion,
                            'oferta' => $oferta?->nombre,
                        ],
                        'total_materias' => $materias->count(),
                        'total_periodos' => $materias->pluck('periodo_numero')->filter()->unique()->count(),
                        'materias' => $materias,
                    ];
                })
                ->values();
        }

        return Inertia::render('alumno/materiasInscritas', [
            'carrerasConMaterias' => $carrerasConMaterias,
            'totalCarreras' => $carrerasConMaterias->count(),
            'totalMaterias' => $carrerasConMaterias->sum('total_materias'),
        ]);
    }

    public function horario(): Response
    {
        $alumno = AlumnoDetalle::query()
            ->where('user_id', auth()->id())
            ->first();

        $inscripcionMaterias = collect();

        if ($alumno) {
            $inscripcionMaterias = $alumno->inscripciones()
                ->with([
                    'inscripcionMaterias.carreraMateria.materia',
                    'inscripcionMaterias.carreraMateria.horarios.aula',
                    'ofertaAcademica.carrera',
                ])
                ->get()
                ->pluck('inscripcionMaterias')
                ->flatten();
        }

        $horariosAgrupados = $inscripcionMaterias
            ->groupBy(fn ($inscripcionMateria) => $inscripcionMateria->carreraMateria->materia->id)
            ->map(fn ($materiaGroup) => [
                'materia_id' => $materiaGroup->first()->carreraMateria->materia->id,
                'codigo' => $materiaGroup->first()->carreraMateria->materia->codigo,
                'nombre' => $materiaGroup->first()->carreraMateria->materia->nombre,
                'carreras' => $materiaGroup
                    ->pluck('carreraMateria.carrera.nombre')
                    ->unique()
                    ->values(),
                'horarios' => $materiaGroup
                    ->flatMap(fn ($inscripcionMateria) => $inscripcionMateria->carreraMateria->horarios)
                    ->map(fn ($horario) => [
                        'dia' => $horario->dia,
                        'hora_inicio' => substr($horario->hora_inicio, 0, 5),
                        'hora_fin' => substr($horario->hora_fin, 0, 5),
                        'aula' => $horario->aula?->nombre ?? 'Sin aula',
                        'estado' => $horario->estado ? 'Activo' : 'Inactivo',
                    ])
                    ->values(),
            ])
            ->values();

        return Inertia::render('alumno/horario', [
            'horarios' => $horariosAgrupados,
        ]);
    }

    public function showSeguimiento(InscripcionMateria $inscripcionMateria): Response
    {
        $inscripcionMateria->load([
            'carreraMateria.materia',
            'seguimientoAcademico',
            'inscripcion.ofertaAcademica.carrera',
            'inscripcion.ofertaAcademica.periodoAcademico',
        ]);

        if ($inscripcionMateria->inscripcion?->alumnoDetalle?->user_id !== auth()->id()) {
            abort(403);
        }

        $seguimientoAcad = $inscripcionMateria->seguimientoAcademico;

        $materia = [
            'id' => $inscripcionMateria->id,
            'codigo' => $inscripcionMateria->carreraMateria?->materia?->codigo,
            'nombre' => $inscripcionMateria->carreraMateria?->materia?->nombre,
            'carga_horaria' => (int) ($inscripcionMateria->carreraMateria?->materia?->carga_horaria ?? 0),
            'periodo_numero' => (int) ($inscripcionMateria->carreraMateria?->periodo_numero ?? 0),
            'estado' => $seguimientoAcad?->estado_academico ?? $inscripcionMateria->estado,
            'fecha_inscripcion' => $inscripcionMateria->inscripcion?->fecha_inscripcion?->format('Y-m-d'),
            'carrera' => [
                'id' => $inscripcionMateria->inscripcion?->ofertaAcademica?->carrera?->id,
                'codigo' => $inscripcionMateria->inscripcion?->ofertaAcademica?->carrera?->codigo,
                'nombre' => $inscripcionMateria->inscripcion?->ofertaAcademica?->carrera?->nombre,
            ],
            'inscripcion' => [
                'oferta' => $inscripcionMateria->inscripcion?->ofertaAcademica?->nombre,
                'periodo' => $inscripcionMateria->inscripcion?->ofertaAcademica?->periodoAcademico?->nombre,
                'gestion' => $inscripcionMateria->inscripcion?->ofertaAcademica?->periodoAcademico?->gestion,
            ],
            'seguimiento' => $seguimientoAcad ? [
                'id' => $seguimientoAcad->id,
                'nota_final' => $seguimientoAcad->nota_final,
                'porcentaje_asistencia' => $seguimientoAcad->porcentaje_asistencia,
                'estado_academico' => $seguimientoAcad->estado_academico,
                'observacion' => $seguimientoAcad->observacion,
                'fecha_registro' => $seguimientoAcad->fecha_registro?->format('Y-m-d'),
            ] : null,
        ];

        return Inertia::render('alumno/materiaSeguimiento', [
            'materia' => $materia,
        ]);
    }
}