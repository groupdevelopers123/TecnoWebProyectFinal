@extends('layouts.admin')

@section('title', 'Editar Oferta Académica')
@section('page-title', 'Editar Oferta Académica')
@section('page-subtitle', 'Modificar datos de la oferta académica')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Editar oferta académica</h2>
        <p class="mt-1 text-sm text-slate-500">
            Actualiza la información de la oferta seleccionada.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.ofertas-academicas.update', $oferta) }}">
        @csrf
        @method('PUT')

        @include('admin.ofertas-academicas._form')
    </form>
</div>

@endsection