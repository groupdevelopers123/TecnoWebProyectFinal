@extends('layouts.admin')

@section('title', 'Editar Pago al Contado')
@section('page-title', 'Editar Pago al Contado')
@section('page-subtitle', 'Modificar datos del pago al contado')

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
        <h2 class="text-xl font-black text-slate-900">Editar pago al contado</h2>
        <p class="mt-1 text-sm text-slate-500">
            Actualiza la información del pago seleccionado.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.pago-contados.update', $pago) }}">
        @csrf
        @method('PUT')

        @include('admin.pagos.pago-contados._form')
    </form>
</div>

@endsection