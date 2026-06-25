@extends('layouts.admin')

@section('title', 'Carrera - Materia')
@section('page-title', 'Carrera - Materia')
@section('page-subtitle', 'Asignación de materias a carreras')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Asignaciones</h2>
            <p class="mt-1 text-sm text-slate-500">
                Relaciona materias con carreras para luego crear horarios.
            </p>
        </div>

        <a href="{{ route('admin.carrera-materias.create') }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Nueva asignación
        </a>
    </div>

    <form method="GET" action="{{ route('admin.carrera-materias.index') }}" class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input type="text"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Carrera o materia"
                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        </div>

        <div class="flex items-end gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <a href="{{ route('admin.carrera-materias.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Limpiar
            </a>
        </div>
    </form>
</div>

<div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Carrera</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Materia</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Período</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($asignaciones as $asignacion)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm font-bold text-slate-900">
                            {{ $asignacion->carrera->nombre }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $asignacion->materia->nombre }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $asignacion->periodo_numero ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.carrera-materias.show', $asignacion) }}"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.carrera-materias.edit', $asignacion) }}"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.carrera-materias.destroy', $asignacion) }}"
                                      onsubmit="return confirm('¿Está seguro de que desea eliminar esta asignación?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                            No existen materias asignadas a carreras.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 px-6 py-4">
        {{ $asignaciones->links() }}
    </div>
</div>

@endsection