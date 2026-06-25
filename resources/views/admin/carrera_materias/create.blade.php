@extends('layouts.admin')

@section('title', 'Nueva Asignación')
@section('page-title', 'Nueva Asignación')
@section('page-subtitle', 'Asignar materia a una carrera')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.carrera-materias.store') }}">
        @csrf
        @include('admin.carrera_materias._form')
    </form>
</div>

@endsection