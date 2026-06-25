<script setup>
import { computed, onBeforeUnmount, ref, watch } from "vue";

import { Head, Link, router, usePage } from "@inertiajs/vue3";

import ModalPagoContado from "./components/ModalPagoContado.vue";
import ModalQrPagoFacil from "./components/ModalQrPagoFacil.vue";
import HeaderAlumno from "../partials/headerAlumno.vue";
import PageVisitCounter from "../partials/PageVisitCounter.vue";

const props = defineProps({
    pagos: {
        type: Object,
        default: () => ({}),
    },

    inscripciones: {
        type: Array,
        default: () => [],
    },

    conceptos: {
        type: Array,
        default: () => [],
    },

    buscar: {
        type: String,
        default: "",
    },
});

const page = usePage();

const buscar = ref(props.buscar ?? "");
const mostrarFormulario = ref(false);
const mostrarModalQr = ref(false);
const pagoQrActual = ref(null);

const buscarDebounce = ref(null);

const flashSuccess = computed(() => page.props.flash?.success ?? "");
const flashError = computed(() => page.props.flash?.error ?? "");

const totalPagos = computed(() => Number(props.pagos?.total ?? 0));
const desdePago = computed(() => Number(props.pagos?.from ?? 0));
const hastaPago = computed(() => Number(props.pagos?.to ?? 0));

