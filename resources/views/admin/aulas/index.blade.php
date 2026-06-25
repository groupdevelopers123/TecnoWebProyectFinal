@extends('layouts.admin')

@section('title', 'Gestión de Aulas')
@section('page-title', 'Gestión de Aulas')
@section('page-subtitle', 'Administración de aulas, ubicación, dimensiones y capacidad')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Listado de aulas</h2>
            <p class="mt-1 text-sm text-slate-500">
                Busca, registra, edita o cambia la disponibilidad de las aulas.
            </p>
        </div>

        <a href="{{ route('admin.aulas.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Nueva aula
        </a>
    </div>

    <form method="GET" action="{{ route('admin.aulas.index') }}" class="mt-6 grid gap-4 md:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input
                type="text"
                name="buscar"
                placeholder="Código, nombre, ubicación o piso"
                value="{{ request('buscar') }}"
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
            >
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700">Disponibilidad</label>
            <select name="disponible"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                <option value="">Todas</option>
                <option value="1" @selected(request('disponible') === '1')>Disponibles</option>
                <option value="0" @selected(request('disponible') === '0')>No disponibles</option>
            </select>
        </div>

        <div class="flex items-end gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <a href="{{ route('admin.aulas.index') }}"
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
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Código</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Aula</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Ubicación</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Capacidad</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Dimensiones</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($aulas as $aula)
                    <tr class="transition hover:bg-slate-50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-slate-700">
                            {{ $aula->codigo }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                                    <i class="fa-solid fa-school"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $aula->nombre }}</p>
                                    <p class="text-xs text-slate-500">Piso: {{ $aula->piso ?? 'No definido' }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            {{ $aula->ubicacion ?? 'No registrada' }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700">
                            {{ $aula->capacidad }} estudiantes
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            @if ($aula->largo && $aula->ancho)
                                {{ $aula->largo }}m x {{ $aula->ancho }}m
                            @else
                                No registradas
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">
                            @if ($aula->disponible)
                                <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 ring-1 ring-green-100">
                                    Disponible
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 ring-1 ring-red-100">
                                    No disponible
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.aulas.show', $aula) }}"
                                   title="Ver aula"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.aulas.edit', $aula) }}"
                                   title="Editar aula"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.aulas.destroy', $aula) }}"
                                      onsubmit="return confirm('¿Está seguro de cambiar la disponibilidad de esta aula?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="{{ $aula->disponible ? 'Marcar como no disponible' : 'Marcar como disponible' }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5
                                            {{ $aula->disponible
                                                ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                                : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                        @if ($aula->disponible)
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        @else
                                            <i class="fa-solid fa-circle-check text-sm"></i>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                            No se encontraron aulas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 px-6 py-4">
        {{ $aulas->links() }}
    </div>
</div>

@endsection