<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Inertia\Inertia;

class BitacoraController extends Controller
{
    public function index()
    {
        $controlDeAcceso = [
            'Dashboard administrativo' => ['propietario' => true, 'secretaria' => true, 'docente' => false, 'alumno' => false],
            'Gestión de usuarios' => ['propietario' => true, 'secretaria' => true, 'docente' => false, 'alumno' => false],
            'Gestión de aulas' => ['propietario' => true, 'secretaria' => true, 'docente' => false, 'alumno' => false],
            'Gestión de carreras y materias' => ['propietario' => true, 'secretaria' => true, 'docente' => false, 'alumno' => false],
            'Gestión de periodos' => ['propietario' => true, 'secretaria' => true, 'docente' => false, 'alumno' => false],
            'Gestión de horarios' => ['propietario' => true, 'secretaria' => true, 'docente' => false, 'alumno' => false],
            'Ofertas académicas' => ['propietario' => true, 'secretaria' => true, 'docente' => false, 'alumno' => false],
            'Inscripciones' => ['propietario' => true, 'secretaria' => true, 'docente' => false, 'alumno' => false],
            'Seguimiento académico' => ['propietario' => true, 'secretaria' => true, 'docente' => true, 'alumno' => false],
            'Pagos' => ['propietario' => true, 'secretaria' => true, 'docente' => false, 'alumno' => true],
            'Reportes' => ['propietario' => true, 'secretaria' => true, 'docente' => false, 'alumno' => false],
            'Bitácora' => ['propietario' => true, 'secretaria' => false, 'docente' => false, 'alumno' => false],
        ];

        $loginAceptados = Bitacora::where('tipo', 'login')->where('estado', 'aceptado')->count();
        $loginFallados = Bitacora::where('tipo', 'login')->where('estado', 'fallado')->count();
        $recursosMasAccedidos = Bitacora::selectRaw('recurso, count(*) as total')
            ->where('tipo', 'recurso')
            ->groupBy('recurso')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $ultimosEventos = Bitacora::latest()->limit(15)->get();

        return Inertia::render('admin/bitacora/Index', [
            'controlDeAcceso' => $controlDeAcceso,
            'loginAceptados' => $loginAceptados,
            'loginFallados' => $loginFallados,
            'recursosMasAccedidos' => $recursosMasAccedidos->map(function ($recurso) {
                return [
                    'recurso' => $recurso->recurso,
                    'total' => (int) $recurso->total,
                ];
            })->values(),
            'ultimosEventos' => $ultimosEventos->map(function ($evento) {
                return [
                    'id' => $evento->id,
                    'tipo' => $evento->tipo,
                    'estado' => $evento->estado,
                    'recurso' => $evento->recurso,
                    'created_at' => $evento->created_at?->toIso8601String(),
                    'email' => $evento->email,
                    'user' => $evento->user ? [
                        'email' => $evento->user->email,
                    ] : null,
                ];
            })->values(),
        ]);
    }
}
