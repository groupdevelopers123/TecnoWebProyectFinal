@extends('layouts.admin')

@section('title', 'Pagos al Contado')
@section('page-title', 'Pagos al Contado')
@section('page-subtitle', 'Administración de pagos al contado y QR PagoFácil')

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
            <h2 class="text-xl font-black text-slate-900">Listado de pagos al contado</h2>
            <p class="mt-1 text-sm text-slate-500">
                Registra pagos en efectivo, transferencia o QR PagoFácil.
            </p>
        </div>

        <a href="{{ route('admin.pago-contados.create') }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Nuevo pago
        </a>
    </div>

    <form method="GET" action="{{ route('admin.pago-contados.index') }}" class="mt-6 grid gap-4 md:grid-cols-3" onsubmit="return false;">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input type="text"
                   id="buscar-pagos"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Alumno, CI, concepto, método, estado, transacción"
                   autocomplete="off"
                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        </div>

        <div class="flex items-end gap-3">
            <button type="button"
                    onclick="buscarPagos()"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <button type="button"
                    onclick="limpiarBusquedaPagos()"
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
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Alumno</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Concepto</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Monto</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Método</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Fecha</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody id="pagos-tbody" class="divide-y divide-slate-100 bg-white">
                @forelse ($pagos as $pago)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ $pago->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                CI: {{ $pago->inscripcion->alumnoDetalle->user?->ci ?? 'No registrado' }}
                            </p>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $pago->conceptoPago->nombre }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                                Bs {{ number_format($pago->monto_pagado, 2) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $pago->metodo_pago }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1
                                @if ($pago->estado === 'Confirmado')
                                    bg-green-50 text-green-700 ring-green-100
                                @elseif ($pago->estado === 'Anulado' || $pago->estado === 'Fallido')
                                    bg-red-50 text-red-700 ring-red-100
                                @else
                                    bg-yellow-50 text-yellow-700 ring-yellow-100
                                @endif">
                                {{ $pago->estado }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $pago->fecha_pago?->format('d/m/Y') }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.pago-contados.show', $pago) }}"
                                   title="Ver pago"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.pago-contados.edit', $pago) }}"
                                   title="Editar pago"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.pago-contados.destroy', $pago) }}"
                                      onsubmit="return confirm('¿Está seguro de cambiar el estado de este pago?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="Cambiar estado"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100">
                                        <i class="fa-solid fa-ban text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                            No se encontraron pagos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="pagos-pagination" class="border-t border-slate-100 px-6 py-4">
        {{ $pagos->links() }}
    </div>
</div>

<script>
    const urlPagos = @json(route('admin.pago-contados.index'));
    const csrfTokenPago = @json(csrf_token());

    const inputBuscarPagos = document.getElementById('buscar-pagos');
    const tbodyPagos = document.getElementById('pagos-tbody');
    const paginationPagos = document.getElementById('pagos-pagination');

    let timerPagos = null;

    inputBuscarPagos.addEventListener('input', function () {
        clearTimeout(timerPagos);

        timerPagos = setTimeout(() => {
            buscarPagos();
        }, 300);
    });

    function buscarPagos(url = null) {
        const urlFinal = new URL(url || urlPagos, window.location.origin);
        urlFinal.searchParams.set('buscar', inputBuscarPagos.value);

        tbodyPagos.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                    Buscando pagos...
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
                renderizarPagos(data.data);
                renderizarPaginacionPagos(data.pagination);
            })
            .catch(() => {
                tbodyPagos.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-red-500">
                            Error al realizar la búsqueda.
                        </td>
                    </tr>
                `;
            });
    }

    function limpiarBusquedaPagos() {
        inputBuscarPagos.value = '';
        buscarPagos();
    }

    function renderizarPagos(pagos) {
        if (!pagos.length) {
            tbodyPagos.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                        No se encontraron pagos registrados.
                    </td>
                </tr>
            `;
            return;
        }

        tbodyPagos.innerHTML = pagos.map(pago => `
            <tr class="transition hover:bg-slate-50">
                <td class="px-6 py-4">
                    <p class="text-sm font-bold text-slate-900">
                        ${escapeHtml(nombreCompleto(pago.alumno.nombres, pago.alumno.apellidos))}
                    </p>
                    <p class="text-xs text-slate-500">
                        CI: ${escapeHtml(pago.alumno.ci ?? 'No registrado')}
                    </p>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${escapeHtml(pago.concepto_pago.nombre ?? 'Sin concepto')}
                </td>

                <td class="px-6 py-4">
                    <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                        Bs ${Number(pago.monto_pagado).toFixed(2)}
                    </span>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${escapeHtml(pago.metodo_pago)}
                </td>

                <td class="px-6 py-4">
                    ${badgeEstadoPago(pago.estado)}
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${formatearFecha(pago.fecha_pago)}
                </td>

                <td class="px-6 py-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="/admin/pago-contados/${pago.id}"
                           title="Ver pago"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>

                        <a href="/admin/pago-contados/${pago.id}/edit"
                           title="Editar pago"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </a>

                        <form method="POST"
                              action="/admin/pago-contados/${pago.id}"
                              onsubmit="return confirm('¿Está seguro de cambiar el estado de este pago?')">
                            <input type="hidden" name="_token" value="${csrfTokenPago}">
                            <input type="hidden" name="_method" value="DELETE">

                            <button type="submit"
                                    title="Cambiar estado"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100">
                                <i class="fa-solid fa-ban text-sm"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderizarPaginacionPagos(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            paginationPagos.innerHTML = '';
            return;
        }

        paginationPagos.innerHTML = `
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm text-slate-500">
                    Página ${pagination.current_page} de ${pagination.last_page}
                    — ${pagination.total} registros
                </p>

                <div class="flex gap-2">
                    <button type="button"
                            ${!pagination.prev_page_url ? 'disabled' : ''}
                            onclick="buscarPagos('${pagination.prev_page_url}')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition
                            ${pagination.prev_page_url
                                ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                : 'bg-slate-50 text-slate-300'}">
                        Anterior
                    </button>

                    <button type="button"
                            ${!pagination.next_page_url ? 'disabled' : ''}
                            onclick="buscarPagos('${pagination.next_page_url}')"
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

    function badgeEstadoPago(estado) {
        let clases = 'bg-yellow-50 text-yellow-700 ring-yellow-100';

        if (estado === 'Confirmado') {
            clases = 'bg-green-50 text-green-700 ring-green-100';
        }

        if (estado === 'Anulado' || estado === 'Fallido') {
            clases = 'bg-red-50 text-red-700 ring-red-100';
        }

        return `
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 ${clases}">
                ${escapeHtml(estado)}
            </span>
        `;
    }

    function nombreCompleto(nombres, apellidos) {
        const completo = `${nombres ?? ''} ${apellidos ?? ''}`.trim();

        return completo || 'Sin nombre';
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