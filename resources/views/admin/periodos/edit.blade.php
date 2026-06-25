@extends('layouts.admin')

@section('title', 'Editar Periodo Académico')
@section('page-title', 'Editar Periodo Académico')
@section('page-subtitle', 'Actualiza la información del periodo')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.periodos-academicos.update', $periodo) }}">
        @csrf
        @method('PUT')
        @include('admin.periodos._form')
    </form>
</div>

@endsection