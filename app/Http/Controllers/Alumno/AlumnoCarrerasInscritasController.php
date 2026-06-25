<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\AlumnoDetalle;
use Inertia\Inertia;
use Inertia\Response;

class AlumnoCarrerasInscritasController extends Controller
{
    public function index(): Response
    {
        $alumno = AlumnoDetalle::query()
            ->where('user_id', auth()->id())
            ->first();

        $carrerasInscritas = collect();

        if ($alumno) {
            $carrerasInscritas = $alumno->inscripciones()
                ->with([
                    'ofertaAcademica.carrera',
                    'ofertaAcademica.periodoAcademico',
                ])
                ->latest('fecha_inscripcion')
                ->get()
                ->groupBy(fn ($inscripcion) => $inscripcion->ofertaAcademica?->carrera_id ?? $inscripcion->oferta_academica_id)
                ->map(function ($inscripcionesCarrera) {
                    $primeraInscripcion = $inscripcionesCarrera->first();
                    $oferta = $primeraInscripcion?->ofertaAcademica;
                    $carrera = $oferta?->carrera;

                    return [
                        'carrera' => [
                            'id' => $carrera?->id,
                            'codigo' => $carrera?->codigo,
                            'nombre' => $carrera?->nombre,
                            'duracion' => $carrera?->duracion,
                            'regimen_academico' => $carrera?->regimen_academico,
                        ],
                        'ultima_inscripcion' => [
                            'id' => $primeraInscripcion?->id,
                            'fecha_inscripcion' => $primeraInscripcion?->fecha_inscripcion?->format('Y-m-d'),
                            'periodo' => $oferta?->periodoAcademico?->nombre,
                            'gestion' => $oferta?->periodoAcademico?->gestion,
                            'oferta' => $oferta?->nombre,
                            'oferta_id' => $oferta?->id,
                        ],
                        'total_inscripciones' => $inscripcionesCarrera->count(),
                        'ofertas' => $inscripcionesCarrera->map(function ($inscripcion) {
                            $oferta = $inscripcion->ofertaAcademica;

                            return [
                                'id' => $inscripcion->id,
                                'oferta_id' => $oferta?->id,
                                'nombre' => $oferta?->nombre,
                                'fecha_inscripcion' => $inscripcion->fecha_inscripcion?->format('Y-m-d'),
                                'periodo' => $oferta?->periodoAcademico?->nombre,
                                'gestion' => $oferta?->periodoAcademico?->gestion,
                                'estado_oferta' => (bool) ($oferta?->estado ?? false),
                                'cupos_disponibles' => (int) ($oferta?->cupos_disponibles ?? 0),
                            ];
                        })->values(),
                    ];
                })
                ->values();
        }

        return Inertia::render('alumno/carrerasInscritas', [
            'carrerasInscritas' => $carrerasInscritas,
            'totalCarreras' => $carrerasInscritas->count(),
        ]);
    }
}