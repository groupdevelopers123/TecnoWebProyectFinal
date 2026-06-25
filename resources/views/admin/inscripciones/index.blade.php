@extends('layouts.admin')

@section('title', 'Gestión de Inscripciones')
@section('page-title', 'Gestión de Inscripciones')
@section('page-subtitle', 'Administración de inscripciones académicas del instituto')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Listado de inscripciones</h2>
            <p class="mt-1 text-sm text-slate-500">
                Registra, busca, edita o elimina inscripciones académicas.
            </p>
        </div>

        <a href="{{ route('admin.inscripciones.create') }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Nueva inscripción
        </a>
    </div>

    <form method="GET" action="{{ route('admin.inscripciones.index') }}" class="mt-6 grid gap-4 md:grid-cols-3" onsubmit="return false;">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input type="text"
                   id="buscar-inscripciones"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Alumno, CI, oferta, carrera, periodo u observación"
                   autocomplete="off"
                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        </div>

        <div class="flex items-end gap-3">
            <button type="button"
                    onclick="buscarInscripciones()"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <button type="button"
                    onclick="limpiarBusquedaInscripciones()"
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
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Oferta</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Carrera</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Periodo</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Fecha</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody id="inscripciones-tbody" class="divide-y divide-slate-100 bg-white">
                @forelse ($inscripciones as $inscripcion)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-cyan-100 text-cyan-700">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ $inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $inscripcion->alumnoDetalle->codigo ?? 'SIN-COD' }}
                                        @if ($inscripcion->alumnoDetalle->user?->ci)
                                            / CI: {{ $inscripcion->alumnoDetalle->user->ci }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $inscripcion->ofertaAcademica->nombre }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $inscripcion->ofertaAcademica->carrera->codigo }}
                            -
                            {{ $inscripcion->ofertaAcademica->carrera->nombre }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700 ring-1 ring-cyan-100">
                                {{ $inscripcion->ofertaAcademica->periodoAcademico->nombre }}
                                {{ $inscripcion->ofertaAcademica->periodoAcademico->gestion }}
                                /
                                Periodo {{ $inscripcion->periodo_numero }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $inscripcion->fecha_inscripcion?->format('d/m/Y') }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                               <button type="button"
                                        onclick="abrirModalInscripcionMateria('{{ route('admin.inscripciones.materias.index', $inscripcion) }}')"
                                        title="Gestionar materias inscritas"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition hover:-translate-y-0.5 hover:bg-emerald-100">
                                    <i class="fa-solid fa-book-open-reader text-sm"></i>
                                </button>

                                <a href="{{ route('admin.inscripciones.show', $inscripcion) }}"
                                   title="Ver inscripción"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.inscripciones.edit', $inscripcion) }}"
                                   title="Editar inscripción"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.inscripciones.destroy', $inscripcion) }}"
                                      onsubmit="return confirm('¿Está seguro de eliminar esta inscripción? Se devolverá un cupo a la oferta académica.')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="Eliminar inscripción"
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
                            No se encontraron inscripciones registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="inscripciones-pagination" class="border-t border-slate-100 px-6 py-4">
        {{ $inscripciones->links() }}
    </div>
</div>

<div id="modal-inscripcion-materia"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">

    <div class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl">
        <div class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-5">
            <div>
                <h2 class="text-xl font-black text-slate-900">Materias de la inscripción</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Gestión de materias inscritas del alumno.
                </p>
            </div>

            <button type="button"
                    onclick="cerrarModalInscripcionMateria()"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-red-50 hover:text-red-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div id="contenido-modal-inscripcion-materia" class="p-6">
            <div class="py-12 text-center text-sm text-slate-500">
                Cargando información...
            </div>
        </div>
    </div>
</div>


<script>
    const urlInscripciones = @json(route('admin.inscripciones.index'));
    const csrfTokenInscripcion = @json(csrf_token());

    const inputBuscarInscripciones = document.getElementById('buscar-inscripciones');
    const tbodyInscripciones = document.getElementById('inscripciones-tbody');
    const paginationInscripciones = document.getElementById('inscripciones-pagination');
    const modalInscripcionMateria = document.getElementById('modal-inscripcion-materia');
    const contenidoModalInscripcionMateria = document.getElementById('contenido-modal-inscripcion-materia');

    let timerInscripciones = null;

    inputBuscarInscripciones.addEventListener('input', function () {
        clearTimeout(timerInscripciones);

        timerInscripciones = setTimeout(() => {
            buscarInscripciones();
        }, 300);
    });

    function buscarInscripciones(url = null) {
        const urlFinal = new URL(url || urlInscripciones, window.location.origin);
        urlFinal.searchParams.set('buscar', inputBuscarInscripciones.value);

        tbodyInscripciones.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                    Buscando inscripciones...
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
                renderizarInscripciones(data.data);
                renderizarPaginacionInscripciones(data.pagination);
            })
            .catch(() => {
                tbodyInscripciones.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-red-500">
                            Error al realizar la búsqueda.
                        </td>
                    </tr>
                `;
            });
    }

    function limpiarBusquedaInscripciones() {
        inputBuscarInscripciones.value = '';
        buscarInscripciones();
    }

    function renderizarInscripciones(inscripciones) {
        if (!inscripciones.length) {
            tbodyInscripciones.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                        No se encontraron inscripciones registradas.
                    </td>
                </tr>
            `;
            return;
        }

        tbodyInscripciones.innerHTML = inscripciones.map(inscripcion => `
            <tr class="transition hover:bg-slate-50">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-cyan-100 text-cyan-700">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-slate-900">
                                ${escapeHtml(nombreCompletoAlumno(inscripcion))}
                            </p>
                            <p class="text-xs text-slate-500">
                                ${escapeHtml(inscripcion.alumno.codigo ?? 'SIN-COD')}
                                ${inscripcion.alumno.ci ? ' / CI: ' + escapeHtml(inscripcion.alumno.ci) : ''}
                            </p>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${escapeHtml(inscripcion.oferta_academica.nombre ?? 'Sin oferta')}
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${escapeHtml(inscripcion.oferta_academica.carrera.codigo ?? '')}
                    ${inscripcion.oferta_academica.carrera.nombre ? ' - ' + escapeHtml(inscripcion.oferta_academica.carrera.nombre) : ''}
                </td>

                <td class="px-6 py-4">
                    <span class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700 ring-1 ring-cyan-100">
                        ${escapeHtml(inscripcion.oferta_academica.periodo_academico.nombre ?? '')}
                        ${inscripcion.oferta_academica.periodo_academico.gestion ? ' ' + escapeHtml(inscripcion.oferta_academica.periodo_academico.gestion) : ''}
                        /
                        Periodo ${inscripcion.periodo_numero}
                    </span>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${formatearFecha(inscripcion.fecha_inscripcion)}
                </td>

                <td class="px-6 py-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="/admin/inscripciones/${inscripcion.id}"
                           title="Ver inscripción"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>

                        <a href="/admin/inscripciones/${inscripcion.id}/edit"
                           title="Editar inscripción"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </a>

                        <form method="POST"
                              action="/admin/inscripciones/${inscripcion.id}"
                              onsubmit="return confirm('¿Está seguro de eliminar esta inscripción? Se devolverá un cupo a la oferta académica.')">
                            <input type="hidden" name="_token" value="${csrfTokenInscripcion}">
                            <input type="hidden" name="_method" value="DELETE">

                            <button type="submit"
                                    title="Eliminar inscripción"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderizarPaginacionInscripciones(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            paginationInscripciones.innerHTML = '';
            return;
        }

        paginationInscripciones.innerHTML = `
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm text-slate-500">
                    Página ${pagination.current_page} de ${pagination.last_page}
                    — ${pagination.total} registros
                </p>

                <div class="flex gap-2">
                    <button type="button"
                            ${!pagination.prev_page_url ? 'disabled' : ''}
                            onclick="buscarInscripciones('${pagination.prev_page_url}')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition
                            ${pagination.prev_page_url
                                ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                : 'bg-slate-50 text-slate-300'}">
                        Anterior
                    </button>

                    <button type="button"
                            ${!pagination.next_page_url ? 'disabled' : ''}
                            onclick="buscarInscripciones('${pagination.next_page_url}')"
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

    function nombreCompletoAlumno(inscripcion) {
        const nombres = inscripcion.alumno.nombres ?? '';
        const apellidos = inscripcion.alumno.apellidos ?? '';
        const completo = `${nombres} ${apellidos}`.trim();

        return completo || 'Alumno sin usuario';
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

    function abrirModalInscripcionMateria(url) {
        modalInscripcionMateria.classList.remove('hidden');
        modalInscripcionMateria.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        cargarContenidoInscripcionMateria(url);
    }

    function cerrarModalInscripcionMateria() {
        modalInscripcionMateria.classList.add('hidden');
        modalInscripcionMateria.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');

        contenidoModalInscripcionMateria.innerHTML = `
            <div class="py-12 text-center text-sm text-slate-500">
                Cargando información...
            </div>
        `;
    }

    function cargarContenidoInscripcionMateria(url) {
        contenidoModalInscripcionMateria.innerHTML = `
            <div class="py-12 text-center text-sm text-slate-500">
                Cargando información...
            </div>
        `;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
            .then(response => response.text())
            .then(html => {
                contenidoModalInscripcionMateria.innerHTML = html;
            })
            .catch(() => {
                contenidoModalInscripcionMateria.innerHTML = `
                    <div class="rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                        Error al cargar la información.
                    </div>
                `;
            });
    }

    document.addEventListener('click', function (event) {
        const link = event.target.closest('[data-modal-link]');

        if (!link) {
            return;
        }

        event.preventDefault();

        cargarContenidoInscripcionMateria(link.href);
    });

    document.addEventListener('submit', function (event) {
        const form = event.target.closest('[data-modal-form]');

        if (!form) {
            return;
        }

        event.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
            .then(response => response.text())
            .then(html => {
                contenidoModalInscripcionMateria.innerHTML = html;
            })
            .catch(() => {
                contenidoModalInscripcionMateria.innerHTML = `
                    <div class="rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                        Error al procesar la solicitud.
                    </div>
                `;
            });
    });
</script>

@endsection