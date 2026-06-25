@extends('layouts.admin')

@section('title', 'Detalle de Inscripción')
@section('page-title', 'Detalle de Inscripción')
@section('page-subtitle', 'Información completa de la inscripción académica')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-cyan-100 text-2xl text-cyan-700">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900">
                    {{ $inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Inscripción #{{ $inscripcion->id }}
                </p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.inscripciones.edit', $inscripcion) }}"
               class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
                Editar
            </a>

            <a href="{{ route('admin.inscripciones.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Volver
            </a>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Alumno</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $inscripcion->alumnoDetalle->codigo ?? 'SIN-COD' }}
                -
                {{ $inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Oferta académica</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $inscripcion->ofertaAcademica->nombre }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Carrera</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $inscripcion->ofertaAcademica->carrera->codigo }}
                -
                {{ $inscripcion->ofertaAcademica->carrera->nombre }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Periodo académico</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $inscripcion->ofertaAcademica->periodoAcademico->nombre }}
                -
                {{ $inscripcion->ofertaAcademica->periodoAcademico->gestion }}
            </p>
        </div>

        <div class="rounded-2xl bg-cyan-50 p-4">
            <p class="text-xs font-bold uppercase text-cyan-500">Periodo número</p>
            <p class="mt-1 font-bold text-cyan-700">
                {{ $inscripcion->periodo_numero }}
            </p>
        </div>

        <div class="rounded-2xl bg-cyan-50 p-4">
            <p class="text-xs font-bold uppercase text-cyan-500">Fecha de inscripción</p>
            <p class="mt-1 font-bold text-cyan-700">
                {{ $inscripcion->fecha_inscripcion?->format('d/m/Y') }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Registrado por</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $inscripcion->usuarioRegistro?->nombreCompleto() ?? 'No registrado' }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Observación</p>
            <p class="mt-1 text-sm font-medium text-slate-700">
                {{ $inscripcion->observacion ?: 'Sin observación registrada.' }}
            </p>
        </div>
    </div>
</div>

@endsection