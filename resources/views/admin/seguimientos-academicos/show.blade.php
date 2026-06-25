@extends('layouts.admin')

@section('title', 'Detalle de Seguimiento Académico')
@section('page-title', 'Detalle de Seguimiento Académico')
@section('page-subtitle', 'Información completa del seguimiento académico')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-100 text-2xl text-indigo-700">
                <i class="fa-solid fa-chart-line"></i>
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900">
                    {{ $seguimiento->inscripcionMateria->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $seguimiento->inscripcionMateria->carreraMateria->materia->nombre }}
                </p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.seguimientos-academicos.edit', $seguimiento) }}"
               class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
                Editar
            </a>

            <a href="{{ route('admin.seguimientos-academicos.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Volver
            </a>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Alumno</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $seguimiento->inscripcionMateria->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Carrera</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $seguimiento->inscripcionMateria->inscripcion->ofertaAcademica->carrera->codigo }}
                -
                {{ $seguimiento->inscripcionMateria->inscripcion->ofertaAcademica->carrera->nombre }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Materia</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $seguimiento->inscripcionMateria->carreraMateria->materia->codigo }}
                -
                {{ $seguimiento->inscripcionMateria->carreraMateria->materia->nombre }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Docente</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $seguimiento->docenteDetalle->user?->nombreCompleto() ?? 'Docente sin usuario' }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Fecha de registro</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $seguimiento->fecha_registro?->format('d/m/Y') }}
            </p>
        </div>

        <div class="rounded-2xl bg-indigo-50 p-4">
            <p class="text-xs font-bold uppercase text-indigo-500">Nota final</p>
            <p class="mt-1 font-bold text-indigo-700">
                {{ $seguimiento->nota_final !== null ? $seguimiento->nota_final : 'No registrada' }}
            </p>
        </div>

        <div class="rounded-2xl bg-emerald-50 p-4">
            <p class="text-xs font-bold uppercase text-emerald-500">Asistencia</p>
            <p class="mt-1 font-bold text-emerald-700">
                {{ $seguimiento->porcentaje_asistencia !== null ? $seguimiento->porcentaje_asistencia . '%' : 'No registrada' }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Estado académico</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $seguimiento->estado_academico }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Observación</p>
            <p class="mt-1 text-sm font-medium text-slate-700">
                {{ $seguimiento->observacion ?: 'Sin observación registrada.' }}
            </p>
        </div>
    </div>
</div>

@endsection