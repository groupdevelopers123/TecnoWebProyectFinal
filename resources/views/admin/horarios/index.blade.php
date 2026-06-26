@extends('layouts.admin')

@section('title', 'Gestión de Horarios')
@section('page-title', 'Gestión de Horarios')
@section('page-subtitle', 'Administración de turnos, aulas, docentes y materias')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Listado de horarios</h2>
            <p class="mt-1 text-sm text-slate-500">
                Organiza los horarios académicos evitando cruces de aula y docente.
            </p>
        </div>

        <a href="{{ route('admin.horarios.create') }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Nuevo horario
        </a>
    </div>

    <form id="horarios-search-form" method="GET" action="{{ route('admin.horarios.index') }}" class="mt-6 grid gap-4 md:grid-cols-4">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input type="text"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Aula, docente, carrera o materia"
                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700">Día</label>
            <select name="dia"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                <option value="">Todos</option>
                @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
                    <option value="{{ $dia }}" @selected(request('dia') === $dia)>{{ $dia }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700">Turno</label>
            <select name="turno"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                <option value="">Todos</option>
                @foreach (['Mañana', 'Tarde', 'Noche'] as $turno)
                    <option value="{{ $turno }}" @selected(request('turno') === $turno)>{{ $turno }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end gap-3 md:col-span-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <a href="{{ route('admin.horarios.index') }}"
               class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Limpiar
            </a>
        </div>
    </form>
</div>

<div id="horarios-list" class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Día</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Horario</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Materia</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Docente</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Aula</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($horarios as $horario)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100">
                                {{ $horario->dia }}
                            </span>
                            <p class="mt-1 text-xs text-slate-500">{{ $horario->turno }}</p>
                        </td>

                        <td class="px-6 py-4 text-sm font-bold text-slate-800">
                            {{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ $horario->carreraMateria->materia->nombre }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $horario->carreraMateria->carrera->nombre }}
                            </p>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $horario->docenteDetalle->user->nombreCompleto() }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $horario->aula->nombre }} - {{ $horario->aula->codigo }}
                        </td>

                        <td class="px-6 py-4">
                            @if ($horario->estado)
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
                                <a href="{{ route('admin.horarios.show', $horario) }}"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.horarios.edit', $horario) }}"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.horarios.destroy', $horario) }}"
                                      onsubmit="return confirm('¿Está seguro de cambiar el estado de este horario?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5
                                            {{ $horario->estado ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                        @if ($horario->estado)
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
                            No existen horarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 px-6 py-4">
        @if($horarios->total() > 10)
            {{ $horarios->links() }}
        @endif
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('horarios-search-form');
    const container = document.getElementById('horarios-list');
    if (!form || !container) return;

    const searchFields = Array.from(form.querySelectorAll('input[name="buscar"], select[name="dia"], select[name="turno"]'));
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
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Aula</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Carrera</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Materia</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Periodo</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Docente</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">No existen horarios registrados.</td>
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
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Aula</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Carrera</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Materia</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Periodo</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Docente</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        ${data.map(horario => `
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-semibold text-slate-700">${escapeHtml(horario.aula_codigo)} - ${escapeHtml(horario.aula_nombre)}</td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-900">${escapeHtml(horario.carrera_nombre)}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">${escapeHtml(horario.materia_nombre)}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">${escapeHtml(horario.periodo_nombre)}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">${escapeHtml(horario.docente_nombre || 'No asignado')}</td>
                                <td class="px-6 py-4">${horario.estado ? '<span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 ring-1 ring-green-100">Activo</span>' : '<span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 ring-1 ring-red-100">Inactivo</span>'}</td>
                                <td class="px-6 py-4"><div class="flex flex-wrap items-center gap-2"><a href="/admin/horarios/${horario.id}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200"><i class="fa-solid fa-eye text-sm"></i></a><a href="/admin/horarios/${horario.id}/edit" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100"><i class="fa-solid fa-pen-to-square text-sm"></i></a></div></td>
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
                container.innerHTML = `<div class="px-6 py-12 text-center text-sm text-red-500">Error al cargar los horarios.</div>`;
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
        field.addEventListener('change', function () {
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