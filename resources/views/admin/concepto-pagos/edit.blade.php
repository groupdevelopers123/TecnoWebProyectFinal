@extends('layouts.admin')

@section('title', 'Editar Concepto de Pago')
@section('page-title', 'Editar Concepto de Pago')
@section('page-subtitle', 'Modificar datos del concepto de pago')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Editar concepto de pago</h2>
        <p class="mt-1 text-sm text-slate-500">
            Actualiza la información del concepto seleccionado.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.concepto-pagos.update', $concepto) }}">
        @csrf
        @method('PUT')

        @include('admin.concepto-pagos._form')
    </form>
</div>

@endsection