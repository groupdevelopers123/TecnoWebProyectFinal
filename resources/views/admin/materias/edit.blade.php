@extends('layouts.admin')

@section('title', 'Editar Materia')
@section('page-title', 'Editar Materia')
@section('page-subtitle', 'Actualiza la información de la materia')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Editar materia</h2>
            <p class="mt-1 text-sm text-slate-500">
                Modifica solo los datos necesarios.
            </p>
        </div>

        <a href="{{ route('admin.materias.show', $materia) }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            <i class="fa-solid fa-eye text-xs"></i>
            Ver detalle
        </a>
    </div>

    <form method="POST" action="{{ route('admin.materias.update', $materia) }}">
        @csrf
        @method('PUT')
        @include('admin.materias._form')
    </form>
</div>

@endsection