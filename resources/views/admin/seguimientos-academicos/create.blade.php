@extends('layouts.admin')

@section('title', 'Registrar Seguimiento Académico')
@section('page-title', 'Registrar Seguimiento Académico')
@section('page-subtitle', 'Crear un nuevo seguimiento académico')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Nuevo seguimiento académico</h2>
        <p class="mt-1 text-sm text-slate-500">
            Completa los datos para registrar el seguimiento académico del alumno.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.seguimientos-academicos.store') }}">
        @csrf

        @include('admin.seguimientos-academicos._form', [
            'seguimiento' => new \App\Models\SeguimientoAcademico()
        ])
    </form>
</div>

@endsection