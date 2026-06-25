@extends('layouts.admin')

@section('title', 'Detalle de Concepto de Pago')
@section('page-title', 'Detalle de Concepto de Pago')
@section('page-subtitle', 'Información completa del concepto de pago')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-amber-100 text-2xl text-amber-700">
                <i class="fa-solid fa-tags"></i>
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900">
                    {{ $concepto->nombre }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Concepto de pago #{{ $concepto->id }}
                </p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.concepto-pagos.edit', $concepto) }}"
               class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
                Editar
            </a>

            <a href="{{ route('admin.concepto-pagos.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Volver
            </a>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Nombre</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $concepto->nombre }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Descripción</p>
            <p class="mt-1 text-sm font-medium text-slate-700">
                {{ $concepto->descripcion ?: 'Sin descripción registrada.' }}
            </p>
        </div>

        <div class="rounded-2xl bg-amber-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-amber-500">Estado</p>

            @if ($concepto->estado === 'Activo')
                <p class="mt-1 font-bold text-green-700">Activo</p>
            @else
                <p class="mt-1 font-bold text-red-700">Inactivo</p>
            @endif
        </div>
    </div>
</div>

@endsection