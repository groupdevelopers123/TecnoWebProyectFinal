<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Inertia\Response;
use Inertia\Inertia;

class DocenteHomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('docente/home');
    }
}