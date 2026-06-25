@extends('layouts.admin')

@section('title', 'Registrar Oferta Académica')
@section('page-title', 'Registrar Oferta Académica')
@section('page-subtitle', 'Crear una nueva oferta académica del instituto')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Nueva oferta académica</h2>
        <p class="mt-1 text-sm text-slate-500">
            Completa los datos para registrar una oferta académica.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.ofertas-academicas.store') }}">
        @csrf

        @include('admin.ofertas-academicas._form', [
            'oferta' => new \App\Models\OfertaAcademica()
        ])
    </form>
</div>

@endsection