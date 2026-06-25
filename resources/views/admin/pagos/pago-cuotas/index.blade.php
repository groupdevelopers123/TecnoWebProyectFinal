@extends('layouts.admin')

@section('title', 'Pagos de Cuotas')
@section('page-title', 'Pagos de Cuotas')
@section('page-subtitle', 'Listado general de cuotas generadas por créditos')

@section('content')

@include('admin.pagos.partials.nav')

@if (session('success'))
    <div class="mb-6 rounded-2xl border border-green-100 bg-green-50 p-4 text-sm font-bold text-green-700">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm font-bold text-red-700">
        {{ session('error') }}
    </div>
@endif

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900">Listado de cuotas</h2>
            <p class="mt-1 text-sm text-slate-500">
                Visualiza cuotas pendientes, pagadas o anuladas.
            </p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.pago-cuotas.index') }}" class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700">Buscar</label>
            <input type="text"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Alumno, CI, concepto, estado o método"
                   autocomplete="off"
                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        </div>

        <div class="flex items-end gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>

            <a href="{{ route('admin.pago-cuotas.index') }}"
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
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Cuota</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Monto</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Vencimiento</th>
                    <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500">Estado</th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-500">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($cuotas as $cuota)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ $cuota->credito->inscripcion->alumnoDetalle->user?->nombreCompleto() ?? 'Alumno sin usuario' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                CI: {{ $cuota->credito->inscripcion->alumnoDetalle->user?->ci ?? 'No registrado' }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-800">
                                Cuota #{{ $cuota->numero_cuota }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $cuota->credito->conceptoPago->nombre ?? 'Sin concepto' }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100">
                                Bs {{ number_format($cuota->monto, 2) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $cuota->fecha_vencimiento?->format('d/m/Y') ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1
                                @if ($cuota->estado_cuota === 'pagado')
                                    bg-green-50 text-green-700 ring-green-100
                                @elseif ($cuota->estado_cuota === 'anulado' || $cuota->estado_cuota === 'fallido')
                                    bg-red-50 text-red-700 ring-red-100
                                @else
                                    bg-yellow-50 text-yellow-700 ring-yellow-100
                                @endif">
                                {{ ucfirst($cuota->estado_cuota) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.pago-cuotas.show', $cuota) }}"
                                   title="Ver detalle"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                @if ($cuota->estado_cuota !== 'pagado')
                                    <a href="{{ route('admin.pago-cuotas.edit', $cuota) }}"
                                       title="Pagar cuota"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition hover:-translate-y-0.5 hover:bg-emerald-100">
                                        <i class="fa-solid fa-money-bill-wave text-sm"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                            No se encontraron cuotas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 px-6 py-4">
        {{ $cuotas->links() }}
    </div>
</div>

@endsection