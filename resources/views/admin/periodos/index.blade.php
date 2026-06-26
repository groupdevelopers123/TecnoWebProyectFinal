@extends('layouts.admin')

@section('title', 'Periodos Académicos')
@section('page-title', 'Periodos Académicos')
@section('page-subtitle', 'Administración de gestiones y periodos académicos')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Listado de periodos</h2>
            <p class="mt-1 text-sm text-slate-500">
                Gestiona los periodos académicos activos e históricos.
            </p>
        </div>

        <a href="{{ route('admin.periodos-academicos.create') }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Nuevo periodo
        </a>
    </div>

    <form id="periodos-search-form" method="GET" action="{{ route('admin.periodos-academicos.index') }}" class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input type="text"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Nombre o gestión"
                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        </div>

        <div class="flex items-end gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <a href="{{ route('admin.periodos-academicos.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Limpiar
            </a>
        </div>
    </form>
</div>

<div id="periodos-list" class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Nombre</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Gestión</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Tipo</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Fechas</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($periodos as $periodo)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">{{ $periodo->nombre }}</p>
                            <p class="text-xs text-slate-500">Periodo {{ $periodo->numero_periodo ?? '-' }}</p>
                        </td>

                        <td class="px-6 py-4 text-sm font-bold text-slate-700">
                            {{ $periodo->gestion }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $periodo->tipo_periodo ?? 'No definido' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $periodo->fecha_inicio?->format('d/m/Y') ?? '-' }}
                            -
                            {{ $periodo->fecha_fin?->format('d/m/Y') ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            @if ($periodo->estado)
                                <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 ring-1 ring-green-100">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 ring-1 ring-red-100">
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.periodos-academicos.show', $periodo) }}"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.periodos-academicos.edit', $periodo) }}"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.periodos-academicos.destroy', $periodo) }}"
                                      onsubmit="return confirm('¿Está seguro de cambiar el estado de este periodo?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5
                                            {{ $periodo->estado ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                        @if ($periodo->estado)
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
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                            No hay periodos académicos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 px-6 py-4">
        @if($periodos->total() > 10)
            {{ $periodos->links() }}
        @endif
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('periodos-search-form');
    const container = document.getElementById('periodos-list');
    if (!form || !container) return;

    const searchFields = Array.from(form.querySelectorAll('input[name="buscar"]'));
    let debounceTimer = null;

    function escapeHtml(text) {
        return text === null || text === undefined
            ? ''
            : String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function renderTable(data) {
        if (!data.length) {
            return `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Nombre</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Gestión</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Tipo</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Fechas</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">No hay periodos académicos registrados.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
        }

        return `
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Nombre</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Gestión</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Tipo</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Fechas</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        ${data.map(periodo => `
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-6 py-4"><p class="text-sm font-bold text-slate-900">${escapeHtml(periodo.nombre)}</p><p class="text-xs text-slate-500">Periodo ${escapeHtml(periodo.id)}</p></td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-700">${escapeHtml(periodo.gestion)}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">${escapeHtml(periodo.tipo_periodo || 'No definido')}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">${escapeHtml(periodo.fecha_inicio)} - ${escapeHtml(periodo.fecha_fin)}</td>
                                <td class="px-6 py-4">${periodo.estado ? '<span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 ring-1 ring-green-100">Activo</span>' : '<span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 ring-1 ring-red-100">Inactivo</span>'}</td>
                                <td class="px-6 py-4"><div class="flex flex-wrap items-center gap-2"><a href="/admin/periodos-academicos/${periodo.id}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200"><i class="fa-solid fa-eye text-sm"></i></a><a href="/admin/periodos-academicos/${periodo.id}/edit" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100"><i class="fa-solid fa-pen-to-square text-sm"></i></a></div></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    function renderPagination(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            return '';
        }

        return `
            <div class="border-t border-slate-100 px-6 py-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Página ${pagination.current_page} de ${pagination.last_page} — ${pagination.total} registros</p>
                    <div class="flex gap-2">
                        <button type="button" ${!pagination.prev_page_url ? 'disabled' : ''} onclick="fetch('${pagination.prev_page_url ?? ''}', {headers: {'X-Requested-With': 'XMLHttpRequest'}}).then(r => r.json()).then(handleResponse)" class="rounded-xl px-4 py-2 text-sm font-bold transition ${pagination.prev_page_url ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-slate-50 text-slate-300'}">Anterior</button>
                        <button type="button" ${!pagination.next_page_url ? 'disabled' : ''} onclick="fetch('${pagination.next_page_url ?? ''}', {headers: {'X-Requested-With': 'XMLHttpRequest'}}).then(r => r.json()).then(handleResponse)" class="rounded-xl px-4 py-2 text-sm font-bold transition ${pagination.next_page_url ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-slate-50 text-slate-300'}">Siguiente</button>
                    </div>
                </div>
            </div>
        `;
    }

    function handleResponse(data) {
        container.innerHTML = renderTable(data.data) + renderPagination(data.pagination);
    }

    function request(url) {
        fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(response => response.json())
            .then(handleResponse)
            .catch(() => {
                container.innerHTML = `<div class="px-6 py-12 text-center text-sm text-red-500">Error al cargar los periodos.</div>`;
            });
    }

    function search() {
        const qs = new URLSearchParams(new FormData(form)).toString();
        request(form.action + '?' + qs);
    }

    searchFields.forEach(field => {
        field.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(search, 250);
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        search();
    });
});
</script>
@endpush

@endsection