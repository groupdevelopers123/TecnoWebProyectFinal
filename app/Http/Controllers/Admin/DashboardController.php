<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Inscripcion;
use App\Models\PagoContado;
use App\Models\PagoCuota;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsuarios = User::count();
        $usuariosActivos = User::where('estado', true)->count();
        $totalAulas = Aula::count();
        $aulasDisponibles = Aula::where('disponible', true)->count();
        $totalInscripciones = Inscripcion::count();
        $pagosPendientes = PagoCuota::where('estado_cuota', 'pendiente')->count()
            + PagoContado::where('estado', 'Pendiente')->count();

        return Inertia::render('admin/Dashboard', [
            'totalUsuarios' => $totalUsuarios,
            'usuariosActivos' => $usuariosActivos,
            'totalAulas' => $totalAulas,
            'aulasDisponibles' => $aulasDisponibles,
            'totalInscripciones' => $totalInscripciones,
            'pagosPendientes' => $pagosPendientes,
        ]);
    }
}