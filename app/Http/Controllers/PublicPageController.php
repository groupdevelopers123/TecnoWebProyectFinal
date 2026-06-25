<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\DocenteDetalle;
use App\Models\OfertaAcademica;

class PublicPageController extends Controller
{
    public function inicio()
    {
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

        return view('welcome', compact('carreras', 'ofertas', 'docentes'));
    }

    public function carreras()
    {
        $carreras = Carrera::query()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('public.carreras.index', compact('carreras'));
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

        return view('public.ofertas.index', compact('ofertas'));
    }

    public function docentes()
    {
        $docentes = DocenteDetalle::query()
            ->with('user')
            ->latest()
            ->get();

        return view('public.docentes.index', compact('docentes'));
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