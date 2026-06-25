@extends('layouts.admin')

@section('title', 'Editar Crédito')
@section('page-title', 'Editar Crédito')
@section('page-subtitle', 'Modificar datos del crédito')

@section('content')

@include('admin.pagos.partials.nav')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Editar crédito</h2>
        <p class="mt-1 text-sm text-slate-500">
            Actualiza la información del crédito seleccionado.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.creditos.update', $credito) }}">
        @csrf
        @method('PUT')

        @include('admin.pagos.creditos._form')
    </form>
</div>

@endsection