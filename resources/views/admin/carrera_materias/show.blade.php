@extends('layouts.admin')

@section('title', 'Detalle de Asignación')
@section('page-title', 'Detalle de Asignación')
@section('page-subtitle', 'Información de carrera y materia asignada')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-2xl text-blue-700">
                <i class="fa-solid fa-link"></i>
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900">
                    {{ $asignacion->carrera->nombre }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $asignacion->materia->nombre }}
                </p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.carrera-materias.edit', $asignacion) }}"
               class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white">
                Editar
            </a>

            <a href="{{ route('admin.carrera-materias.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700">
                Volver
            </a>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Carrera</p>
            <p class="mt-1 font-bold text-slate-800">{{ $asignacion->carrera->nombre }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Materia</p>
            <p class="mt-1 font-bold text-slate-800">{{ $asignacion->materia->nombre }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Período</p>
            <p class="mt-1 font-bold text-slate-800">{{ $asignacion->periodo_numero ?? 'No definido' }}</p>
        </div>
    </div>
</div>

@endsection