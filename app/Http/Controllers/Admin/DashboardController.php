<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsuarios = User::count();
        $aulasDisponibles = Aula::where('disponible', true)->count();

        return view('admin.dashboard', compact(
            'totalUsuarios',
            'aulasDisponibles'
        ));
    }
}