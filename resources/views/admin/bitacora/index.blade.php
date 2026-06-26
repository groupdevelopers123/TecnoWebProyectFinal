@extends('layouts.admin')

@section('title', 'Bitácora de Accesos')
@section('page-title', 'Bitácora de Accesos')
@section('page-subtitle', 'Registros de inicio de sesión y actividad dentro del sistema')

@section('content')
    <div class="space-y-5">

        {{-- Estadísticas principales --}}
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Logins aceptados
                        </p>

                        <p class="mt-4 text-4xl font-black text-slate-900">
                            {{ $loginAceptados }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Logins fallidos
                        </p>

                        <p class="mt-4 text-4xl font-black text-slate-900">
                            {{ $loginFallados }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-100 text-red-600">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Accesos a recursos
                        </p>

                        <p class="mt-4 text-4xl font-black text-slate-900">
                            {{ $recursosMasAccedidos->sum('total') }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600">
                        <i class="fa-solid fa-arrow-pointer"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- Recursos más accedidos --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500">
                        Recursos más accedidos
                    </p>

                    <h2 class="mt-2 text-xl font-black text-slate-900">
                        Actividad de los módulos
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Endpoints y recursos con mayor frecuencia de uso en el sistema.
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <i class="fa-solid fa-chart-column"></i>
                </div>
            </div>

            <div class="mt-6 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($recursosMasAccedidos as $recurso)
                    <div class="flex items-center justify-between gap-4 rounded-3xl border border-slate-100 bg-slate-50 px-4 py-4 transition hover:border-blue-200 hover:bg-blue-50/50">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-900">
                                {{ $recurso->recurso ?? 'Sin recurso' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Accesos registrados
                            </p>
                        </div>

                        <span class="inline-flex h-9 min-w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 px-3 text-sm font-black text-blue-700">
                            {{ $recurso->total }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center md:col-span-2 xl:col-span-3">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <i class="fa-solid fa-chart-simple"></i>
                        </div>

                        <p class="mt-4 text-sm font-bold text-slate-700">
                            No hay datos de acceso
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Los recursos utilizados aparecerán en esta sección.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Últimos eventos --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 p-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500">
                        Últimos eventos
                    </p>

                    <h2 class="mt-2 text-xl font-black text-slate-900">
                        Historial de actividad
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Registro reciente de inicios de sesión y accesos al sistema.
                    </p>
                </div>

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-[0.14em] text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap px-6 py-4">
                                Fecha
                            </th>

                            <th class="whitespace-nowrap px-6 py-4">
                                Tipo
                            </th>

                            <th class="whitespace-nowrap px-6 py-4">
                                Estado
                            </th>

                            <th class="whitespace-nowrap px-6 py-4">
                                Recurso
                            </th>

                            <th class="whitespace-nowrap px-6 py-4">
                                Usuario
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($ultimosEventos as $evento)
                            @php
                                $estado = strtolower($evento->estado ?? '');
                            @endphp

                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4 text-slate-600">
                                    <div class="font-semibold text-slate-900">
                                        {{ $evento->created_at->format('d/m/Y') }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $evento->created_at->format('H:i') }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                        {{ ucfirst($evento->tipo ?? 'Evento') }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    @if (in_array($estado, ['aceptado', 'exitoso', 'éxito', 'success']))
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            {{ ucfirst($evento->estado) }}
                                        </span>
                                    @elseif (in_array($estado, ['fallido', 'rechazado', 'error', 'failed']))
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            {{ ucfirst($evento->estado) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            {{ ucfirst($evento->estado ?? 'Sin estado') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="max-w-xs px-6 py-4">
                                    <span class="block truncate text-slate-600"
                                          title="{{ $evento->recurso ?? 'Sin recurso' }}">
                                        {{ $evento->recurso ?? 'Sin recurso' }}
                                    </span>
                                </td>

                                <td class="max-w-xs px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-black uppercase text-slate-600">
                                            {{ mb_substr($evento->email ?? $evento->user?->email ?? 'A', 0, 1) }}
                                        </div>

                                        <span class="block truncate font-medium text-slate-700"
                                              title="{{ $evento->email ?? $evento->user?->email ?? 'Anónimo' }}">
                                            {{ $evento->email ?? $evento->user?->email ?? 'Anónimo' }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                        <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                                    </div>

                                    <p class="mt-4 text-sm font-bold text-slate-700">
                                        No hay eventos registrados
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        La actividad reciente aparecerá en esta tabla.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection