@extends('layouts.admin')

@section('title', 'Registrar Periodo Académico')
@section('page-title', 'Registrar Periodo Académico')
@section('page-subtitle', 'Crea un nuevo periodo académico')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.periodos-academicos.store') }}">
        @csrf
        @include('admin.periodos._form')
    </form>
</div>

@endsection