<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AlumnoHomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('alumno/home');
    }
}