<template>
    <Head title="Reportes y Estadísticas" />

    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <form
                @submit.prevent="aplicarFiltro"
                class="grid gap-4 md:grid-cols-4"
            >
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700"
                        >Fecha inicio</label
                    >
                    <input
                        v-model="form.inicio"
                        type="date"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700"
                        >Fecha fin</label
                    >
                    <input
                        v-model="form.fin"
                        type="date"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    />
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                    >
                        <i class="fa-solid fa-filter text-xs"></i>
                        Filtrar
                    </button>
                </div>

                <div class="flex items-end">
                    <button
                        type="button"
                        @click="limpiarFiltros"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                    >
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        Limpiar
                    </button>
                </div>
            </form>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div
                class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase text-slate-400">
                            Inscripciones
                        </p>
                        <p class="mt-2 text-3xl font-black text-slate-900">
                            {{ estadisticas.total_inscripciones }}
                        </p>
                    </div>

                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-xl text-blue-700"
                    >
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                </div>
            </div>

            <div
                class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase text-slate-400">
                            Ingresos pagados
                        </p>
                        <p class="mt-2 text-3xl font-black text-green-700">
                            {{ formatCurrency(estadisticas.total_pagos) }}
                        </p>
                    </div>

                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-green-50 text-xl text-green-700"
                    >
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>

            <div
                class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase text-slate-400">
                            Créditos activos
                        </p>
                        <p class="mt-2 text-3xl font-black text-amber-700">
                            {{ estadisticas.creditos_activos }}
                        </p>
                    </div>

                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-xl text-amber-700"
                    >
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                </div>
            </div>

            <div
                class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase text-slate-400">
                            Saldo pendiente
                        </p>
                        <p class="mt-2 text-3xl font-black text-red-700">
                            {{ formatCurrency(estadisticas.saldo_pendiente) }}
                        </p>
                    </div>

                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-xl text-red-700"
                    >
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div
                class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                <div
                    class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div>
                        <h2 class="text-lg font-black text-slate-900">
                            Diagrama de barras
                        </h2>
                        <p class="text-sm text-slate-500">
                            Selecciona la métrica y el período para ver el
                            gráfico.
                        </p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-600"
                                >Métrica</span
                            >
                            <select
                                v-model="barMetric"
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="Pagos Totales" selected>
                                    Pagos Totales
                                </option>
                                <option value="Pagos Contados">
                                    Pagos Contados
                                </option>
                                <option value="Pagos Creditos">
                                    Pagos Creditos
                                </option>
                                <option value="Pagos por concepto de pago">
                                    Pagos por concepto de pago
                                </option>
                                <option value="Cantidad pagos e inscripciones">
                                    Cantidad pagos e inscripciones
                                </option>
                                <option value="Inscripciones">
                                    Inscripciones
                                </option>
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-600"
                                >Período</span
                            >
                            <select
                                v-model="barPeriod"
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="month">Mes</option>
                                <option value="quarter">Trimestre</option>
                                <option value="semester">Semestre</option>
                                <option value="year">Año</option>
                            </select>
                        </label>
                    </div>
                </div>

                <canvas ref="barCanvas" height="140"></canvas>
            </div>

            <div
                class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                <div
                    class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div>
                        <h2 class="text-lg font-black text-slate-900">
                            Diagrama de pastel
                        </h2>
                        <p class="text-sm text-slate-500">
                            Visualiza la participación relativa del periodo
                            seleccionado.
                        </p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-600"
                                >Métrica</span
                            >
                            <select
                                v-model="pieMetric"
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="Pagos Totales" selected>
                                    Pagos Totales
                                </option>
                                <option value="Pagos Contados">
                                    Pagos Contados
                                </option>
                                <option value="Pagos Creditos">
                                    Pagos Creditos
                                </option>
                                <option value="Pagos por concepto de pago">
                                    Pagos por concepto de pago
                                </option>
                                <option value="Cantidad pagos e inscripciones">
                                    Cantidad pagos e inscripciones
                                </option>
                                <option value="Inscripciones">
                                    Inscripciones
                                </option>
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-600"
                                >Período</span
                            >
                            <select
                                v-model="piePeriod"
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="month">Mes</option>
                                <option value="quarter">Trimestre</option>
                                <option value="semester">Semestre</option>
                                <option value="year">Año</option>
                            </select>
                        </label>
                    </div>
                </div>

                <canvas ref="pieCanvas" height="140"></canvas>
            </div>

            <div
                class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2"
            >
                <div
                    class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div>
                        <h2 class="text-lg font-black text-slate-900">
                            Diagrama de ojiva
                        </h2>
                        <p class="text-sm text-slate-500">
                            Observa la tendencia acumulada de la métrica
                            seleccionada.
                        </p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-600"
                                >Métrica</span
                            >
                            <select
                                v-model="ojivaMetric"
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="Pagos Totales" selected>
                                    Pagos Totales
                                </option>
                                <option value="Pagos Contados">
                                    Pagos Contados
                                </option>
                                <option value="Pagos Creditos">
                                    Pagos Creditos
                                </option>
                                <option value="Pagos por concepto de pago">
                                    Pagos por concepto de pago
                                </option>
                                <option value="Cantidad pagos e inscripciones">
                                    Cantidad pagos e inscripciones
                                </option>
                                <option value="Inscripciones">
                                    Inscripciones
                                </option>
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-600"
                                >Período</span
                            >
                            <select
                                v-model="ojivaPeriod"
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="month">Mes</option>
                                <option value="quarter">Trimestre</option>
                                <option value="semester">Semestre</option>
                                <option value="year">Año</option>
                            </select>
                        </label>
                    </div>
                </div>

                <canvas ref="ojivaCanvas" height="120"></canvas>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <h2 class="text-xl font-black text-slate-900">
                    Exportar reportes
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Descarga reportes en PDF o Excel según el rango de fechas
                    seleccionado.
                </p>
            </div>

            <div class="grid gap-5 md:grid-cols-3">
                <div class="rounded-3xl border border-slate-200 p-5">
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-700"
                    >
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>

                    <h3 class="font-black text-slate-900">Inscripciones</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Reporte de estudiantes inscritos.
                    </p>

                    <div class="mt-5 flex gap-2">
                        <a
                            :href="
                                route(
                                    'admin.reportes.inscripciones.pdf',
                                    queryParams,
                                )
                            "
                            class="inline-flex items-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100"
                        >
                            <i class="fa-solid fa-file-pdf"></i>
                            PDF
                        </a>

                        <a
                            :href="
                                route(
                                    'admin.reportes.inscripciones.excel',
                                    queryParams,
                                )
                            "
                            class="inline-flex items-center gap-2 rounded-xl bg-green-50 px-4 py-2 text-xs font-bold text-green-700 transition hover:bg-green-100"
                        >
                            <i class="fa-solid fa-file-excel"></i>
                            Excel
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 p-5">
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-green-50 text-green-700"
                    >
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>

                    <h3 class="font-black text-slate-900">Pagos</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Pagos al contado y cuotas pagadas.
                    </p>

                    <div class="mt-5 flex gap-2">
                        <a
                            :href="
                                route('admin.reportes.pagos.pdf', queryParams)
                            "
                            class="inline-flex items-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100"
                        >
                            <i class="fa-solid fa-file-pdf"></i>
                            PDF
                        </a>

                        <a
                            :href="
                                route('admin.reportes.pagos.excel', queryParams)
                            "
                            class="inline-flex items-center gap-2 rounded-xl bg-green-50 px-4 py-2 text-xs font-bold text-green-700 transition hover:bg-green-100"
                        >
                            <i class="fa-solid fa-file-excel"></i>
                            Excel
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 p-5">
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-700"
                    >
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>

                    <h3 class="font-black text-slate-900">Créditos</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Créditos, saldos y cuotas asociadas.
                    </p>

                    <div class="mt-5 flex gap-2">
                        <a
                            :href="
                                route(
                                    'admin.reportes.creditos.pdf',
                                    queryParams,
                                )
                            "
                            class="inline-flex items-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100"
                        >
                            <i class="fa-solid fa-file-pdf"></i>
                            PDF
                        </a>

                        <a
                            :href="
                                route(
                                    'admin.reportes.creditos.excel',
                                    queryParams,
                                )
                            "
                            class="inline-flex items-center gap-2 rounded-xl bg-green-50 px-4 py-2 text-xs font-bold text-green-700 transition hover:bg-green-100"
                        >
                            <i class="fa-solid fa-file-excel"></i>
                            Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, router } from "@inertiajs/vue3";
