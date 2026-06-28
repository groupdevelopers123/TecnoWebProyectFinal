@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')
@section('page-title', 'Dashboard Administrativo')
@section('page-subtitle', 'Resumen general del sistema académico y administrativo')

@section('content')
<div class="space-y-6">
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 p-8 text-white shadow-xl">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-200">Panel principal</p>
                <h2 class="mt-3 text-3xl font-black">Bienvenido al centro de control del instituto</h2>
                <p class="mt-3 text-sm text-slate-300 sm:text-base">
                    Consulta en un solo lugar el estado operativo del sistema y accede rápidamente a los módulos clave del proceso académico.
                </p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <p class="text-sm font-semibold text-blue-100">Usuarios activos</p>
                <p class="mt-2 text-3xl font-black">{{ number_format($usuariosActivos) }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Usuarios registrados</p>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <p class="mt-4 text-4xl font-black text-slate-900">{{ number_format($totalUsuarios) }}</p>
            <p class="mt-2 text-sm text-slate-500">Total en el sistema</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Aulas disponibles</p>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-school"></i>
                </div>
            </div>
            <p class="mt-4 text-4xl font-black text-slate-900">{{ number_format($aulasDisponibles) }}</p>
            <p class="mt-2 text-sm text-slate-500">de {{ number_format($totalAulas) }} registradas</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Inscripciones</p>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
            </div>
            <p class="mt-4 text-4xl font-black text-slate-900">{{ number_format($totalInscripciones) }}</p>
            <p class="mt-2 text-sm text-slate-500">Procesadas en el sistema</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Pagos pendientes</p>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
            <p class="mt-4 text-4xl font-black text-slate-900">{{ number_format($pagosPendientes) }}</p>
            <p class="mt-2 text-sm text-slate-500">Cuotas y pagos por revisar</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-black text-slate-900">Accesos rápidos</h2>
                    <p class="mt-1 text-sm text-slate-500">Módulos principales para la gestión diaria del instituto.</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                @php
                    $modulos = [
                        [
                            'title' => 'Usuarios',
                            'description' => 'Gestiona propietarios, secretarias, docentes y alumnos.',
                            'route' => route('admin.usuarios.index'),
                            'icon' => 'fa-solid fa-users',
                            'accent' => 'border-blue-100 bg-blue-50 text-blue-600',
                        ],
                        [
                            'title' => 'Aulas',
                            'description' => 'Revisa disponibilidad, capacidad y ubicación de cada aula.',
                            'route' => route('admin.aulas.index'),
                            'icon' => 'fa-solid fa-school',
                            'accent' => 'border-emerald-100 bg-emerald-50 text-emerald-600',
                        ],
                        [
                            'title' => 'Horarios',
                            'description' => 'Administra periodos, carreras, materias y horarios.',
                            'route' => route('admin.periodos-academicos.index'),
                            'icon' => 'fa-solid fa-calendar-week',
                            'accent' => 'border-violet-100 bg-violet-50 text-violet-600',
                        ],
                        [
                            'title' => 'Inscripciones',
                            'description' => 'Revisa y gestiona las inscripciones del periodo actual.',
                            'route' => route('admin.inscripciones.index'),
                            'icon' => 'fa-solid fa-clipboard-list',
                            'accent' => 'border-slate-200 bg-slate-50 text-slate-600',
                        ],
                        [
                            'title' => 'Seguimiento académico',
                            'description' => 'Monitorea el avance y rendimiento de los estudiantes.',
                            'route' => route('admin.seguimientos-academicos.index'),
                            'icon' => 'fa-solid fa-chart-line',
                            'accent' => 'border-amber-100 bg-amber-50 text-amber-600',
                        ],
                        [
                            'title' => 'Pagos',
                            'description' => 'Controla pagos al contado, cuotas y créditos.',
                            'route' => route('admin.pago-contados.index'),
                            'icon' => 'fa-solid fa-file-invoice-dollar',
                            'accent' => 'border-rose-100 bg-rose-50 text-rose-600',
                        ],
                    ];
                @endphp

                @foreach ($modulos as $modulo)
                    <a href="{{ $modulo['route'] }}"
                       class="rounded-3xl border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $modulo['accent'] }}">
                                <i class="{{ $modulo['icon'] }}"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900">{{ $modulo['title'] }}</h3>
                                <p class="mt-2 text-sm text-slate-500">{{ $modulo['description'] }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-xl font-black text-slate-900">Estado del sistema</h2>
                <p class="mt-1 text-sm text-slate-500">Resumen operativo del día.</p>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Aulas disponibles</p>
                            <p class="mt-1 text-2xl font-black text-slate-900">{{ number_format($aulasDisponibles) }}/{{ number_format($totalAulas) }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Inscripciones cargadas</p>
                            <p class="mt-1 text-2xl font-black text-slate-900">{{ number_format($totalInscripciones) }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Pendientes de pago</p>
                            <p class="mt-1 text-2xl font-black text-slate-900">{{ number_format($pagosPendientes) }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection