@extends('layouts.admin')

@section('title', 'Detalle de Crédito')
@section('page-title', 'Detalle de Crédito')
@section('page-subtitle', 'Información completa del crédito')

@section('content')

@include('admin.pagos.partials.nav')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-amber-100 text-2xl text-amber-700">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900">
                    Crédito #{{ $credito->id }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $credito->conceptoPago->nombre ?? 'Sin concepto' }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.creditos.edit', $credito) }}"
               class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
                Editar
            </a>

            <a href="{{ route('admin.creditos.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Volver
            </a>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">

        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Alumno</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $credito->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Carrera</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $credito->inscripcion->ofertaAcademica->carrera->nombre ?? 'Sin carrera' }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Concepto</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $credito->conceptoPago->nombre ?? 'Sin concepto' }}
            </p>
        </div>

        <div class="rounded-2xl bg-blue-50 p-4">
            <p class="text-xs font-bold uppercase text-blue-500">Monto total</p>
            <p class="mt-1 font-bold text-blue-700">
                Bs {{ number_format($credito->monto_total, 2) }}
            </p>
        </div>

        <div class="rounded-2xl bg-amber-50 p-4">
            <p class="text-xs font-bold uppercase text-amber-500">Saldo pendiente</p>
            <p class="mt-1 font-bold text-amber-700">
                Bs {{ number_format($credito->saldo_pendiente, 2) }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Cantidad de cuotas</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $credito->cantidad_cuotas }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Tipo de pago</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $credito->tipo_pago }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Estado</p>

            <p class="mt-1 font-bold
                @if ($credito->estado === 'pagado')
                    text-green-700
                @elseif ($credito->estado === 'anulado')
                    text-red-700
                @elseif ($credito->estado === 'activo')
                    text-blue-700
                @else
                    text-yellow-700
                @endif">
                {{ ucfirst($credito->estado) }}
            </p>
        </div>

    </div>
</div>

@endsection