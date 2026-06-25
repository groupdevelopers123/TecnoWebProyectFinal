@extends('layouts.admin')

@section('title', 'Registrar Materia')
@section('page-title', 'Registrar Materia')
@section('page-subtitle', isset($carreraSeleccionada) && $carreraSeleccionada ? 'Registrar materia para una carrera específica' : 'Crea una nueva materia académica')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">
            Nueva materia
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            @if (isset($carreraSeleccionada) && $carreraSeleccionada)
                La materia será asignada automáticamente a la carrera {{ $carreraSeleccionada->nombre }}.
            @else
                Completa los datos de la materia.
            @endif
        </p>
    </div>

    <form method="POST" action="{{ route('admin.materias.store') }}">
        @csrf

        @include('admin.materias._form')
    </form>
</div>

@endsection