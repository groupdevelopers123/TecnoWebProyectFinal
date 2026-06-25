@extends('layouts.admin')

@section('title', 'Registrar Pago al Contado')
@section('page-title', 'Registrar Pago al Contado')
@section('page-subtitle', 'Crear un nuevo pago al contado')

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
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Nuevo pago al contado</h2>
        <p class="mt-1 text-sm text-slate-500">
            Registra un pago al contado. Si seleccionas QR, podrás generar el QR con PagoFácil.
        </p>
    </div>

    <form id="form-pago-contado"
        method="POST"
        action="{{ route('admin.pago-contados.store') }}">
        @csrf

        @include('admin.pagos.pago-contados._form', [
            'pago' => new \App\Models\PagoContado()
        ])
    </form>
</div>

@endsection