import { computed, nextTick, onMounted, reactive, ref, watch } from "vue";

const props = defineProps({
    inicio: { type: String, default: "" },
    fin: { type: String, default: "" },
    estadisticas: { type: Object, default: () => ({}) },
    pagosContados: { type: Array, default: () => [] },
    pagosCreditos: { type: Array, default: () => [] },
    pagosContadosCantidad: { type: Array, default: () => [] },
    pagosCreditosCantidad: { type: Array, default: () => [] },
    inscripciones: { type: Array, default: () => [] },
    pagosPorConcepto: { type: Array, default: () => [] },
});

const form = reactive({
    inicio: props.inicio,
    fin: props.fin,
});

watch(
    () => props.inicio,
    (value) => {
        form.inicio = value;
    },
    { immediate: true },
);

watch(
    () => props.fin,
    (value) => {
        form.fin = value;
    },
    { immediate: true },
);

const queryParams = computed(() => ({
    inicio: form.inicio || props.inicio,
    fin: form.fin || props.fin,
}));

const barMetric = ref("Pagos Totales");
const barPeriod = ref("month");
const pieMetric = ref("Pagos Totales");
const piePeriod = ref("month");
const ojivaMetric = ref("Pagos Totales");
const ojivaPeriod = ref("month");

const barCanvas = ref(null);
const pieCanvas = ref(null);
const ojivaCanvas = ref(null);

