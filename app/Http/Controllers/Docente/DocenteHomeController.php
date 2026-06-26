<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use Inertia\Inertia;
use Inertia\Response;

class DocenteHomeController extends Controller
{
    public function index(): Response
    {
        $usuario = auth()->user();
        $docenteDetalle = $usuario?->docenteDetalle;

        $materiasCount = $docenteDetalle ? $docenteDetalle->materias()->count() : 0;
        $alumnosCount = 0;

        if ($docenteDetalle) {
            $alumnosCount = Inscripcion::query()
                ->whereHas('ofertaAcademica', function ($query) use ($docenteDetalle) {
                    $query->where('docente_detalle_id', $docenteDetalle->id);
                })
                ->distinct('alumno_detalle_id')
                ->count('alumno_detalle_id');
        }

        return Inertia::render('docente/home', [
            'estadisticas' => [
                'materias' => $materiasCount,
                'alumnos' => $alumnosCount,
                'notificaciones' => $usuario?->unreadNotifications()->count() ?? 0,
            ],
        ]);
    }
}