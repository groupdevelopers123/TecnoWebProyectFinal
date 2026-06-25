@extends('layouts.admin')

@section('title', 'Registrar Concepto de Pago')
@section('page-title', 'Registrar Concepto de Pago')
@section('page-subtitle', 'Crear un nuevo concepto de pago')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Nuevo concepto de pago</h2>
        <p class="mt-1 text-sm text-slate-500">
            Completa los datos para registrar un concepto de pago.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.concepto-pagos.store') }}">
        @csrf

        @include('admin.concepto-pagos._form')
    </form>
</div>

@endsection