let barChart = null;
let pieChart = null;
let ojivaChart = null;

function formatCurrency(value) {
    const amount = Number(value ?? 0);
    return `Bs ${amount.toLocaleString("es-BO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
}

function aplicarFiltro() {
    router.get(
        route("admin.reportes.index"),
        { inicio: form.inicio, fin: form.fin },
        {
            preserveState: true,
            replace: true,
        },
    );
}

function limpiarFiltros() {
    router.get(
        route("admin.reportes.index"),
        {},
        {
            preserveState: true,
            replace: true,
        },
    );
}

const metricSources = {
    "Pagos Contados": props.pagosContados,
    "Pagos Creditos": props.pagosCreditos,
    "Pagos Totales": null,
    "Pagos por concepto de pago": props.pagosPorConcepto,
    "Cantidad pagos e inscripciones": null,
    Inscripciones: props.inscripciones,
};

const chartOptions = {
    responsive: true,
    plugins: {
        legend: {
            labels: {
                font: {
                    weight: "bold",
                },
            },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
        },
    },
};

const barPalette = [
    "#7dd3fc",
    "#fb7185",
    "#fbbf24",
    "#a78bfa",
    "#34d399",
    "#60a5fa",
    "#f472b6",
    "#f9a8d4",
    "#f97316",
    "#10b981",
    "#8b5cf6",
    "#e879f9",
];
const barBorderPalette = [
    "#0284c7",
    "#be123c",
    "#b45309",
    "#7c3aed",
    "#047857",
    "#075985",
    "#be185d",
    "#c084fc",
    "#c2410c",
    "#047857",
    "#4c1d95",
    "#9d174d",
];
const piePalette = [
    "#38bdf8",
    "#fb7185",
    "#fde68a",
    "#a78bfa",
    "#34d399",
    "#60a5fa",
    "#f472b6",
    "#f9a8d4",
];
const pieBorderPalette = [
    "#1e3a8a",
    "#7c2d12",
    "#92400e",
    "#5b21b6",
    "#065f46",
    "#0c4a6e",
    "#831843",
    "#9d174d",
];
const pieTotalsColors = ["#38bdf8", "#fb7185"];
const pieCountColors = ["#38bdf8", "#fb7185", "#fde68a"];
const multiSeriesColors = {
    "Pagos Contados": { backgroundColor: "#38bdf8", borderColor: "#1d4ed8" },
    "Pagos Creditos": { backgroundColor: "#fb7185", borderColor: "#be123c" },
    Inscripciones: { backgroundColor: "#fde68a", borderColor: "#ca8a04" },
};

function normalizePeriodKey(mes, period) {
    const [year, month] = mes.split("-").map(Number);

    if (period === "month") {
        return mes;
    }

    if (period === "quarter") {
        return `${year}-Q${Math.ceil(month / 3)}`;
    }

    if (period === "semester") {
        return `${year}-S${month <= 6 ? 1 : 2}`;
    }

    return `${year}`;
}

function sortPeriodKeys(a, b) {
    const [aYear, aRange] = a.split("-");
    const [bYear, bRange] = b.split("-");

    if (aYear !== bYear) {
        return Number(aYear) - Number(bYear);
    }

    if (!aRange && !bRange) {
        return 0;
    }

    if (!aRange) {
        return -1;
    }

    if (!bRange) {
        return 1;
    }

    const valueFor = (range) => {
        if (range.startsWith("Q")) {
            return Number(range.slice(1));
        }
        if (range.startsWith("S")) {
            return Number(range.slice(1)) * 10;
        }
        return 0;
    };

    return valueFor(aRange) - valueFor(bRange);
}

function aggregateByPeriod(source, period) {
    const aggregated = source.reduce((carry, item) => {
        const key = normalizePeriodKey(item.mes, period);
        carry[key] = (carry[key] || 0) + Number(item.total);
        return carry;
    }, {});

    return Object.entries(aggregated)
        .sort(([a], [b]) => sortPeriodKeys(a, b))
        .map(([periodKey, total]) => ({
            period: periodKey,
            total: Number(total.toFixed(2)),
        }));
}

function createDataset(metric, period) {
    if (metric === "Pagos Totales") {
        const contadoByPeriod = aggregateByPeriod(props.pagosContados, period);
        const creditosByPeriod = aggregateByPeriod(props.pagosCreditos, period);
        const allPeriods = [
            ...new Set([
                ...contadoByPeriod.map((item) => item.period),
                ...creditosByPeriod.map((item) => item.period),
            ]),
        ].sort(sortPeriodKeys);

        return allPeriods.map((periodKey) => ({
            period: periodKey,
            contado:
                contadoByPeriod.find((item) => item.period === periodKey)
                    ?.total || 0,
            creditos:
                creditosByPeriod.find((item) => item.period === periodKey)
                    ?.total || 0,
        }));
    }

    if (metric === "Pagos por concepto de pago") {
        if (props.pagosPorConcepto.length === 0) {
            return [{ concepto: "Sin datos", total: 0 }];
        }

        return props.pagosPorConcepto.map((item) => ({
            concepto: item.concepto,
            total: Number(item.total),
        }));
    }

    if (metric === "Cantidad pagos e inscripciones") {
        const contadoByPeriod = aggregateByPeriod(
            props.pagosContadosCantidad,
            period,
        );
        const creditosByPeriod = aggregateByPeriod(
            props.pagosCreditosCantidad,
            period,
        );
        const inscripcionesByPeriod = aggregateByPeriod(
            props.inscripciones,
            period,
        );
        const allPeriods = [
            ...new Set([
                ...contadoByPeriod.map((item) => item.period),
                ...creditosByPeriod.map((item) => item.period),
                ...inscripcionesByPeriod.map((item) => item.period),
            ]),
        ].sort(sortPeriodKeys);

        return allPeriods.map((periodKey) => ({
            period: periodKey,
            contado:
                contadoByPeriod.find((item) => item.period === periodKey)
                    ?.total || 0,
            creditos:
                creditosByPeriod.find((item) => item.period === periodKey)
                    ?.total || 0,
            inscripciones:
                inscripcionesByPeriod.find((item) => item.period === periodKey)
                    ?.total || 0,
        }));
    }

    const source = metricSources[metric] || [];
    const grouped = aggregateByPeriod(source, period);

    if (grouped.length === 0) {
        return [{ period: "Sin datos", total: 0 }];
    }

    return grouped;
}

function createChart(context, type, config) {
    return new window.Chart(context, {
        type,
        data: config.data,
        options: config.options,
    });
}

function destroyCharts() {
    [barChart, pieChart, ojivaChart].forEach((chart) => chart?.destroy());
    barChart = null;
    pieChart = null;
    ojivaChart = null;
}

function loadChartJs() {
    return new Promise((resolve) => {
        if (window.Chart) {
            resolve();
            return;
        }

        const script = document.createElement("script");
        script.src = "https://cdn.jsdelivr.net/npm/chart.js";
        script.onload = () => resolve();
        document.head.appendChild(script);
    });
}

async function initializeCharts() {
    await loadChartJs();
    destroyCharts();

    barChart = createChart(barCanvas.value, "bar", {
        data: {
            labels: [],
            datasets: [
                {
                    label: "Pagos Totales",
                    data: [],
                    backgroundColor: "#2563eb",
                    borderColor: "#1d4ed8",
                    borderWidth: 1,
                },
            ],
        },
        options: chartOptions,
    });

    pieChart = createChart(pieCanvas.value, "pie", {
        data: {
            labels: [],
            datasets: [
                {
                    label: "Pagos Totales",
                    data: [],
                    backgroundColor: [
                        "#2563eb",
                        "#16a34a",
                        "#f59e0b",
                        "#9333ea",
                        "#db2777",
                        "#0ea5e9",
                        "#14b8a6",
                        "#f97316",
                    ],
                    borderColor: "#fff",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        font: { weight: "bold" },
                    },
                },
            },
        },
    });

    ojivaChart = createChart(ojivaCanvas.value, "line", {
        data: {
            labels: [],
            datasets: [
                {
                    label: "Pagos Totales acumulado",
                    data: [],
                    tension: 0.35,
                    fill: false,
                    borderColor: "#9333ea",
                    backgroundColor: "#9333ea",
                    pointRadius: 4,
                    borderWidth: 3,
                },
            ],
        },
        options: chartOptions,
    });

    refreshCharts();
}

function updateChart(chart, metric, period, labelSuffix = "") {
    if (!chart) {
        return;
    }

    const dataset = createDataset(metric, period);
    let labels = [];
    let values = [];
    let datasets = [];

    if (metric === "Pagos Totales") {
        labels = dataset.map((item) => item.period);
        const contadoValues = dataset.map((item) => item.contado);
        const creditosValues = dataset.map((item) => item.creditos);

        if (chart === pieChart) {
            const totalContado = contadoValues.reduce(
                (sum, value) => sum + value,
                0,
            );
            const totalCreditos = creditosValues.reduce(
                (sum, value) => sum + value,
                0,
            );
            labels = ["Pagos Contados", "Pagos Creditos"];
            values = [totalContado, totalCreditos];
            datasets = [
                {
                    label: metric,
                    data: values,
                    backgroundColor: pieTotalsColors.slice(0, values.length),
                    borderColor: "#fff",
                    borderWidth: 1,
                },
            ];
        } else if (chart === ojivaChart) {
            const combined = contadoValues.map(
                (value, index) => value + creditosValues[index],
            );
            const cumulative = combined.reduce((acc, value) => {
                const next = acc.length ? acc[acc.length - 1] + value : value;
                acc.push(Number(next.toFixed(2)));
                return acc;
            }, []);
            labels = dataset.map((item) => item.period);
            datasets = [
                {
                    label: `${metric}${labelSuffix}`,
                    data: cumulative,
                    tension: 0.35,
                    fill: false,
                    borderColor: "#9333ea",
                    backgroundColor: "#9333ea",
                    pointRadius: 4,
                    borderWidth: 3,
                },
            ];
        } else {
            datasets = [
                {
                    label: "Pagos Contados",
                    data: contadoValues,
                    backgroundColor:
                        multiSeriesColors["Pagos Contados"].backgroundColor,
                    borderColor:
                        multiSeriesColors["Pagos Contados"].borderColor,
                    borderWidth: 1,
                },
                {
                    label: "Pagos Creditos",
                    data: creditosValues,
                    backgroundColor:
                        multiSeriesColors["Pagos Creditos"].backgroundColor,
                    borderColor:
                        multiSeriesColors["Pagos Creditos"].borderColor,
                    borderWidth: 1,
                },
            ];
        }
    } else if (metric === "Pagos por concepto de pago") {
        labels = dataset.map((item) => item.concepto);
        values = dataset.map((item) => item.total);
        datasets = [
            {
                label: metric,
                data: values,
                backgroundColor:
                    chart === pieChart
                        ? piePalette.slice(0, values.length)
                        : values.map(
                              (_, index) =>
                                  barPalette[index % barPalette.length],
                          ),
                borderColor:
                    chart === pieChart
                        ? pieBorderPalette.slice(0, values.length)
                        : values.map(
                              (_, index) =>
                                  barBorderPalette[
                                      index % barBorderPalette.length
                                  ],
                          ),
                borderWidth: 1,
            },
        ];

        if (chart === ojivaChart) {
            const cumulative = values.reduce((acc, value) => {
                const next = acc.length ? acc[acc.length - 1] + value : value;
                acc.push(Number(next.toFixed(2)));
                return acc;
            }, []);
            datasets[0].data = cumulative;
            datasets[0].borderColor = "#9333ea";
            datasets[0].backgroundColor = "#9333ea";
        }
    } else if (metric === "Cantidad pagos e inscripciones") {
        labels = dataset.map((item) => item.period);
        const contadoValues = dataset.map((item) => item.contado);
        const creditosValues = dataset.map((item) => item.creditos);
        const inscripcionesValues = dataset.map((item) => item.inscripciones);

        if (chart === pieChart) {
            labels = ["Pagos Contados", "Pagos Creditos", "Inscripciones"];
            values = [
                contadoValues.reduce((sum, value) => sum + value, 0),
                creditosValues.reduce((sum, value) => sum + value, 0),
                inscripcionesValues.reduce((sum, value) => sum + value, 0),
            ];
            datasets = [
                {
                    label: metric,
                    data: values,
                    backgroundColor: pieCountColors.slice(0, values.length),
                    borderColor: "#fff",
                    borderWidth: 1,
                },
            ];
        } else if (chart === ojivaChart) {
            const combined = contadoValues.map(
                (value, index) =>
                    value + creditosValues[index] + inscripcionesValues[index],
            );
            const cumulative = combined.reduce((acc, value) => {
                const next = acc.length ? acc[acc.length - 1] + value : value;
                acc.push(Number(next.toFixed(2)));
                return acc;
            }, []);
            datasets = [
                {
                    label: `${metric}${labelSuffix}`,
                    data: cumulative,
                    tension: 0.35,
                    fill: false,
                    borderColor: "#9333ea",
                    backgroundColor: "#9333ea",
                    pointRadius: 4,
                    borderWidth: 3,
                },
            ];
        } else {
            datasets = [
                {
                    label: "Pagos Contados",
                    data: contadoValues,
                    backgroundColor:
                        multiSeriesColors["Pagos Contados"].backgroundColor,
                    borderColor:
                        multiSeriesColors["Pagos Contados"].borderColor,
                    borderWidth: 1,
                },
                {
                    label: "Pagos Creditos",
                    data: creditosValues,
                    backgroundColor:
                        multiSeriesColors["Pagos Creditos"].backgroundColor,
                    borderColor:
                        multiSeriesColors["Pagos Creditos"].borderColor,
                    borderWidth: 1,
                },
                {
                    label: "Inscripciones",
                    data: inscripcionesValues,
                    backgroundColor:
                        multiSeriesColors.Inscripciones.backgroundColor,
                    borderColor: multiSeriesColors.Inscripciones.borderColor,
                    borderWidth: 1,
                },
            ];
        }
    } else {
        labels = dataset.map((item) => item.period);
        values = dataset.map((item) => item.total);

        if (chart === ojivaChart) {
            const cumulative = values.reduce((acc, value) => {
                const next = acc.length ? acc[acc.length - 1] + value : value;
                acc.push(Number(next.toFixed(2)));
                return acc;
            }, []);
            datasets = [
                {
                    label: `${metric}${labelSuffix}`,
                    data: cumulative,
                    tension: 0.35,
                    fill: false,
                    borderColor: "#9333ea",
                    backgroundColor: "#9333ea",
                    pointRadius: 4,
                    borderWidth: 3,
                },
            ];
        } else {
            datasets = [
                {
                    label: metric,
                    data: values,
                    backgroundColor: "#2563eb",
                    borderColor: "#1d4ed8",
                    borderWidth: 1,
                },
            ];
        }
    }

    chart.data.labels = labels;
    chart.data.datasets = datasets;
    chart.update();
}

function refreshCharts() {
    updateChart(barChart, barMetric.value, barPeriod.value);
    updateChart(pieChart, pieMetric.value, piePeriod.value);
    updateChart(ojivaChart, ojivaMetric.value, ojivaPeriod.value, " acumulado");
}

watch([barMetric, barPeriod], () => {
    nextTick(() => refreshCharts());
});

watch([pieMetric, piePeriod], () => {
    nextTick(() => refreshCharts());
});

watch([ojivaMetric, ojivaPeriod], () => {
    nextTick(() => refreshCharts());
});

watch(
    () => [
        props.pagosContados,
        props.pagosCreditos,
        props.pagosContadosCantidad,
        props.pagosCreditosCantidad,
        props.inscripciones,
        props.pagosPorConcepto,
    ],
    () => {
        nextTick(() => initializeCharts());
    },
    { deep: true },
);

onMounted(() => {
    initializeCharts();
});
</script>
