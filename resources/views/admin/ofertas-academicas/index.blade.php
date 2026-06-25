@extends('layouts.admin')

@section('title', 'Gestión de Ofertas Académicas')
@section('page-title', 'Gestión de Ofertas Académicas')
@section('page-subtitle', 'Administración de ofertas académicas del instituto')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Listado de ofertas académicas</h2>
            <p class="mt-1 text-sm text-slate-500">
                Registra, busca, edita o cambia el estado de las ofertas académicas.
            </p>
        </div>

        <a href="{{ route('admin.ofertas-academicas.create') }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Nueva oferta
        </a>
    </div>

    <form method="GET" action="{{ route('admin.ofertas-academicas.index') }}" class="mt-6 grid gap-4 md:grid-cols-3" onsubmit="return false;">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input type="text"
                   id="buscar-ofertas"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Oferta, carrera, código, periodo o gestión"
                   autocomplete="off"
                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        </div>

        <div class="flex items-end gap-3">
            <button type="button"
                    onclick="buscarOfertas()"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <button type="button"
                    onclick="limpiarBusquedaOfertas()"
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
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Oferta</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Carrera</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Periodo</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Docente</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Cupos</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Fechas</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody id="ofertas-tbody" class="divide-y divide-slate-100 bg-white">
                @forelse ($ofertas as $oferta)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-violet-100 text-violet-700">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $oferta->nombre }}</p>
                                    <p class="text-xs text-slate-500">Oferta académica</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $oferta->carrera->codigo }} - {{ $oferta->carrera->nombre }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $oferta->periodoAcademico->nombre }} - {{ $oferta->periodoAcademico->gestion }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            @if ($oferta->docenteDetalle)
                                {{ $oferta->docenteDetalle->codigo }} - {{ $oferta->docenteDetalle->user->nombres }} {{ $oferta->docenteDetalle->user->apellidos }}
                            @else
                                <span class="text-slate-400">No asignado</span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                                {{ $oferta->cupos_disponibles }} / {{ $oferta->cantidad_cupos }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $oferta->fecha_inicio?->format('d/m/Y') }}
                            -
                            {{ $oferta->fecha_fin?->format('d/m/Y') }}
                        </td>

                        <td class="px-6 py-4">
                            @if ($oferta->estado)
                                <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 ring-1 ring-green-100">
                                    Activa
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 ring-1 ring-red-100">
                                    Inactiva
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.ofertas-academicas.show', $oferta) }}"
                                   title="Ver oferta"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.ofertas-academicas.edit', $oferta) }}"
                                   title="Editar oferta"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.ofertas-academicas.destroy', $oferta) }}"
                                      onsubmit="return confirm('¿Está seguro de cambiar el estado de esta oferta académica?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="{{ $oferta->estado ? 'Desactivar oferta' : 'Activar oferta' }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5
                                            {{ $oferta->estado
                                                ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                                : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                        @if ($oferta->estado)
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
                            No se encontraron ofertas académicas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="ofertas-pagination" class="border-t border-slate-100 px-6 py-4">
        {{ $ofertas->links() }}
    </div>
</div>

<script>
    const urlOfertas = @json(route('admin.ofertas-academicas.index'));
    const csrfTokenOferta = @json(csrf_token());

    const inputBuscarOfertas = document.getElementById('buscar-ofertas');
    const tbodyOfertas = document.getElementById('ofertas-tbody');
    const paginationOfertas = document.getElementById('ofertas-pagination');

    let timerOfertas = null;

    inputBuscarOfertas.addEventListener('input', function () {
        clearTimeout(timerOfertas);

        timerOfertas = setTimeout(() => {
            buscarOfertas();
        }, 300);
    });

    function buscarOfertas(url = null) {
        const urlFinal = new URL(url || urlOfertas, window.location.origin);
        urlFinal.searchParams.set('buscar', inputBuscarOfertas.value);

        tbodyOfertas.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                    Buscando ofertas académicas...
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
                renderizarOfertas(data.data);
                renderizarPaginacionOfertas(data.pagination);
            })
            .catch(() => {
                tbodyOfertas.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-red-500">
                            Error al realizar la búsqueda.
                        </td>
                    </tr>
                `;
            });
    }

    function limpiarBusquedaOfertas() {
        inputBuscarOfertas.value = '';
        buscarOfertas();
    }

    function renderizarOfertas(ofertas) {
        if (!ofertas.length) {
            tbodyOfertas.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                        No se encontraron ofertas académicas registradas.
                    </td>
                </tr>
            `;
            return;
        }

        tbodyOfertas.innerHTML = ofertas.map(oferta => `
            <tr class="transition hover:bg-slate-50">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-violet-100 text-violet-700">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-slate-900">
                                ${escapeHtml(oferta.nombre)}
                            </p>
                            <p class="text-xs text-slate-500">Oferta académica</p>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${escapeHtml(oferta.carrera.codigo ?? '')}
                    ${oferta.carrera.nombre ? ' - ' + escapeHtml(oferta.carrera.nombre) : ''}
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${escapeHtml(oferta.periodo_academico.nombre ?? '')}
                    ${oferta.periodo_academico.gestion ? ' - ' + escapeHtml(oferta.periodo_academico.gestion) : ''}
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${oferta.docente_detalle ? 
                        escapeHtml(oferta.docente_detalle.codigo ?? '') + ' - ' + 
                        escapeHtml(oferta.docente_detalle.user?.nombres ?? '') + ' ' + 
                        escapeHtml(oferta.docente_detalle.user?.apellidos ?? '')
                        : '<span class="text-slate-400">No asignado</span>'
                    }
                </td>

                <td class="px-6 py-4">
                    <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                        ${oferta.cupos_disponibles} / ${oferta.cantidad_cupos}
                    </span>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${formatearFecha(oferta.fecha_inicio)}
                    -
                    ${formatearFecha(oferta.fecha_fin)}
                </td>

                <td class="px-6 py-4">
                    ${oferta.estado ? badgeActivoOferta() : badgeInactivoOferta()}
                </td>

                <td class="px-6 py-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="/admin/ofertas-academicas/${oferta.id}"
                           title="Ver oferta"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>

                        <a href="/admin/ofertas-academicas/${oferta.id}/edit"
                           title="Editar oferta"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </a>

                        <form method="POST"
                              action="/admin/ofertas-academicas/${oferta.id}"
                              onsubmit="return confirm('¿Está seguro de cambiar el estado de esta oferta académica?')">
                            <input type="hidden" name="_token" value="${csrfTokenOferta}">
                            <input type="hidden" name="_method" value="DELETE">

                            <button type="submit"
                                    title="${oferta.estado ? 'Desactivar oferta' : 'Activar oferta'}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5
                                    ${oferta.estado
                                        ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                        : 'bg-green-50 text-green-700 hover:bg-green-100'}">
                                ${oferta.estado
                                    ? '<i class="fa-solid fa-trash-can text-sm"></i>'
                                    : '<i class="fa-solid fa-circle-check text-sm"></i>'}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderizarPaginacionOfertas(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            paginationOfertas.innerHTML = '';
            return;
        }

        paginationOfertas.innerHTML = `
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm text-slate-500">
                    Página ${pagination.current_page} de ${pagination.last_page}
                    — ${pagination.total} registros
                </p>

                <div class="flex gap-2">
                    <button type="button"
                            ${!pagination.prev_page_url ? 'disabled' : ''}
                            onclick="buscarOfertas('${pagination.prev_page_url}')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition
                            ${pagination.prev_page_url
                                ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                : 'bg-slate-50 text-slate-300'}">
                        Anterior
                    </button>

                    <button type="button"
                            ${!pagination.next_page_url ? 'disabled' : ''}
                            onclick="buscarOfertas('${pagination.next_page_url}')"
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

    function badgeActivoOferta() {
        return `
            <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 ring-1 ring-green-100">
                Activa
            </span>
        `;
    }

    function badgeInactivoOferta() {
        return `
            <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 ring-1 ring-red-100">
                Inactiva
            </span>
        `;
    }

    function formatearFecha(fecha) {
        if (!fecha) {
            return '';
        }

        const partes = fecha.split('-');

        if (partes.length !== 3) {
            return fecha;
        }

        return `${partes[2]}/${partes[1]}/${partes[0]}`;
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