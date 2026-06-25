@extends('layouts.admin')

@section('title', 'Detalle de Horario')
@section('page-title', 'Detalle de Horario')
@section('page-subtitle', 'Información completa del horario académico')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-2xl text-blue-700">
                <i class="fa-solid fa-clock"></i>
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900">
                    {{ $horario->dia }} / {{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $horario->turno }} — {{ $horario->periodoAcademico->nombre }}
                </p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.horarios.edit', $horario) }}"
               class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white">
                Editar
            </a>

            <a href="{{ route('admin.horarios.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700">
                Volver
            </a>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Carrera</p>
            <p class="mt-1 font-bold text-slate-800">{{ $horario->carreraMateria->carrera->nombre }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Materia</p>
            <p class="mt-1 font-bold text-slate-800">{{ $horario->carreraMateria->materia->nombre }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Docente</p>
            <p class="mt-1 font-bold text-slate-800">{{ $horario->docenteDetalle->user->nombreCompleto() }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Aula</p>
            <p class="mt-1 font-bold text-slate-800">{{ $horario->aula->nombre }} - {{ $horario->aula->codigo }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Día</p>
            <p class="mt-1 font-bold text-slate-800">{{ $horario->dia }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Turno</p>
            <p class="mt-1 font-bold text-slate-800">{{ $horario->turno }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Hora inicio</p>
            <p class="mt-1 font-bold text-slate-800">{{ substr($horario->hora_inicio, 0, 5) }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Hora fin</p>
            <p class="mt-1 font-bold text-slate-800">{{ substr($horario->hora_fin, 0, 5) }}</p>
        </div>

        <div class="rounded-2xl bg-blue-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-blue-500">Periodo académico</p>
            <p class="mt-1 font-bold text-slate-800">{{ $horario->periodoAcademico->nombre }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Estado</p>
            <p class="mt-1 font-bold {{ $horario->estado ? 'text-green-700' : 'text-red-700' }}">
                {{ $horario->estado ? 'Activo' : 'Inactivo' }}
            </p>
        </div>
    </div>
</div>

@endsection