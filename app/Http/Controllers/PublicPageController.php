<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\DocenteDetalle;
use App\Models\OfertaAcademica;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PublicPageController extends Controller
{
    public function inicio()
    {
        if (Auth::check()) {
            $usuario = Auth::user()->loadMissing('role');
            $ruta = $this->rutaPorRol(optional($usuario->role)->nombre ?? '');

            if ($ruta !== '/') {
                return redirect($ruta);
            }
        }

        $carreras = Carrera::query()
            ->where('estado', true)
            ->orderBy('nombre')
            ->take(6)
            ->get();

        $ofertas = OfertaAcademica::query()
            ->with([
                'carrera',
                'periodoAcademico',
                'docenteDetalle.user',
            ])
            ->where('estado', true)
            ->latest()
            ->take(6)
            ->get();

        $docentes = DocenteDetalle::query()
            ->with('user')
            ->latest()
            ->take(8)
            ->get();

        return Inertia::render('welcome', compact('carreras', 'ofertas', 'docentes'));
    }

    public function carreras()
    {
        $carreras = Carrera::query()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        return Inertia::render('public/carreras/Index', compact('carreras'));
    }

    public function ofertasAcademicas()
    {
        $ofertas = OfertaAcademica::query()
            ->with([
                'carrera',
                'periodoAcademico',
                'docenteDetalle.user',
            ])
            ->where('estado', true)
            ->latest()
            ->get();

        return Inertia::render('public/ofertas/Index', compact('ofertas'));
    }

    public function docentes()
    {
        $docentes = DocenteDetalle::query()
            ->with('user')
            ->latest()
            ->get();

        return Inertia::render('public/docentes/Index', compact('docentes'));
    }

    private function rutaPorRol(string $rol): string
    {
        $rol = strtolower(trim($rol));

        return match ($rol) {
            'propietario', 'secretaria' => '/admin/dashboard',
            'docente' => '/docente/inicio',
            'alumno' => '/alumno/inicio',
            default => '/',
        };
    }

    public function inscribirse(OfertaAcademica $oferta)
    {
        return redirect()
            ->route('register', [
                'oferta_academica_id' => $oferta->id,
            ])
            ->with('info', 'Para inscribirte a esta oferta académica primero debes registrarte.');
    }
}