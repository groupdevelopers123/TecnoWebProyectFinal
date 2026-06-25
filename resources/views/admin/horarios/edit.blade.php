@extends('layouts.admin')

@section('title', 'Editar Horario')
@section('page-title', 'Editar Horario')
@section('page-subtitle', 'Actualiza el horario académico')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.horarios.update', $horario) }}">
        @csrf
        @method('PUT')
        @include('admin.horarios._form')
    </form>
</div>

@endsection