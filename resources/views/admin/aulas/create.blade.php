@extends('layouts.admin')

@section('title', 'Registrar Aula')
@section('page-title', 'Registrar Aula')
@section('page-subtitle', 'Crea una nueva aula para el instituto')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Nueva aula</h2>
        <p class="mt-1 text-sm text-slate-500">
            Completa los datos del aula, su capacidad y dimensiones.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.aulas.store') }}">
        @csrf

        @include('admin.aulas._form')
    </form>
</div>

@endsection