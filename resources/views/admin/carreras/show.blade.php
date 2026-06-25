@extends('layouts.admin')

@section('title', 'Detalle de Carrera')
@section('page-title', 'Detalle de Carrera')
@section('page-subtitle', 'Información completa de la carrera')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-2xl text-blue-700">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900">{{ $carrera->nombre }}</h2>
                <p class="mt-1 text-sm text-slate-500">Código: {{ $carrera->codigo }}</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.carreras.edit', $carrera) }}"
               class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
                Editar
            </a>

            <a href="{{ route('admin.carreras.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Volver
            </a>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Código</p>
            <p class="mt-1 font-bold text-slate-800">{{ $carrera->codigo }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Nombre</p>
            <p class="mt-1 font-bold text-slate-800">{{ $carrera->nombre }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Duración</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $carrera->duracion ? $carrera->duracion . ' periodos' : 'No registrada' }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Régimen académico</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $carrera->regimen_academico ?? 'No registrado' }}
            </p>
        </div>

        <div class="rounded-2xl bg-blue-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-blue-500">Estado</p>
            <p class="mt-1 font-bold {{ $carrera->estado ? 'text-green-700' : 'text-red-700' }}">
                {{ $carrera->estado ? 'Activa' : 'Inactiva' }}
            </p>
        </div>

        <div class="mt-6 rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Materias asignadas</p>

            @if ($carrera->carreraMaterias->count())
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($carrera->carreraMaterias as $asignacion)
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ $asignacion->materia->nombre }}

                            @if ($asignacion->periodo_numero)
                                -
                                @if ($carrera->regimen_academico === 'Anual')
                                    Año {{ $asignacion->periodo_numero }}
                                @elseif ($carrera->regimen_academico === 'Modular')
                                    Módulo {{ $asignacion->periodo_numero }}
                                @else
                                    Semestre {{ $asignacion->periodo_numero }}
                                @endif
                            @endif
                        </span>
                    @endforeach
                </div>
            @else
                <p class="mt-2 text-sm text-slate-500">
                    Esta carrera todavía no tiene materias asignadas.
                </p>
            @endif
        </div>
    </div>
</div>

@endsection