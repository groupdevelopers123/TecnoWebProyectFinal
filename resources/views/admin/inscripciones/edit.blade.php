@extends('layouts.admin')

@section('title', 'Editar Inscripción')
@section('page-title', 'Editar Inscripción')
@section('page-subtitle', 'Modificar datos de la inscripción académica')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Editar inscripción</h2>
        <p class="mt-1 text-sm text-slate-500">
            Actualiza la información de la inscripción seleccionada.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.inscripciones.update', $inscripcion) }}">
        @csrf
        @method('PUT')

        @include('admin.inscripciones._form')
    </form>
</div>

@endsection