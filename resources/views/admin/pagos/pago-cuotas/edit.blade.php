@extends('layouts.admin')

@section('title', 'Pagar Cuota')
@section('page-title', 'Pagar Cuota')
@section('page-subtitle', 'Registrar pago de una cuota')

@section('content')

@include('admin.pagos.partials.nav')

@if (session('error'))
    <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm font-bold text-red-700">
        {{ session('error') }}
    </div>
@endif

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">
            Pagar cuota #{{ $cuota->numero_cuota }}
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Completa los datos del pago de la cuota.
        </p>
    </div>

    <form id="form-pago-cuota"
          method="POST"
          action="{{ route('admin.pago-cuotas.update', $cuota) }}">
        @csrf
        @method('PUT')

        @include('admin.pagos.pago-cuotas._form')
    </form>
</div>

@endsection