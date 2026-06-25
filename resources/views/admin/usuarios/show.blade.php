@extends('layouts.admin')

@section('title', 'Detalle de Usuario')
@section('page-title', 'Detalle de Usuario')
@section('page-subtitle', 'Información completa del usuario registrado')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-2xl font-black text-blue-700">
                {{ strtoupper(substr($usuario->nombres, 0, 1)) }}
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900">
                    {{ $usuario->nombreCompleto() }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $usuario->email }}
                </p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.usuarios.edit', $usuario) }}"
               class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                Editar
            </a>

            <a href="{{ route('admin.usuarios.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Volver
            </a>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">CI</p>
            <p class="mt-1 font-bold text-slate-800">{{ $usuario->ci }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Rol</p>
            <p class="mt-1 font-bold text-slate-800">{{ ucfirst($usuario->role->nombre) }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Teléfono</p>
            <p class="mt-1 font-bold text-slate-800">{{ $usuario->telefono ?? 'No registrado' }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Dirección</p>
            <p class="mt-1 font-bold text-slate-800">{{ $usuario->direccion ?? 'No registrada' }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Fecha de nacimiento</p>
            <p class="mt-1 font-bold text-slate-800">{{ $usuario->fecha_nacimiento?->format('d/m/Y') ?? 'No registrada' }}</p>
        </div>

        <div class="rounded-2xl bg-slate-50 p-4">
            <p class="text-xs font-bold uppercase text-slate-400">Estado</p>
            <p class="mt-1 font-bold {{ $usuario->estado ? 'text-green-700' : 'text-red-700' }}">
                {{ $usuario->estado ? 'Activo' : 'Inactivo' }}
            </p>
        </div>

        <div class="border-t border-slate-200 pt-6 md:col-span-2">
            <h3 class="text-lg font-black text-slate-900">Datos específicos</h3>
            <p class="mt-1 text-sm text-slate-500">Información correspondiente al rol asignado.</p>
        </div>

        @if ($usuario->role->nombre === 'propietario' && $usuario->propietarioDetalle)
            <div class="rounded-2xl bg-blue-50 p-4">
                <p class="text-xs font-bold uppercase text-blue-500">Código</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->propietarioDetalle->codigo }}</p>
            </div>

            <div class="rounded-2xl bg-blue-50 p-4">
                <p class="text-xs font-bold uppercase text-blue-500">Cargo</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->propietarioDetalle->cargo ?? 'No registrado' }}</p>
            </div>
        @endif

        @if ($usuario->role->nombre === 'secretaria' && $usuario->secretariaDetalle)
            <div class="rounded-2xl bg-violet-50 p-4">
                <p class="text-xs font-bold uppercase text-violet-500">Código</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->secretariaDetalle->codigo }}</p>
            </div>

            <div class="rounded-2xl bg-violet-50 p-4">
                <p class="text-xs font-bold uppercase text-violet-500">Turno</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->secretariaDetalle->turno_trabajo ?? 'No registrado' }}</p>
            </div>

            <div class="rounded-2xl bg-violet-50 p-4">
                <p class="text-xs font-bold uppercase text-violet-500">Sueldo</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->secretariaDetalle->sueldo ?? 'No registrado' }}</p>
            </div>
        @endif

        @if ($usuario->role->nombre === 'docente' && $usuario->docenteDetalle)
            <div class="rounded-2xl bg-emerald-50 p-4">
                <p class="text-xs font-bold uppercase text-emerald-500">Código</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->docenteDetalle->codigo }}</p>
            </div>

            <div class="rounded-2xl bg-emerald-50 p-4">
                <p class="text-xs font-bold uppercase text-emerald-500">Especialidad</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->docenteDetalle->especialidad ?? 'No registrada' }}</p>
            </div>

            <div class="rounded-2xl bg-emerald-50 p-4">
                <p class="text-xs font-bold uppercase text-emerald-500">Título</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->docenteDetalle->titulo ?? 'No registrado' }}</p>
            </div>

            <div class="rounded-2xl bg-emerald-50 p-4">
                <p class="text-xs font-bold uppercase text-emerald-500">Registro profesional</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->docenteDetalle->registro_profesional ?? 'No registrado' }}</p>
            </div>
        @endif

        @if ($usuario->role->nombre === 'alumno' && $usuario->alumnoDetalle)
            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-xs font-bold uppercase text-amber-500">Código</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->alumnoDetalle->codigo }}</p>
            </div>

            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-xs font-bold uppercase text-amber-500">Colegio de origen</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->alumnoDetalle->colegio_origen ?? 'No registrado' }}</p>
            </div>

            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-xs font-bold uppercase text-amber-500">Año bachillerato</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->alumnoDetalle->anio_bachillerato ?? 'No registrado' }}</p>
            </div>

            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-xs font-bold uppercase text-amber-500">Estado académico</p>
                <p class="mt-1 font-bold text-slate-800">{{ $usuario->alumnoDetalle->estado_academico ?? 'No registrado' }}</p>
            </div>
        @endif
    </div>
</div>

@endsection