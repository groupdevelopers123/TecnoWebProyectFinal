@extends('layouts.admin')

@section('title', 'Editar Carrera')
@section('page-title', 'Editar Carrera')
@section('page-subtitle', 'Actualizar carrera académica')

@section('content')
<div class="rounded-3xl bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.carreras.update', $carrera) }}">
        @csrf
        @method('PUT')
        @include('admin.carreras._form')
    </form>
</div>
@endsection