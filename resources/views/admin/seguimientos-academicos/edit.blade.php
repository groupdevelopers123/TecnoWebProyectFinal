@extends('layouts.admin')

@section('title', 'Editar Seguimiento Académico')
@section('page-title', 'Editar Seguimiento Académico')
@section('page-subtitle', 'Modificar datos del seguimiento académico')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Editar seguimiento académico</h2>
        <p class="mt-1 text-sm text-slate-500">
            Actualiza la información del seguimiento seleccionado.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.seguimientos-academicos.update', $seguimiento) }}">
        @csrf
        @method('PUT')

        @include('admin.seguimientos-academicos._form')
    </form>
</div>

@endsection