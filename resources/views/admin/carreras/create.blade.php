@extends('layouts.admin')

@section('title', 'Registrar Carrera')
@section('page-title', 'Registrar Carrera')
@section('page-subtitle', 'Nueva carrera académica')

@section('content')
<div class="rounded-3xl bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.carreras.store') }}">
        @csrf
        @include('admin.carreras._form')
    </form>
</div>
@endsection