@extends('layouts.admin')

@section('title', 'Registrar Crédito')
@section('page-title', 'Registrar Crédito')
@section('page-subtitle', 'Crear un nuevo crédito académico')

@section('content')

@include('admin.pagos.partials.nav')

@if (session('success'))
    <div class="mb-6 rounded-2xl border border-green-100 bg-green-50 p-4 text-sm font-bold text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Nuevo crédito</h2>
        <p class="mt-1 text-sm text-slate-500">
            Completa los datos para registrar un crédito.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.creditos.store') }}">
        @csrf

        @include('admin.pagos.creditos._form')
    </form>
</div>

@endsection