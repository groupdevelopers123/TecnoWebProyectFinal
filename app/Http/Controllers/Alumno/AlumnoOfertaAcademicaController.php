<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\OfertaAcademica;
use Inertia\Inertia;
use Inertia\Response;

class AlumnoOfertaAcademicaController extends Controller
{
    public function index(): Response
    {
        $alumnoDetalle = auth()->user()->alumnoDetalle;
        $inscripciones = $alumnoDetalle
            ? $alumnoDetalle->inscripciones()
                ->pluck('oferta_academica_id')
                ->toArray()
            : [];

        $ofertas = OfertaAcademica::query()
            ->with([
                'carrera',
                'periodoAcademico',
            ])
            ->where('estado', true)
            ->latest()
            ->get()
            ->map(function (OfertaAcademica $oferta) use ($inscripciones) {
                return [
                    'id' => $oferta->id,
                    'nombre' => $oferta->nombre,

                    'cantidad_cupos' => (int) ($oferta->cantidad_cupos ?? 0),
                    'cupos_disponibles' => (int) ($oferta->cupos_disponibles ?? 0),

                    'fecha_inicio' => $oferta->fecha_inicio
                        ?->format('Y-m-d'),

                    'fecha_fin' => $oferta->fecha_fin
                        ?->format('Y-m-d'),

                    'precio_matricula' => (float) (
                        $oferta->precio_matricula ?? 0
                    ),

                    'precio_mensualidad' => (float) (
                        $oferta->precio_mensualidad ?? 0
                    ),

                    'precio_carrera_completa' => (float) (
                        $oferta->precio_carrera_completa ?? 0
                    ),

                    'estado' => (bool) $oferta->estado,
                    'inscrito' => in_array($oferta->id, $inscripciones, true),

                    'carrera' => [
                        'id' => $oferta->carrera?->id,
                        'codigo' => $oferta->carrera?->codigo,
                        'nombre' => $oferta->carrera?->nombre,

                        'regimen_academico' =>
                            $oferta->carrera?->regimen_academico,

                        'duracion' =>
                            $oferta->carrera?->duracion,
                    ],

                    'periodo_academico' => [
                        'id' => $oferta->periodoAcademico?->id,
                        'nombre' => $oferta->periodoAcademico?->nombre,
                        'gestion' => $oferta->periodoAcademico?->gestion,
                    ],

                    'docente' => [
                        'nombre_completo' => null,
                    ],
                ];
            })
            ->values();

        return Inertia::render('alumno/ofertaAcademica', [
            'ofertas' => $ofertas,
        ]);
    }
}