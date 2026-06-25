<script setup>
import { computed, onBeforeUnmount, ref, watch } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";

import HeaderAlumno from "../partials/headerAlumno.vue";
import PageVisitCounter from "../partials/PageVisitCounter.vue";
import ModalCuotasCredito from "./components/ModalCuotasCredito.vue";

const props = defineProps({
    creditos: {
        type: Object,
        default: () => ({}),
    },

    buscar: {
        type: String,
        default: "",
    },
});

const page = usePage();

const buscar = ref(props.buscar ?? "");
const mostrarModalCuotas = ref(false);
const creditoActual = ref(null);
const buscarDebounce = ref(null);

const totalCreditos = computed(() => Number(props.creditos?.total ?? 0));
const desdeCredito = computed(() => Number(props.creditos?.from ?? 0));
const hastaCredito = computed(() => Number(props.creditos?.to ?? 0));

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

const buscarCreditos = (texto = buscar.value) => {
    router.get(
        "/alumno/mis-creditos",
        { buscar: texto },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const abrirModalCuotas = (credito) => {
    creditoActual.value = credito;
    mostrarModalCuotas.value = true;
};

const cerrarModalCuotas = () => {
    mostrarModalCuotas.value = false;
    creditoActual.value = null;
};

const estadoClase = (estado) => {
    const valor = String(estado ?? "").toLowerCase();

    if (valor === "pagado")
        return "bg-emerald-50 text-emerald-700 ring-emerald-100";
    if (valor === "anulado" || valor === "fallido")
        return "bg-red-50 text-red-700 ring-red-100";
    return "bg-amber-50 text-amber-700 ring-amber-100";
};

onBeforeUnmount(() => {
    if (buscarDebounce.value) {
        clearTimeout(buscarDebounce.value);
    }
});

watch(buscar, (nuevoValor) => {
    if (buscarDebounce.value) {
        clearTimeout(buscarDebounce.value);
    }

    buscarDebounce.value = setTimeout(() => {
        buscarCreditos(nuevoValor);
    }, 350);
});
</script>

<template>
    <Head title="Mis Créditos" />

    <div class="min-h-screen bg-slate-100">
        <HeaderAlumno />

        <main class="px-5 pb-10 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <section
                    class="relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-r from-slate-50 via-amber-50 to-white p-6 shadow-sm sm:p-7"
                >
                    <div
                        class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-amber-100/60 to-transparent"
                    ></div>

                    <div
                        class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between"
                    >
                        <div>
                            <p
                                class="text-xs font-black uppercase tracking-[0.3em] text-amber-500"
                            >
                                Portal del alumno
                            </p>

                            <h1
                                class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl"
                            >
                                Mis Créditos
                            </h1>

                            <p
                                class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base"
                            >
                                Consulta tus créditos, abre el detalle de cada
                                crédito y visualiza las cuotas en un modal
                                central.
                            </p>
                        </div>

                        <div
                            class="rounded-2xl border border-slate-200 bg-white/80 px-4 py-4 shadow-sm backdrop-blur sm:min-w-[220px]"
                        >
                            <p
                                class="text-xs font-semibold uppercase tracking-widest text-slate-500"
                            >
                                Total de créditos
                            </p>

                            <p class="mt-1 text-3xl font-black text-slate-900">
                                {{ totalCreditos }}
                            </p>
                        </div>
                    </div>
                </section>

                <section
                    class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div
                        class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <div>
                            <h2 class="text-lg font-black text-slate-900">
                                Control de créditos
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Busca tus créditos por concepto, estado o
                                carrera.
                            </p>
                        </div>
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
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-100"
                                placeholder="Concepto, estado, carrera, código o monto total"
                                autocomplete="off"
                            />
                        </div>

                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                                @click="buscarCreditos('')"
                            >
                                Buscar
                            </button>

                            <button
                                type="button"
                                class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-200"
                                @click="
                                    () => {
                                        buscar = '';
                                        buscarCreditos('');
                                    }
                                "
                            >
                                Limpiar
                            </button>
                        </div>
                    </div>
                </section>

                <ModalCuotasCredito
                    :mostrar="mostrarModalCuotas"
                    :credito="creditoActual"
                    @cerrar="cerrarModalCuotas"
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
                                        Monto total
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Saldo
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Cuotas
                                    </th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Estado
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
                                    v-for="credito in creditos.data ?? []"
                                    :key="credito.id"
                                    class="transition hover:bg-slate-50"
                                >
                                    <td class="px-5 py-4">
                                        <p
                                            class="text-sm font-black text-slate-900"
                                        >
                                            {{
                                                credito.concepto_pago?.nombre ??
                                                "Sin concepto"
                                            }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ credito.tipo_pago ?? "CREDITO" }}
                                        </p>
                                    </td>

                                    <td
                                        class="px-5 py-4 text-sm text-slate-600"
                                    >
                                        <p class="font-bold text-slate-900">
                                            {{
                                                credito.inscripcion
                                                    ?.carrera_codigo ??
                                                "SIN-COD"
                                            }}
                                            -
                                            {{
                                                credito.inscripcion?.oferta ??
                                                "Sin oferta"
                                            }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{
                                                credito.inscripcion?.periodo ??
                                                "Sin periodo"
                                            }}
                                            {{
                                                credito.inscripcion?.gestion ??
                                                ""
                                            }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700 ring-1 ring-blue-100"
                                        >
                                            {{
                                                formatearMonto(
                                                    credito.monto_total,
                                                )
                                            }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-700 ring-1 ring-amber-100"
                                        >
                                            {{
                                                formatearMonto(
                                                    credito.saldo_pendiente,
                                                )
                                            }}
                                        </span>
                                    </td>

                                    <td
                                        class="px-5 py-4 text-sm text-slate-600"
                                    >
                                        {{ credito.cantidad_cuotas }} cuotas
                                    </td>

                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-black ring-1"
                                            :class="estadoClase(credito.estado)"
                                        >
                                            {{ credito.estado }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <Link
                                                :href="`/alumno/mis-creditos/${credito.id}`"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200"
                                                title="Ver detalle del crédito"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                                    />
                                                    <path
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"
                                                    />
                                                </svg>
                                            </Link>

                                            <button
                                                type="button"
                                                @click="
                                                    abrirModalCuotas(credito)
                                                "
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100"
                                                title="Ver cuotas del crédito"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path d="M4 5h16M4 19h16" />
                                                    <path d="M4 12h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="!(creditos.data ?? []).length">
                                    <td
                                        colspan="7"
                                        class="px-6 py-12 text-center text-sm text-slate-500"
                                    >
                                        No se encontraron créditos registrados.
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
                                Mostrando {{ desdeCredito }} a
                                {{ hastaCredito }} de
                                {{ totalCreditos }} créditos
                            </p>

                            <div class="flex flex-wrap gap-2">
                                <template
                                    v-for="link in creditos.links ?? []"
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
