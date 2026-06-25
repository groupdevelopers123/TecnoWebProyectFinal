@extends('layouts.admin')

@section('title', 'Editar Aula')
@section('page-title', 'Editar Aula')
@section('page-subtitle', 'Actualiza la información del aula seleccionada')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Editar aula</h2>
            <p class="mt-1 text-sm text-slate-500">
                Modifica solo los datos necesarios.
            </p>
        </div>

        <a href="{{ route('admin.aulas.show', $aula) }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            <i class="fa-solid fa-eye text-xs"></i>
            Ver detalle
        </a>
    </div>

    <form method="POST" action="{{ route('admin.aulas.update', $aula) }}">
        @csrf
        @method('PUT')

        @include('admin.aulas._form')
    </form>
</div>

@endsection