const formatearMonto = (valor) => {
    const numero = Number(valor ?? 0);

    return `Bs ${numero.toLocaleString("es-BO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};

const formatearFecha = (fecha) => {
    if (!fecha) {
        return "Sin fecha";
    }

    const fechaObjeto = new Date(fecha);

    if (Number.isNaN(fechaObjeto.getTime())) {
        return "Sin fecha";
    }

    return new Intl.DateTimeFormat("es-BO", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    }).format(fechaObjeto);
};

const buscarPagos = (texto = buscar.value) => {
    router.get(
        "/alumno/mis-pagos",
        { buscar: texto },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const abrirFormulario = () => {
    mostrarModalQr.value = false;
    pagoQrActual.value = null;
    mostrarFormulario.value = true;
};

const cerrarFormulario = () => {
    mostrarFormulario.value = false;
};

const abrirModalQr = (pago) => {
    pagoQrActual.value = pago;
    mostrarModalQr.value = true;
    mostrarFormulario.value = false;
};

const cerrarModalQr = () => {
    mostrarModalQr.value = false;
    pagoQrActual.value = null;
};

const manejarPagoConfirmado = () => {
    router.reload({
        only: ["pagos"],
        preserveScroll: true,
        preserveState: true,
    });
};

watch(buscar, (nuevoValor) => {
    if (buscarDebounce.value) {
        clearTimeout(buscarDebounce.value);
    }

    buscarDebounce.value = setTimeout(() => {
        buscarPagos(nuevoValor);
    }, 350);
});

const estadoClase = (estado) => {
    const valor = String(estado ?? "").toLowerCase();

    if (valor === "confirmado")
        return "bg-emerald-50 text-emerald-700 ring-emerald-100";
    if (valor === "anulado" || valor === "fallido")
        return "bg-red-50 text-red-700 ring-red-100";
    return "bg-amber-50 text-amber-700 ring-amber-100";
};

const metodoClase = (metodo) => {
    if (metodo === "QR") return "bg-blue-50 text-blue-700";
    if (metodo === "Transferencia") return "bg-violet-50 text-violet-700";
    return "bg-slate-100 text-slate-700";
};

onBeforeUnmount(() => {
    if (buscarDebounce.value) {
        clearTimeout(buscarDebounce.value);
    }
});
</script>

<template>
    <Head title="Mis Pagos" />

    <div class="min-h-screen bg-slate-100">
        <HeaderAlumno />

        <main class="px-5 pb-10 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <section
                    class="relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-r from-slate-50 via-blue-50 to-white p-6 shadow-sm sm:p-7"
                >
                    <div
                        class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-blue-100/60 to-transparent"
                    ></div>

                    <div
                        class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between"
                    >
                        <div>
                            <p
                                class="text-xs font-black uppercase tracking-[0.3em] text-blue-500"
                            >
                                Portal del alumno
                            </p>

                            <h1
                                class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl"
                            >
                                Mis Pagos
                            </h1>

                            <p
                                class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base"
                            >
                                Consulta tus pagos registrados, busca por
                                concepto o estado y agrega un pago contado para
                                mensualidad u otro concepto que no sea
                                matrícula.
                            </p>
                        </div>

                        <div
                            class="rounded-2xl border border-slate-200 bg-white/80 px-4 py-4 shadow-sm backdrop-blur sm:min-w-[220px]"
                        >
                            <p
                                class="text-xs font-semibold uppercase tracking-widest text-slate-500"
                            >
                                Total de pagos
                            </p>

                            <p class="mt-1 text-3xl font-black text-slate-900">
                                {{ totalPagos }}
                            </p>
                        </div>
                    </div>
                </section>

                <div
                    v-if="flashSuccess"
                    class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm font-semibold text-emerald-700"
                >
                    {{ flashSuccess }}
                </div>

                <div
                    v-if="flashError"
                    class="mt-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm font-semibold text-red-700"
                >
                    {{ flashError }}
                </div>

                <section
                    class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div
                        class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <div>
                            <h2 class="text-lg font-black text-slate-900">
                                Control de pagos
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Busca y registra pagos al contado con una vista
                                formal y compacta.
                            </p>
                        </div>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-black text-white transition hover:-translate-y-0.5 hover:bg-slate-800"
                            @click="abrirFormulario"
                        >
                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M12 5v14M5 12h14" />
                            </svg>

                            Agregar pago contado
                        </button>
                    </div>

                    <div
                        class="mt-5 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-end"
                    >
                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                            >
                                Buscar
                            </label>

                            <input
                                v-model="buscar"
                                type="text"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
                                placeholder="Alumno, concepto, estado, método, transacción, carrera"
                                autocomplete="off"
                            />
                        </div>

                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                                @click="buscarPagos('')"
                            >
                                Buscar
                            </button>

                            <button
                                type="button"
                                class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-200"
                                @click="
                                    () => {
                                        buscar = '';
                                        buscarPagos('');
                                    }
                                "
                            >
                                Limpiar
                            </button>
                        </div>
                    </div>
                </section>

                <ModalPagoContado
                    :mostrar="mostrarFormulario"
                    :inscripciones="inscripciones"
                    :conceptos="conceptos"
                    @qr-generado="abrirModalQr"
                    @cerrar="cerrarFormulario"
                />

                <ModalQrPagoFacil
                    :mostrar="mostrarModalQr"
                    :pago="pagoQrActual"
                    @cerrar="cerrarModalQr"
                    @confirmado="manejarPagoConfirmado"
                />

                <section
                    class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Concepto
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Inscripción
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Monto
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Método
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Estado
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Fecha
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 bg-white">
                                <tr
                                    v-for="pago in pagos.data ?? []"
                                    :key="pago.id"
                                    class="transition hover:bg-slate-50"
                                >
                                    <td class="px-5 py-4">
                                        <p
                                            class="text-sm font-black text-slate-900"
                                        >
                                            {{
                                                pago.concepto_pago?.nombre ??
                                                "Sin concepto"
                                            }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{
                                                pago.inscripcion?.carrera ??
                                                "Sin carrera"
                                            }}
                                        </p>
                                    </td>

                                    <td
                                        class="px-5 py-4 text-sm text-slate-600"
                                    >
                                        <p class="font-bold text-slate-900">
                                            {{
                                                pago.inscripcion
                                                    ?.carrera_codigo ??
                                                "SIN-COD"
                                            }}
                                            -
                                            {{
                                                pago.inscripcion?.oferta ??
                                                "Sin oferta"
                                            }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{
                                                pago.inscripcion?.periodo ??
                                                "Sin periodo"
                                            }}
                                            {{
                                                pago.inscripcion?.gestion ?? ""
                                            }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700 ring-1 ring-emerald-100"
                                        >
                                            {{
                                                formatearMonto(
                                                    pago.monto_pagado,
                                                )
                                            }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-black"
                                            :class="
                                                metodoClase(pago.metodo_pago)
                                            "
                                        >
                                            {{ pago.metodo_pago }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-black ring-1"
                                            :class="estadoClase(pago.estado)"
                                        >
                                            {{ pago.estado }}
                                        </span>
                                    </td>

                                    <td
                                        class="px-5 py-4 text-sm text-slate-600"
                                    >
                                        {{ formatearFecha(pago.fecha_pago) }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <Link
                                                v-if="pago.qr_url"
                                                :href="pago.qr_url"
                                                target="_blank"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100"
                                                title="Ver QR"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4z"
                                                    />
                                                    <path
                                                        d="M14 14h2v2h-2zM18 14h2v2h-2zM14 18h2v2h-2zM18 18h2v2h-2z"
                                                    />
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="!(pagos.data ?? []).length">
                                    <td
                                        colspan="7"
                                        class="px-6 py-12 text-center text-sm text-slate-500"
                                    >
                                        No se encontraron pagos registrados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-slate-100 px-5 py-4">
                        <div
                            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <p class="text-sm text-slate-500">
                                Mostrando {{ desdePago }} a {{ hastaPago }} de
                                {{ totalPagos }} pagos
                            </p>

                            <div class="flex flex-wrap gap-2">
                                <template
                                    v-for="link in pagos.links ?? []"
                                    :key="link.label + link.url"
                                >
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        preserve-scroll
                                        preserve-state
                                        class="rounded-xl border px-3 py-2 text-sm font-bold transition"
                                        :class="
                                            link.active
                                                ? 'border-blue-600 bg-blue-600 text-white'
                                                : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
                                        "
                                    >
                                        <span v-html="link.label"></span>
                                    </Link>

                                    <span
                                        v-else
                                        class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-bold text-slate-400"
                                        v-html="link.label"
                                    ></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <footer
            class="fixed left-4 bottom-4 z-50 bg-transparent p-0 sm:left-6 sm:bottom-6"
        >
            <PageVisitCounter compact />
        </footer>
    </div>
</template>
