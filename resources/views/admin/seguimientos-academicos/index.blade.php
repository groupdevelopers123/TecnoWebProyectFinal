@extends('layouts.admin')

@section('title', 'Seguimiento Académico')
@section('page-title', 'Seguimiento Académico')
@section('page-subtitle', 'Administración del seguimiento académico de los alumnos')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Listado de seguimientos</h2>
            <p class="mt-1 text-sm text-slate-500">
                Registra, busca, edita o elimina seguimientos académicos.
            </p>
        </div>

        <a href="{{ route('admin.seguimientos-academicos.create') }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Nuevo seguimiento
        </a>
    </div>

    <form method="GET" action="{{ route('admin.seguimientos-academicos.index') }}" class="mt-6 grid gap-4 md:grid-cols-3" onsubmit="return false;">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input type="text"
                   id="buscar-seguimientos"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Alumno, docente, materia, carrera, estado u observación"
                   autocomplete="off"
                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        </div>

        <div class="flex items-end gap-3">
            <button type="button"
                    onclick="buscarSeguimientos()"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <button type="button"
                    onclick="limpiarBusquedaSeguimientos()"
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
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Materia</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Docente</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Nota / Asistencia</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Fecha</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody id="seguimientos-tbody" class="divide-y divide-slate-100 bg-white">
                @forelse ($seguimientos as $seguimiento)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ $seguimiento->inscripcionMateria->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                CI:
                                {{ $seguimiento->inscripcionMateria->inscripcion->alumnoDetalle->user?->ci ?? 'No registrado' }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ $seguimiento->inscripcionMateria->carreraMateria->materia->nombre }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $seguimiento->inscripcionMateria->inscripcion->ofertaAcademica->carrera->nombre }}
                            </p>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $seguimiento->docenteDetalle->user?->nombreCompleto() ?? 'Docente sin usuario' }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                Nota: {{ $seguimiento->nota_final ?? '-' }}
                            </span>

                            <span class="mt-1 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                                Asistencia: {{ $seguimiento->porcentaje_asistencia ?? '-' }}%
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1
                                @if ($seguimiento->estado_academico === 'Aprobado')
                                    bg-green-50 text-green-700 ring-green-100
                                @elseif ($seguimiento->estado_academico === 'Reprobado')
                                    bg-red-50 text-red-700 ring-red-100
                                @elseif ($seguimiento->estado_academico === 'Retirado')
                                    bg-yellow-50 text-yellow-700 ring-yellow-100
                                @else
                                    bg-blue-50 text-blue-700 ring-blue-100
                                @endif">
                                {{ $seguimiento->estado_academico }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $seguimiento->fecha_registro?->format('d/m/Y') }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.seguimientos-academicos.show', $seguimiento) }}"
                                   title="Ver seguimiento"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.seguimientos-academicos.edit', $seguimiento) }}"
                                   title="Editar seguimiento"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.seguimientos-academicos.destroy', $seguimiento) }}"
                                      onsubmit="return confirm('¿Está seguro de eliminar este seguimiento académico?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="Eliminar seguimiento"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                            No se encontraron seguimientos académicos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="seguimientos-pagination" class="border-t border-slate-100 px-6 py-4">
        {{ $seguimientos->links() }}
    </div>
</div>

