@extends('layouts.admin')

@section('title', 'Detalle de Periodo')
@section('page-title', 'Detalle de Periodo')
@section('page-subtitle', 'Información completa del periodo académico')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-2xl text-blue-700">
                <i class="fa-solid fa-calendar-days"></i>
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900">{{ $periodo->nombre }}</h2>
                <p class="mt-1 text-sm text-slate-500">Gestión {{ $periodo->gestion }}</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.periodos-academicos.edit', $periodo) }}"
               class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white">
                Editar
            </a>

            <a href="{{ route('admin.periodos-academicos.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700">
                Volver
            </a>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Nombre</p>
            <p class="mt-1 font-bold text-slate-800">{{ $periodo->nombre }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Gestión</p>
            <p class="mt-1 font-bold text-slate-800">{{ $periodo->gestion }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Tipo</p>
            <p class="mt-1 font-bold text-slate-800">{{ $periodo->tipo_periodo ?? 'No definido' }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Número</p>
            <p class="mt-1 font-bold text-slate-800">{{ $periodo->numero_periodo ?? 'No definido' }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Fecha inicio</p>
            <p class="mt-1 font-bold text-slate-800">{{ $periodo->fecha_inicio?->format('d/m/Y') ?? 'No registrada' }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Fecha fin</p>
            <p class="mt-1 font-bold text-slate-800">{{ $periodo->fecha_fin?->format('d/m/Y') ?? 'No registrada' }}</p>
        </div>

        <div class="rounded-2xl bg-blue-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-blue-500">Estado</p>
            <p class="mt-1 font-bold {{ $periodo->estado ? 'text-green-700' : 'text-red-700' }}">
                {{ $periodo->estado ? 'Activo' : 'Inactivo' }}
            </p>
        </div>
    </div>
</div>

@endsection