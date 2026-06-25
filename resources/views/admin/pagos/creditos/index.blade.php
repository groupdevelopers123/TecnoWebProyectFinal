@extends('layouts.admin')

@section('title', 'Créditos')
@section('page-title', 'Créditos')
@section('page-subtitle', 'Administración de créditos académicos')

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
            <h2 class="text-xl font-black text-slate-900">Listado de créditos</h2>
            <p class="mt-1 text-sm text-slate-500">
                Registra, busca, edita o cambia el estado de los créditos.
            </p>
        </div>

        <a href="{{ route('admin.creditos.create') }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Nuevo crédito
        </a>
    </div>

    <form method="GET" action="{{ route('admin.creditos.index') }}" class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input type="text"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Alumno, CI, concepto, carrera o estado"
                   autocomplete="off"
                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        </div>

        <div class="flex items-end gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <a href="{{ route('admin.creditos.index') }}"
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
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Alumno</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Concepto</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Monto</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Saldo</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Cuotas</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($creditos as $credito)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ $credito->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                CI: {{ $credito->inscripcion->alumnoDetalle->user?->ci ?? 'No registrado' }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-800">
                                {{ $credito->conceptoPago->nombre ?? 'Sin concepto' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $credito->inscripcion->ofertaAcademica->carrera->nombre ?? 'Sin carrera' }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100">
                                Bs {{ number_format($credito->monto_total, 2) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-100">
                                Bs {{ number_format($credito->saldo_pendiente, 2) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm font-bold text-slate-600">
                            {{ $credito->cantidad_cuotas }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1
                                @if ($credito->estado === 'pagado')
                                    bg-green-50 text-green-700 ring-green-100
                                @elseif ($credito->estado === 'anulado')
                                    bg-red-50 text-red-700 ring-red-100
                                @elseif ($credito->estado === 'activo')
                                    bg-blue-50 text-blue-700 ring-blue-100
                                @else
                                    bg-yellow-50 text-yellow-700 ring-yellow-100
                                @endif">
                                {{ ucfirst($credito->estado) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button"
                                        onclick="abrirModalCuotas('{{ route('admin.creditos.cuotas.index', $credito) }}')"
                                        title="Ver cuotas del crédito"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700 transition hover:-translate-y-0.5 hover:bg-amber-100">
                                    <i class="fa-solid fa-calendar-check text-sm"></i>
                                </button>
                                <a href="{{ route('admin.creditos.show', $credito) }}"
                                   title="Ver crédito"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a href="{{ route('admin.creditos.edit', $credito) }}"
                                   title="Editar crédito"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.creditos.destroy', $credito) }}"
                                      onsubmit="return confirm('¿Está seguro de cambiar el estado de este crédito?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="Anular o reactivar crédito"
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
                            No se encontraron créditos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 px-6 py-4">
        {{ $creditos->links() }}
    </div>
</div>

<div id="modal-cuotas-credito"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">

    <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl">
        <div class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-5">
            <div>
                <h2 class="text-xl font-black text-slate-900">Cuotas del crédito</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Detalle de cuotas generadas automáticamente.
                </p>
            </div>

            <button type="button"
                    onclick="cerrarModalCuotas()"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-red-50 hover:text-red-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div id="contenido-modal-cuotas" class="p-6">
            <div class="py-12 text-center text-sm text-slate-500">
                Cargando cuotas...
            </div>
        </div>
    </div>
</div>

<script>
    const modalCuotasCredito = document.getElementById('modal-cuotas-credito');
    const contenidoModalCuotas = document.getElementById('contenido-modal-cuotas');

    function abrirModalCuotas(url) {
        modalCuotasCredito.classList.remove('hidden');
        modalCuotasCredito.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        contenidoModalCuotas.innerHTML = `
            <div class="py-12 text-center text-sm text-slate-500">
                Cargando cuotas...
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
                contenidoModalCuotas.innerHTML = html;
            })
            .catch(() => {
                contenidoModalCuotas.innerHTML = `
                    <div class="rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                        Error al cargar las cuotas del crédito.
                    </div>
                `;
            });
    }

    function cerrarModalCuotas() {
        modalCuotasCredito.classList.add('hidden');
        modalCuotasCredito.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }
</script>

@endsection