<script>
    const urlSeguimientos = @json(route('admin.seguimientos-academicos.index'));
    const csrfTokenSeguimiento = @json(csrf_token());

    const inputBuscarSeguimientos = document.getElementById('buscar-seguimientos');
    const tbodySeguimientos = document.getElementById('seguimientos-tbody');
    const paginationSeguimientos = document.getElementById('seguimientos-pagination');

    let timerSeguimientos = null;

    inputBuscarSeguimientos.addEventListener('input', function () {
        clearTimeout(timerSeguimientos);

        timerSeguimientos = setTimeout(() => {
            buscarSeguimientos();
        }, 300);
    });

    function buscarSeguimientos(url = null) {
        const urlFinal = new URL(url || urlSeguimientos, window.location.origin);
        urlFinal.searchParams.set('buscar', inputBuscarSeguimientos.value);

        tbodySeguimientos.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                    Buscando seguimientos académicos...
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
                renderizarSeguimientos(data.data);
                renderizarPaginacionSeguimientos(data.pagination);
            })
            .catch(() => {
                tbodySeguimientos.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-red-500">
                            Error al realizar la búsqueda.
                        </td>
                    </tr>
                `;
            });
    }

    function limpiarBusquedaSeguimientos() {
        inputBuscarSeguimientos.value = '';
        buscarSeguimientos();
    }

    function renderizarSeguimientos(seguimientos) {
        if (!seguimientos.length) {
            tbodySeguimientos.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                        No se encontraron seguimientos académicos registrados.
                    </td>
                </tr>
            `;
            return;
        }

        tbodySeguimientos.innerHTML = seguimientos.map(seguimiento => `
            <tr class="transition hover:bg-slate-50">
                <td class="px-6 py-4">
                    <p class="text-sm font-bold text-slate-900">
                        ${escapeHtml(nombreCompleto(seguimiento.alumno.nombres, seguimiento.alumno.apellidos))}
                    </p>
                    <p class="text-xs text-slate-500">
                        CI: ${escapeHtml(seguimiento.alumno.ci ?? 'No registrado')}
                    </p>
                </td>

                <td class="px-6 py-4">
                    <p class="text-sm font-bold text-slate-900">
                        ${escapeHtml(seguimiento.materia.nombre ?? 'Sin materia')}
                    </p>
                    <p class="text-xs text-slate-500">
                        ${escapeHtml(seguimiento.carrera.nombre ?? 'Sin carrera')}
                    </p>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${escapeHtml(nombreCompleto(seguimiento.docente.nombres, seguimiento.docente.apellidos))}
                </td>

                <td class="px-6 py-4">
                    <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                        Nota: ${seguimiento.nota_final ?? '-'}
                    </span>

                    <span class="mt-1 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                        Asistencia: ${seguimiento.porcentaje_asistencia ?? '-'}%
                    </span>
                </td>

                <td class="px-6 py-4">
                    ${badgeEstadoAcademico(seguimiento.estado_academico)}
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${formatearFecha(seguimiento.fecha_registro)}
                </td>

                <td class="px-6 py-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="/admin/seguimientos-academicos/${seguimiento.id}"
                           title="Ver seguimiento"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>

                        <a href="/admin/seguimientos-academicos/${seguimiento.id}/edit"
                           title="Editar seguimiento"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </a>

                        <form method="POST"
                              action="/admin/seguimientos-academicos/${seguimiento.id}"
                              onsubmit="return confirm('¿Está seguro de eliminar este seguimiento académico?')">
                            <input type="hidden" name="_token" value="${csrfTokenSeguimiento}">
                            <input type="hidden" name="_method" value="DELETE">

                            <button type="submit"
                                    title="Eliminar seguimiento"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderizarPaginacionSeguimientos(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            paginationSeguimientos.innerHTML = '';
            return;
        }

        paginationSeguimientos.innerHTML = `
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm text-slate-500">
                    Página ${pagination.current_page} de ${pagination.last_page}
                    — ${pagination.total} registros
                </p>

                <div class="flex gap-2">
                    <button type="button"
                            ${!pagination.prev_page_url ? 'disabled' : ''}
                            onclick="buscarSeguimientos('${pagination.prev_page_url}')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition
                            ${pagination.prev_page_url
                                ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                : 'bg-slate-50 text-slate-300'}">
                        Anterior
                    </button>

                    <button type="button"
                            ${!pagination.next_page_url ? 'disabled' : ''}
                            onclick="buscarSeguimientos('${pagination.next_page_url}')"
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

    function badgeEstadoAcademico(estado) {
        let clases = 'bg-blue-50 text-blue-700 ring-blue-100';

        if (estado === 'Aprobado') {
            clases = 'bg-green-50 text-green-700 ring-green-100';
        }

        if (estado === 'Reprobado') {
            clases = 'bg-red-50 text-red-700 ring-red-100';
        }

        if (estado === 'Retirado') {
            clases = 'bg-yellow-50 text-yellow-700 ring-yellow-100';
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