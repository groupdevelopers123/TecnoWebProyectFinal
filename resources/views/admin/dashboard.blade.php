@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')
@section('page-title', 'Dashboard Administrativo')
@section('page-subtitle', 'Resumen general del sistema académico y administrativo')

@section('content')

<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
    <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
        <p class="text-sm font-medium text-slate-500">Usuarios registrados</p>
        <p class="mt-3 text-4xl font-black text-slate-900">{{ $totalUsuarios }}</p>
        <div class="mt-5 h-2 rounded-full bg-blue-100">
            <div class="h-2 w-3/4 rounded-full bg-blue-600"></div>
        </div>
    </div>

    <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
        <p class="text-sm font-medium text-slate-500">Aulas disponibles</p>
        <p class="mt-3 text-4xl font-black text-slate-900">{{ $aulasDisponibles }}</p>
        <div class="mt-5 h-2 rounded-full bg-emerald-100">
            <div class="h-2 w-1/4 rounded-full bg-emerald-500"></div>
        </div>
    </div>

    <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
        <p class="text-sm font-medium text-slate-500">Inscripciones</p>
        <p class="mt-3 text-4xl font-black text-slate-900">0</p>
        <div class="mt-5 h-2 rounded-full bg-violet-100">
            <div class="h-2 w-1/4 rounded-full bg-violet-500"></div>
        </div>
    </div>

    <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
        <p class="text-sm font-medium text-slate-500">Pagos pendientes</p>
        <p class="mt-3 text-4xl font-black text-slate-900">0</p>
        <div class="mt-5 h-2 rounded-full bg-amber-100">
            <div class="h-2 w-1/4 rounded-full bg-amber-500"></div>
        </div>
    </div>
</div>

<div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">
                Módulos del sistema
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Accesos principales para propietario y secretaria.
            </p>
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('admin.usuarios.index') }}"
           class="rounded-3xl border border-blue-100 bg-blue-50 p-5 transition hover:-translate-y-1 hover:bg-blue-100 hover:shadow-lg">
            <p class="text-sm font-bold text-blue-600">CU1</p>
            <h3 class="mt-2 text-lg font-black text-slate-900">Gestión de usuarios</h3>
            <p class="mt-2 text-sm text-slate-500">Propietarios, secretarias, docentes y alumnos.</p>
        </a>

        <a href="{{ route('admin.aulas.index') }}"
        class="rounded-3xl border border-emerald-100 bg-emerald-50 p-5 transition hover:-translate-y-1 hover:bg-emerald-100 hover:shadow-lg">
            <p class="text-sm font-bold text-emerald-600">CU2</p>
            <h3 class="mt-2 text-lg font-black text-slate-900">Gestión de aulas</h3>
            <p class="mt-2 text-sm text-slate-500">Ubicación, dimensiones y capacidad.</p>
        </a>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:shadow-lg">
            <p class="text-sm font-bold text-slate-500">CU3</p>
            <h3 class="mt-2 text-lg font-black text-slate-900">Gestión de horarios</h3>
            <p class="mt-2 text-sm text-slate-500">Turnos mañana, tarde y noche.</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:shadow-lg">
            <p class="text-sm font-bold text-slate-500">CU4</p>
            <h3 class="mt-2 text-lg font-black text-slate-900">Oferta académica</h3>
            <p class="mt-2 text-sm text-slate-500">Periodos, carreras y materias.</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:shadow-lg">
            <p class="text-sm font-bold text-slate-500">CU5</p>
            <h3 class="mt-2 text-lg font-black text-slate-900">Inscripciones</h3>
            <p class="mt-2 text-sm text-slate-500">Estudiantes del periodo 1-2026.</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:shadow-lg">
            <p class="text-sm font-bold text-slate-500">CU6</p>
            <h3 class="mt-2 text-lg font-black text-slate-900">Seguimiento académico</h3>
            <p class="mt-2 text-sm text-slate-500">Rendimiento y trayectoria del estudiante.</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:shadow-lg">
            <p class="text-sm font-bold text-slate-500">CU7</p>
            <h3 class="mt-2 text-lg font-black text-slate-900">Pagos</h3>
            <p class="mt-2 text-sm text-slate-500">Contado, crédito y cuotas.</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:shadow-lg">
            <p class="text-sm font-bold text-slate-500">CU8</p>
            <h3 class="mt-2 text-lg font-black text-slate-900">Reportes</h3>
            <p class="mt-2 text-sm text-slate-500">Reportes y estadísticas del sistema.</p>
        </div>
    </div>
</div>

@endsection