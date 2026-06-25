@extends('layouts.admin')

@section('title', 'Registrar Usuario')
@section('page-title', 'Registrar Usuario')
@section('page-subtitle', 'Crea un nuevo propietario, secretaria, docente o alumno')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-900">Nuevo usuario</h2>
        <p class="mt-1 text-sm text-slate-500">
            Completa los datos generales y específicos según el rol.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.usuarios.store') }}">
        @csrf

        @include('admin.usuarios._form')
    </form>
</div>

@endsection