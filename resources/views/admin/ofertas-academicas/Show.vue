<template>
    <Head title="Detalle de Oferta Académica" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-3xl bg-violet-100 text-2xl text-violet-700"
                >
                    <i class="fa-solid fa-layer-group"></i>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900">
                        {{ oferta.nombre }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ oferta.carrera?.codigo }} -
                        {{ oferta.carrera?.nombre }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a
                    :href="editUrl"
                    class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                >
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                    Editar
                </a>

                <a
                    :href="indexUrl"
                    class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    Volver
                </a>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                <p class="text-xs font-bold uppercase text-slate-400">Nombre</p>
                <p class="mt-1 font-bold text-slate-800">{{ oferta.nombre }}</p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Carrera
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ oferta.carrera?.codigo }} - {{ oferta.carrera?.nombre }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Periodo académico
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ oferta.periodo_academico?.nombre }} -
                    {{ oferta.periodo_academico?.gestion }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Docente
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    <span v-if="oferta.docente_detalle">
                        {{ oferta.docente_detalle.codigo }} -
                        {{ oferta.docente_detalle.user?.nombres }}
                        {{ oferta.docente_detalle.user?.apellidos }}
                    </span>
                    <span v-else class="text-slate-400">No asignado</span>
                </p>
            </div>

            <div class="rounded-2xl bg-violet-50 p-4">
                <p class="text-xs font-bold uppercase text-violet-500">
                    Cantidad de cupos
                </p>
                <p class="mt-1 text-xl font-black text-violet-700">
                    {{ oferta.cantidad_cupos }}
                </p>
            </div>

            <div class="rounded-2xl bg-emerald-50 p-4">
                <p class="text-xs font-bold uppercase text-emerald-500">
                    Cupos disponibles
                </p>
                <p class="mt-1 text-xl font-black text-emerald-700">
                    {{ oferta.cupos_disponibles }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Fecha de inicio
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ formatDate(oferta.fecha_inicio) }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Fecha de finalización
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ formatDate(oferta.fecha_fin) }}
                </p>
            </div>

            <div class="mt-2 md:col-span-2">
                <h3 class="text-lg font-black text-slate-900">
                    Precios de la oferta académica
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Información económica registrada para esta oferta.
                </p>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-700"
                    >
                        <i class="fa-solid fa-file-signature"></i>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase text-blue-500">
                            Precio de matrícula
                        </p>
                        <p class="mt-1 text-xl font-black text-blue-700">
                            Bs {{ formatMoney(oferta.precio_matricula) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-5">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-700"
                    >
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase text-cyan-600">
                            Precio de mensualidad
                        </p>
                        <p class="mt-1 text-xl font-black text-cyan-700">
                            Bs {{ formatMoney(oferta.precio_mensualidad) }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="rounded-2xl border border-amber-100 bg-amber-50 p-5 md:col-span-2"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-700"
                    >
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase text-amber-600">
                            Precio de la carrera completa
                        </p>
                        <p class="mt-1 text-2xl font-black text-amber-700">
                            Bs {{ formatMoney(oferta.precio_carrera_completa) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-violet-50 p-4 md:col-span-2">
                <p class="text-xs font-bold uppercase text-violet-500">
                    Estado
                </p>
                <div class="mt-2">
                    <span
                        class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-bold"
                        :class="
                            oferta.estado
                                ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700'
                        "
                    >
                        <span
                            class="h-2 w-2 rounded-full"
                            :class="
                                oferta.estado ? 'bg-green-500' : 'bg-red-500'
                            "
                        ></span>
                        {{ oferta.estado ? "Activa" : "Inactiva" }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const oferta = computed(() => page.props.oferta || {});
const editUrl = computed(() =>
    oferta.value?.id
        ? route("admin.ofertas-academicas.edit", oferta.value.id)
        : route("admin.ofertas-academicas.index"),
);
const indexUrl = computed(() => route("admin.ofertas-academicas.index"));

function formatDate(value) {
    if (!value) {
        return "-";
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat("es-BO").format(date);
}

function formatMoney(value) {
    const number = Number(value ?? 0);

    return new Intl.NumberFormat("es-BO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(number);
}
</script>
