@extends('layouts.admin')

@section('title', 'Detalle de Materia')
@section('page-title', 'Detalle de Materia')
@section('page-subtitle', 'Información completa de la materia')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-emerald-100 text-2xl text-emerald-700">
                <i class="fa-solid fa-book-open"></i>
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900">{{ $materia->nombre }}</h2>
                <p class="mt-1 text-sm text-slate-500">Código: {{ $materia->codigo }}</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.materias.edit', $materia) }}"
               class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
                Editar
            </a>

            <a href="{{ route('admin.materias.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Volver
            </a>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Código</p>
            <p class="mt-1 font-bold text-slate-800">{{ $materia->codigo }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Nombre</p>
            <p class="mt-1 font-bold text-slate-800">{{ $materia->nombre }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Carga horaria</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $materia->carga_horaria ? $materia->carga_horaria . ' horas' : 'No registrada' }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Docente</p>
            <p class="mt-1 font-bold text-slate-800">
                @if ($materia->docenteDetalle)
                    {{ $materia->docenteDetalle->codigo }} - {{ $materia->docenteDetalle->user->nombres }} {{ $materia->docenteDetalle->user->apellidos }}
                @else
                    <span class="text-slate-400">No asignado</span>
                @endif
            </p>
        </div>

        <div class="rounded-2xl bg-emerald-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-emerald-500">Estado</p>
            <p class="mt-1 font-bold {{ $materia->estado ? 'text-green-700' : 'text-red-700' }}">
                {{ $materia->estado ? 'Activa' : 'Inactiva' }}
            </p>
        </div>

        <div class="mt-6 rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Carreras asignadas</p>

            @if ($materia->carreraMaterias->count())
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($materia->carreraMaterias as $asignacion)
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100">
                            {{ $asignacion->carrera->nombre }}
                            @if ($asignacion->periodo_numero)
                                - Período {{ $asignacion->periodo_numero }}
                            @endif
                        </span>
                    @endforeach
                </div>
            @else
                <p class="mt-2 text-sm text-slate-500">
                    Esta materia todavía no está asignada a ninguna carrera.
                </p>
            @endif
        </div>
    </div>
</div>

@endsection