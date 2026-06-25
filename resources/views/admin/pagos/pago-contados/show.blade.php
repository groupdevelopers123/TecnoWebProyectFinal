@extends('layouts.admin')

@section('title', 'Detalle de Pago al Contado')
@section('page-title', 'Detalle de Pago al Contado')
@section('page-subtitle', 'Información completa del pago')

@section('content')

@include('admin.pagos.partials.nav')

@if (session('success'))
    <div class="mb-6 rounded-2xl border border-green-100 bg-green-50 p-4 text-sm font-bold text-green-700">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm font-bold text-red-700">
        {{ session('error') }}
    </div>
@endif

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-emerald-100 text-2xl text-emerald-700">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900">
                    Pago #{{ $pago->id }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $pago->conceptoPago->nombre }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            @if ($pago->metodo_pago === 'QR' && $pago->estado === 'Pendiente')
                <form method="POST" action="{{ route('admin.pago-contados.consultar', $pago) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:-translate-y-0.5 hover:bg-emerald-700">
                        <i class="fa-solid fa-rotate text-xs"></i>
                        Consultar PagoFácil
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.pago-contados.edit', $pago) }}"
               class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
                Editar
            </a>

            <a href="{{ route('admin.pago-contados.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Volver
            </a>
        </div>
    </div>

    @if ($pago->metodo_pago === 'QR' && $pago->qr_path)
        <div class="mb-6 rounded-3xl border border-emerald-100 bg-emerald-50 p-6 text-center">
            <p class="mb-4 text-sm font-bold uppercase text-emerald-600">
                QR PagoFácil
            </p>

            <img src="{{ Storage::url($pago->qr_path) }}"
                 alt="QR PagoFácil"
                 class="mx-auto h-72 w-72 rounded-3xl border border-white bg-white p-4 shadow-sm">

            <p class="mt-4 text-sm font-bold text-slate-700">
                Payment Number: {{ $pago->payment_number }}
            </p>
        </div>
    @endif

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Alumno</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $pago->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Carrera</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $pago->inscripcion->ofertaAcademica->carrera->nombre }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Concepto</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $pago->conceptoPago->nombre }}
            </p>
        </div>

        <div class="rounded-2xl bg-emerald-50 p-4">
            <p class="text-xs font-bold uppercase text-emerald-500">Monto pagado</p>
            <p class="mt-1 font-bold text-emerald-700">
                Bs {{ number_format($pago->monto_pagado, 2) }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Fecha de pago</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $pago->fecha_pago?->format('d/m/Y') }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Método</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $pago->metodo_pago }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Estado</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $pago->estado }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Código transacción</p>
            <p class="mt-1 font-bold text-slate-800">
                {{ $pago->codigo_transaccion ?: 'No registrado' }}
            </p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
            <p class="text-xs font-bold uppercase text-slate-400">Observación</p>
            <p class="mt-1 whitespace-pre-line text-sm font-medium text-slate-700">
                {{ $pago->observacion ?: 'Sin observación registrada.' }}
            </p>
        </div>
    </div>
</div>

@endsection