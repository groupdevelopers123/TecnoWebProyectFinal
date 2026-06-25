@extends('layouts.admin')

@section('title', 'Editar Asignación')
@section('page-title', 'Editar Asignación')
@section('page-subtitle', 'Actualizar relación entre carrera y materia')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.carrera-materias.update', $asignacion) }}">
        @csrf
        @method('PUT')
        @include('admin.carrera_materias._form')
    </form>
</div>

@endsection