@extends('layouts.admin')

@section('title', 'Registrar Horario')
@section('page-title', 'Registrar Horario')
@section('page-subtitle', 'Crea un nuevo horario académico')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.horarios.store') }}">
        @csrf
        @include('admin.horarios._form')
    </form>
</div>

@endsection