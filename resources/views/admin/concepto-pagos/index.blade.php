@extends('layouts.admin')

@section('title', 'Conceptos de Pago')
@section('page-title', 'Conceptos de Pago')
@section('page-subtitle', 'Administración de conceptos de pago del instituto')

@section('content')

@include('admin.pagos.partials.nav')

@if (session('success'))
    <div class="mb-6 rounded-2xl border border-green-100 bg-green-50 p-4 text-sm font-bold text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Listado de conceptos de pago</h2>
            <p class="mt-1 text-sm text-slate-500">
                Registra, busca, edita o cambia el estado de los conceptos de pago.
            </p>
        </div>

        <a href="{{ route('admin.concepto-pagos.create') }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Nuevo concepto
        </a>
    </div>

    <form method="GET" action="{{ route('admin.concepto-pagos.index') }}" class="mt-6 grid gap-4 md:grid-cols-3" onsubmit="return false;">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input type="text"
                   id="buscar-conceptos"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Nombre, descripción o estado"
                   autocomplete="off"
                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        </div>

        <div class="flex items-end gap-3">
            <button type="button"
                    onclick="buscarConceptos()"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <button type="button"
                    onclick="limpiarBusquedaConceptos()"
                    class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                Limpiar
            </button>
        </div>
    </form>
</div>

<div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                        Concepto
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                        Descripción
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                        Estado
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                        Acciones
                    </th>
                </tr>
            </thead>

            <tbody id="conceptos-tbody" class="divide-y divide-slate-100 bg-white">
                @forelse ($conceptos as $concepto)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                    <i class="fa-solid fa-tags"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ $concepto->nombre }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Concepto #{{ $concepto->id }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $concepto->descripcion ?: 'Sin descripción' }}
                        </td>

                        <td class="px-6 py-4">
                            @if ($concepto->estado === 'Activo')
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
                                <a href="{{ route('admin.concepto-pagos.show', $concepto) }}"
                                   title="Ver concepto"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.concepto-pagos.edit', $concepto) }}"
                                   title="Editar concepto"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.concepto-pagos.destroy', $concepto) }}"
                                      onsubmit="return confirm('¿Está seguro de cambiar el estado de este concepto de pago?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="{{ $concepto->estado === 'Activo' ? 'Desactivar concepto' : 'Activar concepto' }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5
                                            {{ $concepto->estado === 'Activo'
                                                ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                                : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                        @if ($concepto->estado === 'Activo')
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
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                            No se encontraron conceptos de pago registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="conceptos-pagination" class="border-t border-slate-100 px-6 py-4">
        {{ $conceptos->links() }}
    </div>
</div>

<script>
    const urlConceptos = @json(route('admin.concepto-pagos.index'));
    const csrfTokenConcepto = @json(csrf_token());

    const inputBuscarConceptos = document.getElementById('buscar-conceptos');
    const tbodyConceptos = document.getElementById('conceptos-tbody');
    const paginationConceptos = document.getElementById('conceptos-pagination');

    let timerConceptos = null;

    inputBuscarConceptos.addEventListener('input', function () {
        clearTimeout(timerConceptos);

        timerConceptos = setTimeout(() => {
            buscarConceptos();
        }, 300);
    });

    function buscarConceptos(url = null) {
        const urlFinal = new URL(url || urlConceptos, window.location.origin);
        urlFinal.searchParams.set('buscar', inputBuscarConceptos.value);

        tbodyConceptos.innerHTML = `
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                    Buscando conceptos de pago...
                </td>
            </tr>
        `;

        fetch(urlFinal, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                renderizarConceptos(data.data);
                renderizarPaginacionConceptos(data.pagination);
            })
            .catch(() => {
                tbodyConceptos.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-red-500">
                            Error al realizar la búsqueda.
                        </td>
                    </tr>
                `;
            });
    }

    function limpiarBusquedaConceptos() {
        inputBuscarConceptos.value = '';
        buscarConceptos();
    }

    function renderizarConceptos(conceptos) {
        if (!conceptos.length) {
            tbodyConceptos.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                        No se encontraron conceptos de pago registrados.
                    </td>
                </tr>
            `;
            return;
        }

        tbodyConceptos.innerHTML = conceptos.map(concepto => `
            <tr class="transition hover:bg-slate-50">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            <i class="fa-solid fa-tags"></i>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-slate-900">
                                ${escapeHtml(concepto.nombre)}
                            </p>
                            <p class="text-xs text-slate-500">
                                Concepto #${concepto.id}
                            </p>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${concepto.descripcion ? escapeHtml(concepto.descripcion) : 'Sin descripción'}
                </td>

                <td class="px-6 py-4">
                    ${badgeEstadoConcepto(concepto.estado)}
                </td>

                <td class="px-6 py-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="/admin/concepto-pagos/${concepto.id}"
                           title="Ver concepto"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>

                        <a href="/admin/concepto-pagos/${concepto.id}/edit"
                           title="Editar concepto"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </a>

                        <form method="POST"
                              action="/admin/concepto-pagos/${concepto.id}"
                              onsubmit="return confirm('¿Está seguro de cambiar el estado de este concepto de pago?')">
                            <input type="hidden" name="_token" value="${csrfTokenConcepto}">
                            <input type="hidden" name="_method" value="DELETE">

                            <button type="submit"
                                    title="${concepto.estado === 'Activo' ? 'Desactivar concepto' : 'Activar concepto'}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5
                                    ${concepto.estado === 'Activo'
                                        ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                        : 'bg-green-50 text-green-700 hover:bg-green-100'}">
                                ${concepto.estado === 'Activo'
                                    ? '<i class="fa-solid fa-trash-can text-sm"></i>'
                                    : '<i class="fa-solid fa-circle-check text-sm"></i>'}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderizarPaginacionConceptos(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            paginationConceptos.innerHTML = '';
            return;
        }

        paginationConceptos.innerHTML = `
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm text-slate-500">
                    Página ${pagination.current_page} de ${pagination.last_page}
                    — ${pagination.total} registros
                </p>

                <div class="flex gap-2">
                    <button type="button"
                            ${!pagination.prev_page_url ? 'disabled' : ''}
                            onclick="buscarConceptos('${pagination.prev_page_url}')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition
                            ${pagination.prev_page_url
                                ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                : 'bg-slate-50 text-slate-300'}">
                        Anterior
                    </button>

                    <button type="button"
                            ${!pagination.next_page_url ? 'disabled' : ''}
                            onclick="buscarConceptos('${pagination.next_page_url}')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition
                            ${pagination.next_page_url
                                ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                : 'bg-slate-50 text-slate-300'}">
                        Siguiente
                    </button>
                </div>
            </div>
        `;
    }

    function badgeEstadoConcepto(estado) {
        if (estado === 'Activo') {
            return `
                <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 ring-1 ring-green-100">
                    Activo
                </span>
            `;
        }

        return `
            <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 ring-1 ring-red-100">
                Inactivo
            </span>
        `;
    }

    function escapeHtml(texto) {
        if (texto === null || texto === undefined) {
            return '';
        }

        return String(texto)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>

@endsection