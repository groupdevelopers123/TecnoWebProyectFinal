<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Inscripcion;
use App\Models\PagoContado;
use App\Models\PagoCuota;
use App\Models\User;

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

        return view('admin.dashboard', compact(
            'totalUsuarios',
            'usuariosActivos',
            'totalAulas',
            'aulasDisponibles',
            'totalInscripciones',
            'pagosPendientes'
        ));
    }
}