<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\AlumnoDetalle;
use Inertia\Inertia;
use Inertia\Response;

class AlumnoHomeController extends Controller
{
    public function index(): Response
    {
        $alumno = AlumnoDetalle::with([
            'inscripciones.ofertaAcademica.carrera',
            'inscripciones.ofertaAcademica.periodoAcademico',
            'inscripciones.inscripcionMaterias',
        ])
            ->where('user_id', auth()->id())
            ->first();

        $resumenAcademico = [
            'carrera' => 'Sin carrera asignada',
            'periodo' => 'Sin período activo',
            'materias' => 0,
            'estado' => $alumno?->estado_academico ?? 'Activo',
        ];

        if ($alumno && $alumno->inscripciones->isNotEmpty()) {
            $inscripcionPrincipal = $alumno->inscripciones->first();

            $resumenAcademico = [
                'carrera' => $inscripcionPrincipal->ofertaAcademica?->carrera?->nombre
                    ?? $resumenAcademico['carrera'],
                'periodo' => $inscripcionPrincipal->ofertaAcademica?->periodoAcademico?->nombre
                    ?? $resumenAcademico['periodo'],
                'materias' => $alumno->inscripciones
                    ->flatMap(fn ($inscripcion) => $inscripcion->inscripcionMaterias)
                    ->count(),
                'estado' => $alumno->estado_academico ?? $resumenAcademico['estado'],
            ];
        }

        return Inertia::render('alumno/home', [
            'resumen_academico' => $resumenAcademico,
        ]);
    }
}