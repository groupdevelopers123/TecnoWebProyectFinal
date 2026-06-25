@extends('layouts.admin')

@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')
@section('page-subtitle', 'Actualiza la información general y específica del usuario')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Editar usuario</h2>
            <p class="mt-1 text-sm text-slate-500">
                Modifica solo los datos necesarios.
            </p>
        </div>

        <a href="{{ route('admin.usuarios.show', $usuario) }}"
           class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Ver detalle
        </a>
    </div>

    <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
        @csrf
        @method('PUT')

        @include('admin.usuarios._form')
    </form>
</div>

@endsection