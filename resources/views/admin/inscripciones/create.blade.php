@extends('layouts.admin')

@section('title', 'Registrar Inscripción')
@section('page-title', 'Registrar Inscripción')
@section('page-subtitle', 'Crear una nueva inscripción académica')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Nueva inscripción</h2>
        <p class="mt-1 text-sm text-slate-500">
            Completa los datos para registrar la inscripción del alumno.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.inscripciones.store') }}">
        @csrf

        @include('admin.inscripciones._form', [
            'inscripcion' => new \App\Models\Inscripcion()
        ])
    </form>
</div>

@endsection