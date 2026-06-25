@extends('layouts.admin')

@section('title', 'Reportes y Estadísticas')
@section('page-title', 'Reportes y Estadísticas')
@section('page-subtitle', 'Análisis dinámico de inscripciones, pagos, créditos y cuotas')

@section('content')

<div class="space-y-6">

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.reportes.index') }}" class="grid gap-4 md:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">Fecha inicio</label>
                <input type="date"
                       name="inicio"
                       value="{{ $inicio }}"
                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">Fecha fin</label>
                <input type="date"
                       name="fin"
                       value="{{ $fin }}"
                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                    <i class="fa-solid fa-filter text-xs"></i>
                    Filtrar
                </button>
            </div>

            <div class="flex items-end">
                <a href="{{ route('admin.reportes.index') }}"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-black uppercase text-slate-400">Inscripciones</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">
                        {{ $estadisticas['total_inscripciones'] }}
                    </p>
                </div>

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-xl text-blue-700">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-black uppercase text-slate-400">Ingresos pagados</p>
                    <p class="mt-2 text-3xl font-black text-green-700">
                        Bs {{ number_format($estadisticas['total_pagos'], 2) }}
                    </p>
                </div>

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-green-50 text-xl text-green-700">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-black uppercase text-slate-400">Créditos activos</p>
                    <p class="mt-2 text-3xl font-black text-amber-700">
                        {{ $estadisticas['creditos_activos'] }}
                    </p>
                </div>

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-xl text-amber-700">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-black uppercase text-slate-400">Saldo pendiente</p>
                    <p class="mt-2 text-3xl font-black text-red-700">
                        Bs {{ number_format($estadisticas['saldo_pendiente'], 2) }}
                    </p>
                </div>

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-xl text-red-700">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>

    </div>

    <div class="grid gap-6 xl:grid-cols-2">

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-black text-slate-900">Pagos mensuales</h2>
                <p class="text-sm text-slate-500">Comparación entre pagos al contado y cuotas pagadas.</p>
            </div>

            <canvas id="chartPagosMensuales" height="130"></canvas>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-black text-slate-900">Inscripciones mensuales</h2>
                <p class="text-sm text-slate-500">Cantidad de inscripciones registradas por mes.</p>
            </div>

            <canvas id="chartInscripciones" height="130"></canvas>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-black text-slate-900">Créditos por estado</h2>
                <p class="text-sm text-slate-500">Distribución porcentual de créditos.</p>
            </div>

            <canvas id="chartCreditosEstado" height="130"></canvas>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-black text-slate-900">Cuotas por estado</h2>
                <p class="text-sm text-slate-500">Distribución de cuotas pendientes, pagadas o anuladas.</p>
            </div>

            <canvas id="chartCuotasEstado" height="130"></canvas>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="mb-5">
                <h2 class="text-lg font-black text-slate-900">Ojiva de inscripciones</h2>
                <p class="text-sm text-slate-500">Frecuencia acumulada de inscripciones por mes.</p>
            </div>

            <canvas id="chartOjiva" height="90"></canvas>
        </div>

    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">Exportar reportes</h2>
            <p class="mt-1 text-sm text-slate-500">
                Descarga reportes en PDF o Excel según el rango de fechas seleccionado.
            </p>
        </div>

        @php
            $query = ['inicio' => $inicio, 'fin' => $fin];
        @endphp

        <div class="grid gap-5 md:grid-cols-3">

            <div class="rounded-3xl border border-slate-200 p-5">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>

                <h3 class="font-black text-slate-900">Inscripciones</h3>
                <p class="mt-1 text-sm text-slate-500">Reporte de estudiantes inscritos.</p>

                <div class="mt-5 flex gap-2">
                    <a href="{{ route('admin.reportes.inscripciones.pdf', $query) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100">
                        <i class="fa-solid fa-file-pdf"></i>
                        PDF
                    </a>

                    <a href="{{ route('admin.reportes.inscripciones.excel', $query) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-green-50 px-4 py-2 text-xs font-bold text-green-700 transition hover:bg-green-100">
                        <i class="fa-solid fa-file-excel"></i>
                        Excel
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 p-5">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-green-50 text-green-700">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>

                <h3 class="font-black text-slate-900">Pagos</h3>
                <p class="mt-1 text-sm text-slate-500">Pagos al contado y cuotas pagadas.</p>

                <div class="mt-5 flex gap-2">
                    <a href="{{ route('admin.reportes.pagos.pdf', $query) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100">
                        <i class="fa-solid fa-file-pdf"></i>
                        PDF
                    </a>

                    <a href="{{ route('admin.reportes.pagos.excel', $query) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-green-50 px-4 py-2 text-xs font-bold text-green-700 transition hover:bg-green-100">
                        <i class="fa-solid fa-file-excel"></i>
                        Excel
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 p-5">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>

                <h3 class="font-black text-slate-900">Créditos</h3>
                <p class="mt-1 text-sm text-slate-500">Créditos, saldos y cuotas asociadas.</p>

                <div class="mt-5 flex gap-2">
                    <a href="{{ route('admin.reportes.creditos.pdf', $query) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100">
                        <i class="fa-solid fa-file-pdf"></i>
                        PDF
                    </a>

                    <a href="{{ route('admin.reportes.creditos.excel', $query) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-green-50 px-4 py-2 text-xs font-bold text-green-700 transition hover:bg-green-100">
                        <i class="fa-solid fa-file-excel"></i>
                        Excel
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const pagosMensuales = @json($pagosMensuales);
    const inscripcionesMensuales = @json($inscripcionesMensuales);
    const creditosPorEstado = @json($creditosPorEstado);
    const cuotasPorEstado = @json($cuotasPorEstado);
    const ojivaInscripciones = @json($ojivaInscripciones);

    const chartOptions = {
        responsive: true,
        plugins: {
            legend: {
                labels: {
                    font: {
                        weight: 'bold'
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    };

    new Chart(document.getElementById('chartPagosMensuales'), {
        type: 'bar',
        data: {
            labels: pagosMensuales.map(item => item.mes),
            datasets: [
                {
                    label: 'Pagos contado',
                    data: pagosMensuales.map(item => item.contado),
                    borderWidth: 1
                },
                {
                    label: 'Pagos cuotas',
                    data: pagosMensuales.map(item => item.cuotas),
                    borderWidth: 1
                }
            ]
        },
        options: chartOptions
    });

    new Chart(document.getElementById('chartInscripciones'), {
        type: 'bar',
        data: {
            labels: inscripcionesMensuales.map(item => item.mes),
            datasets: [
                {
                    label: 'Inscripciones',
                    data: inscripcionesMensuales.map(item => item.total),
                    borderWidth: 1
                }
            ]
        },
        options: chartOptions
    });

    new Chart(document.getElementById('chartCreditosEstado'), {
        type: 'pie',
        data: {
            labels: creditosPorEstado.map(item => item.estado),
            datasets: [
                {
                    label: 'Créditos',
                    data: creditosPorEstado.map(item => item.total),
                    borderWidth: 1
                }
            ]
        }
    });

    new Chart(document.getElementById('chartCuotasEstado'), {
        type: 'doughnut',
        data: {
            labels: cuotasPorEstado.map(item => item.estado),
            datasets: [
                {
                    label: 'Cuotas',
                    data: cuotasPorEstado.map(item => item.total),
                    borderWidth: 1
                }
            ]
        }
    });

    new Chart(document.getElementById('chartOjiva'), {
        type: 'line',
        data: {
            labels: ojivaInscripciones.map(item => item.mes),
            datasets: [
                {
                    label: 'Inscripciones acumuladas',
                    data: ojivaInscripciones.map(item => item.acumulado),
                    tension: 0.35,
                    fill: false,
                    borderWidth: 3
                }
            ]
        },
        options: chartOptions
    });
</script>

@endsection