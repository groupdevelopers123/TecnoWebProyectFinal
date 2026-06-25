@extends('layouts.admin')

@section('title', 'Gestión de Carreras')
@section('page-title', 'Gestión de Carreras')
@section('page-subtitle', 'Administración de carreras académicas del instituto')

@section('content')

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Listado de carreras</h2>
            <p class="mt-1 text-sm text-slate-500">
                Registra, busca, edita o cambia el estado de las carreras.
            </p>
        </div>

        <a href="{{ route('admin.carreras.create') }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Nueva carrera
        </a>
    </div>

    <form method="GET" action="{{ route('admin.carreras.index') }}" class="mt-6 grid gap-4 md:grid-cols-3" onsubmit="return false;">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input type="text"
                   id="buscar-carreras"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Código, nombre o régimen"
                   autocomplete="off"
                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        </div>

        <div class="flex items-end gap-3">
            <button type="button"
                    onclick="buscarCarreras()"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <button type="button"
                    onclick="limpiarBusquedaCarreras()"
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
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Código</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Carrera</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Duración</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Régimen</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody id="carreras-tbody" class="divide-y divide-slate-100 bg-white">
                @forelse ($carreras as $carrera)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm font-bold text-slate-700">
                            {{ $carrera->codigo }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $carrera->nombre }}</p>
                                    <p class="text-xs text-slate-500">Carrera académica</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $carrera->duracion ? $carrera->duracion . ' periodos' : 'No registrada' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $carrera->regimen_academico ?? 'No registrado' }}
                        </td>

                        <td class="px-6 py-4">
                            @if ($carrera->estado)
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
                                <button type="button"
                                        onclick="abrirModalMaterias({{ $carrera->id }})"
                                        title="Gestionar materias"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition hover:-translate-y-0.5 hover:bg-emerald-100">
                                    <i class="fa-solid fa-book-open text-sm"></i>
                                </button>

                                <a href="{{ route('admin.carreras.show', $carrera) }}"
                                   title="Ver carrera"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.carreras.edit', $carrera) }}"
                                   title="Editar carrera"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.carreras.destroy', $carrera) }}"
                                      onsubmit="return confirm('¿Está seguro de cambiar el estado de esta carrera?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="{{ $carrera->estado ? 'Desactivar carrera' : 'Activar carrera' }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5
                                            {{ $carrera->estado
                                                ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                                : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                        @if ($carrera->estado)
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
                            No se encontraron carreras registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="carreras-pagination" class="border-t border-slate-100 px-6 py-4">
        {{ $carreras->links() }}
    </div>
</div>

<div id="modales-carreras">
    @foreach ($carreras as $carrera)
        <div id="modal-materias-{{ $carrera->id }}"
             class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">

            <div class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-5">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">
                            Materias de {{ $carrera->nombre }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Código: {{ $carrera->codigo }} —
                            Régimen: {{ $carrera->regimen_academico ?? 'No registrado' }}
                        </p>
                    </div>

                    <button type="button"
                            onclick="cerrarModalMaterias({{ $carrera->id }})"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-red-50 hover:text-red-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-6">
                    <div class="mb-6 rounded-3xl border border-blue-100 bg-blue-50 p-5">
                        <h3 class="text-lg font-black text-slate-900">Agregar materia</h3>

                        <form method="POST"
                              action="{{ route('admin.carreras.materias.store', $carrera) }}"
                              class="mt-5 grid gap-4 md:grid-cols-3">
                            @csrf

                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">Materia</label>
                                <select name="materia_id"
                                        required
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                    <option value="">Seleccione una materia</option>

                                    @foreach ($materias as $materia)
                                        @php
                                            $yaAsignada = $carrera->carreraMaterias
                                                ->pluck('materia_id')
                                                ->contains($materia->id);
                                        @endphp

                                        @if (! $yaAsignada)
                                            <option value="{{ $materia->id }}">
                                                {{ $materia->codigo }} - {{ $materia->nombre }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700">
                                    @if ($carrera->regimen_academico === 'Anual')
                                        Año
                                    @elseif ($carrera->regimen_academico === 'Modular')
                                        Módulo
                                    @else
                                        Semestre
                                    @endif
                                </label>

                                <select name="periodo_numero"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                    <option value="">Seleccione</option>
                                    @for ($i = 1; $i <= ($carrera->duracion ?? 12); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    Agregar materia
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Código</th>
                                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Materia</th>
                                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Carga horaria</th>
                                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                                            @if ($carrera->regimen_academico === 'Anual')
                                                Año
                                            @elseif ($carrera->regimen_academico === 'Modular')
                                                Módulo
                                            @else
                                                Semestre
                                            @endif
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse ($carrera->carreraMaterias as $asignacion)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-bold text-slate-700">
                                                {{ $asignacion->materia->codigo }}
                                            </td>

                                            <td class="px-6 py-4 text-sm font-bold text-slate-900">
                                                {{ $asignacion->materia->nombre }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-slate-600">
                                                {{ $asignacion->materia->carga_horaria ? $asignacion->materia->carga_horaria . ' horas' : 'No registrada' }}
                                            </td>

                                            <td class="px-6 py-4">
                                                <form method="POST"
                                                      action="{{ route('admin.carreras.materias.update', [$carrera, $asignacion]) }}"
                                                      class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PUT')

                                                    <select name="periodo_numero"
                                                            class="w-24 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                                        <option value="">-</option>
                                                        @for ($i = 1; $i <= ($carrera->duracion ?? 12); $i++)
                                                            <option value="{{ $i }}" @selected($asignacion->periodo_numero == $i)>
                                                                {{ $i }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                            </td>

                                            <td class="px-6 py-4">
                                                    <div class="flex items-center gap-2">
                                                        <button type="submit"
                                                                title="Guardar cambios"
                                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                                            <i class="fa-solid fa-floppy-disk text-sm"></i>
                                                        </button>
                                                </form>

                                                <form method="POST"
                                                      action="{{ route('admin.carreras.materias.destroy', [$carrera, $asignacion]) }}"
                                                      onsubmit="return confirm('¿Está seguro de retirar esta materia de la carrera?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            title="Eliminar materia de la carrera"
                                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100">
                                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                                    </button>
                                                </form>
                                                    </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                                                Esta carrera todavía no tiene materias asignadas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
    const csrfToken = @json(csrf_token());
    const urlCarreras = @json(route('admin.carreras.index'));

    const inputBuscar = document.getElementById('buscar-carreras');
    const tbodyCarreras = document.getElementById('carreras-tbody');
    const divPaginacion = document.getElementById('carreras-pagination');
    const contenedorModales = document.getElementById('modales-carreras');

    let temporizador = null;
    let materiasGlobales = @json($materias);

    inputBuscar.addEventListener('input', function () {
        clearTimeout(temporizador);

        temporizador = setTimeout(() => {
            buscarCarreras();
        }, 300);
    });

    function buscarCarreras(url = null) {
        const urlFinal = new URL(url || urlCarreras, window.location.origin);
        urlFinal.searchParams.set('buscar', inputBuscar.value);

        tbodyCarreras.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                    Buscando carreras...
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
                materiasGlobales = data.materias;

                renderizarCarreras(data.data);
                renderizarModales(data.data);
                renderizarPaginacion(data.pagination);
            })
            .catch(() => {
                tbodyCarreras.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-red-500">
                            Error al realizar la búsqueda.
                        </td>
                    </tr>
                `;
            });
    }

    function limpiarBusquedaCarreras() {
        inputBuscar.value = '';
        buscarCarreras();
    }

    function renderizarCarreras(carreras) {
        if (!carreras.length) {
            tbodyCarreras.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                        No se encontraron carreras registradas.
                    </td>
                </tr>
            `;
            return;
        }

        tbodyCarreras.innerHTML = carreras.map(carrera => `
            <tr class="transition hover:bg-slate-50">
                <td class="px-6 py-4 text-sm font-bold text-slate-700">
                    ${escapeHtml(carrera.codigo)}
                </td>

                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-slate-900">${escapeHtml(carrera.nombre)}</p>
                            <p class="text-xs text-slate-500">Carrera académica</p>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${carrera.duracion ? carrera.duracion + ' periodos' : 'No registrada'}
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${carrera.regimen_academico ? escapeHtml(carrera.regimen_academico) : 'No registrado'}
                </td>

                <td class="px-6 py-4">
                    ${carrera.estado ? badgeActivo() : badgeInactivo()}
                </td>

                <td class="px-6 py-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                                onclick="abrirModalMaterias(${carrera.id})"
                                title="Gestionar materias"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition hover:-translate-y-0.5 hover:bg-emerald-100">
                            <i class="fa-solid fa-book-open text-sm"></i>
                        </button>

                        <a href="/admin/carreras/${carrera.id}"
                           title="Ver carrera"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>

                        <a href="/admin/carreras/${carrera.id}/edit"
                           title="Editar carrera"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </a>

                        <form method="POST"
                              action="/admin/carreras/${carrera.id}"
                              onsubmit="return confirm('¿Está seguro de cambiar el estado de esta carrera?')">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">

                            <button type="submit"
                                    title="${carrera.estado ? 'Desactivar carrera' : 'Activar carrera'}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5
                                    ${carrera.estado
                                        ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                        : 'bg-green-50 text-green-700 hover:bg-green-100'}">
                                ${carrera.estado
                                    ? '<i class="fa-solid fa-trash-can text-sm"></i>'
                                    : '<i class="fa-solid fa-circle-check text-sm"></i>'}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderizarModales(carreras) {
        contenedorModales.innerHTML = carreras.map(carrera => modalCarrera(carrera)).join('');
    }

    function modalCarrera(carrera) {
        const labelPeriodo = obtenerLabelPeriodo(carrera);
        const opcionesPeriodo = generarOpcionesPeriodo(carrera.duracion);
        const opcionesMaterias = generarOpcionesMaterias(carrera);

        return `
            <div id="modal-materias-${carrera.id}"
                 class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">

                <div class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl">
                    <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-5">
                        <div>
                            <h2 class="text-xl font-black text-slate-900">
                                Materias de ${escapeHtml(carrera.nombre)}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Código: ${escapeHtml(carrera.codigo)} —
                                Régimen: ${carrera.regimen_academico ? escapeHtml(carrera.regimen_academico) : 'No registrado'}
                            </p>
                        </div>

                        <button type="button"
                                onclick="cerrarModalMaterias(${carrera.id})"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-red-50 hover:text-red-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="mb-6 rounded-3xl border border-blue-100 bg-blue-50 p-5">
                            <h3 class="text-lg font-black text-slate-900">Agregar materia</h3>

                            <form method="POST"
                                  action="/admin/carreras/${carrera.id}/materias"
                                  class="mt-5 grid gap-4 md:grid-cols-3">
                                <input type="hidden" name="_token" value="${csrfToken}">

                                <div>
                                    <label class="mb-2 block text-sm font-bold text-slate-700">Materia</label>
                                    <select name="materia_id"
                                            required
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                        <option value="">Seleccione una materia</option>
                                        ${opcionesMaterias}
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-bold text-slate-700">${labelPeriodo}</label>
                                    <select name="periodo_numero"
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                        <option value="">Seleccione</option>
                                        ${opcionesPeriodo}
                                    </select>
                                </div>

                                <div class="flex items-end">
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                        Agregar materia
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Código</th>
                                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Materia</th>
                                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Carga horaria</th>
                                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">${labelPeriodo}</th>
                                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        ${filasMateriasAsignadas(carrera)}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function filasMateriasAsignadas(carrera) {
        const labelOpciones = generarOpcionesPeriodo(carrera.duracion);

        if (!carrera.materias_asignadas.length) {
            return `
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                        Esta carrera todavía no tiene materias asignadas.
                    </td>
                </tr>
            `;
        }

        return carrera.materias_asignadas.map(asignacion => `
            <tr>
                <td class="px-6 py-4 text-sm font-bold text-slate-700">
                    ${escapeHtml(asignacion.materia.codigo)}
                </td>

                <td class="px-6 py-4 text-sm font-bold text-slate-900">
                    ${escapeHtml(asignacion.materia.nombre)}
                </td>

                <td class="px-6 py-4 text-sm text-slate-600">
                    ${asignacion.materia.carga_horaria ? asignacion.materia.carga_horaria + ' horas' : 'No registrada'}
                </td>

                <td class="px-6 py-4">
                    <form method="POST"
                          action="/admin/carreras/${carrera.id}/materias/${asignacion.id}"
                          class="flex items-center gap-2">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="PUT">

                        <select name="periodo_numero"
                                class="w-24 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <option value="">-</option>
                            ${generarOpcionesPeriodo(carrera.duracion, asignacion.periodo_numero)}
                        </select>
                </td>

                <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <button type="submit"
                                    title="Guardar cambios"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                <i class="fa-solid fa-floppy-disk text-sm"></i>
                            </button>
                    </form>

                    <form method="POST"
                          action="/admin/carreras/${carrera.id}/materias/${asignacion.id}"
                          onsubmit="return confirm('¿Está seguro de retirar esta materia de la carrera?')">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">

                        <button type="submit"
                                title="Eliminar materia de la carrera"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </form>
                        </div>
                </td>
            </tr>
        `).join('');
    }

    function generarOpcionesMaterias(carrera) {
        const idsAsignadas = carrera.materias_asignadas.map(asignacion => asignacion.materia_id);

        return materiasGlobales
            .filter(materia => !idsAsignadas.includes(materia.id))
            .map(materia => `
                <option value="${materia.id}">
                    ${escapeHtml(materia.codigo)} - ${escapeHtml(materia.nombre)}
                </option>
            `).join('');
    }

    function generarOpcionesPeriodo(duracion, seleccionado = null) {
        const limite = duracion || 12;
        let opciones = '';

        for (let i = 1; i <= limite; i++) {
            opciones += `
                <option value="${i}" ${Number(seleccionado) === i ? 'selected' : ''}>
                    ${i}
                </option>
            `;
        }

        return opciones;
    }

    function obtenerLabelPeriodo(carrera) {
        if (carrera.regimen_academico === 'Anual') {
            return 'Año';
        }

        if (carrera.regimen_academico === 'Modular') {
            return 'Módulo';
        }

        return 'Semestre';
    }

    function renderizarPaginacion(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            divPaginacion.innerHTML = '';
            return;
        }

        divPaginacion.innerHTML = `
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm text-slate-500">
                    Página ${pagination.current_page} de ${pagination.last_page}
                    — ${pagination.total} registros
                </p>

                <div class="flex gap-2">
                    <button type="button"
                            ${!pagination.prev_page_url ? 'disabled' : ''}
                            onclick="buscarCarreras('${pagination.prev_page_url}')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition
                            ${pagination.prev_page_url
                                ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                : 'bg-slate-50 text-slate-300'}">
                        Anterior
                    </button>

                    <button type="button"
                            ${!pagination.next_page_url ? 'disabled' : ''}
                            onclick="buscarCarreras('${pagination.next_page_url}')"
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

    function abrirModalMaterias(idCarrera) {
        const modal = document.getElementById(`modal-materias-${idCarrera}`);

        if (!modal) return;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function cerrarModalMaterias(idCarrera) {
        const modal = document.getElementById(`modal-materias-${idCarrera}`);

        if (!modal) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    function badgeActivo() {
        return `
            <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 ring-1 ring-green-100">
                Activa
            </span>
        `;
    }

    function badgeInactivo() {
        return `
            <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 ring-1 ring-red-100">
                Inactiva
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

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('[id^="modal-materias-"]').forEach(function (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });

            document.body.classList.remove('overflow-hidden');
        }
    });

    document.addEventListener('click', function (event) {
        if (event.target.id && event.target.id.startsWith('modal-materias-')) {
            event.target.classList.add('hidden');
            event.target.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    });
</script>

@endsection