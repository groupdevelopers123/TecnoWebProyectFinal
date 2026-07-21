<template>
    <Head title="Gestión de Ofertas Académicas" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Listado de ofertas académicas
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Registra, busca, edita o cambia el estado de las ofertas
                    académicas.
                </p>
            </div>

            <a
                :href="route('admin.ofertas-academicas.create')"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                Nueva oferta
            </a>
        </div>

        <form class="mt-6 grid gap-4 md:grid-cols-3" @submit.prevent="search">
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Buscar</label
                >
                <input
                    v-model="filters.buscar"
                    type="text"
                    placeholder="Oferta, carrera, código, periodo o gestión"
                    autocomplete="off"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
            </div>

            <div class="flex items-end gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800"
                >
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    Buscar
                </button>

                <button
                    type="button"
                    @click="reset"
                    class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    Limpiar
                </button>
            </div>
        </form>
    </div>

    <div
        class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
    >
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Oferta
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Carrera
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Periodo
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Docente
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Cupos
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Fechas
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Estado
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    <tr
                        v-for="oferta in ofertasData"
                        :key="oferta.id"
                        class="transition hover:bg-slate-50"
                    >
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-violet-100 text-violet-700"
                                >
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ oferta.nombre }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Oferta académica
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ oferta.carrera.codigo }} -
                            {{ oferta.carrera.nombre }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ oferta.periodo_academico.nombre }} -
                            {{ oferta.periodo_academico.gestion }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            <span v-if="oferta.docente_detalle">
                                {{ oferta.docente_detalle.codigo }} -
                                {{ oferta.docente_detalle.user?.nombres }}
                                {{ oferta.docente_detalle.user?.apellidos }}
                            </span>
                            <span v-else class="text-slate-400"
                                >No asignado</span
                            >
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100"
                            >
                                {{ oferta.cupos_disponibles }} /
                                {{ oferta.cantidad_cupos }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ formatDate(oferta.fecha_inicio) }}
                            -
                            {{ formatDate(oferta.fecha_fin) }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                                :class="
                                    oferta.estado
                                        ? 'bg-green-50 text-green-700 ring-green-100'
                                        : 'bg-red-50 text-red-700 ring-red-100'
                                "
                            >
                                {{ oferta.estado ? "Activa" : "Inactiva" }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a
                                    :href="
                                        route(
                                            'admin.ofertas-academicas.show',
                                            oferta.id,
                                        )
                                    "
                                    title="Ver oferta"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200"
                                >
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a
                                    :href="
                                        route(
                                            'admin.ofertas-academicas.edit',
                                            oferta.id,
                                        )
                                    "
                                    title="Editar oferta"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100"
                                >
                                    <i
                                        class="fa-solid fa-pen-to-square text-sm"
                                    ></i>
                                </a>

                                <button
                                    type="button"
                                    @click="toggleOferta(oferta)"
                                    :title="
                                        oferta.estado
                                            ? 'Desactivar oferta'
                                            : 'Activar oferta'
                                    "
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5"
                                    :class="
                                        oferta.estado
                                            ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                            : 'bg-green-50 text-green-700 hover:bg-green-100'
                                    "
                                >
                                    <i
                                        class="fa-solid text-sm"
                                        :class="
                                            oferta.estado
                                                ? 'fa-trash-can'
                                                : 'fa-circle-check'
                                        "
                                    ></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="ofertasData.length === 0">
                        <td
                            colspan="8"
                            class="px-6 py-12 text-center text-sm text-slate-500"
                        >
                            No se encontraron ofertas académicas registradas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="pagination.last_page > 1"
            class="border-t border-slate-100 px-6 py-4"
        >
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm text-slate-500">
                    Página {{ pagination.current_page }} de
                    {{ pagination.last_page }} —
                    {{ pagination.total }} registros
                </p>

                <div class="flex gap-2">
                    <button
                        type="button"
                        :disabled="!pagination.prev_page_url"
                        @click="goToPage(pagination.prev_page_url)"
                        class="rounded-xl px-4 py-2 text-sm font-bold transition"
                        :class="
                            pagination.prev_page_url
                                ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                : 'bg-slate-50 text-slate-300'
                        "
                    >
                        Anterior
                    </button>

                    <button
                        type="button"
                        :disabled="!pagination.next_page_url"
                        @click="goToPage(pagination.next_page_url)"
                        class="rounded-xl px-4 py-2 text-sm font-bold transition"
                        :class="
                            pagination.next_page_url
                                ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                : 'bg-slate-50 text-slate-300'
                        "
                    >
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import { computed, reactive, watch } from "vue";

const page = usePage();
const ofertasData = computed(() => page.props.ofertas?.data || []);
const pagination = computed(
    () =>
        page.props.ofertas?.pagination || {
            current_page: 1,
            last_page: 1,
            total: 0,
        },
);
const filters = reactive({ buscar: page.props.request?.buscar || "" });
let debounceTimer = null;

function search() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            route("admin.ofertas-academicas.index"),
            {
                buscar: filters.buscar || undefined,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 250);
}

function reset() {
    filters.buscar = "";
    router.get(
        route("admin.ofertas-academicas.index"),
        {},
        { preserveState: true, replace: true },
    );
}

function goToPage(url) {
    if (!url) {
        return;
    }

    router.visit(url, { preserveState: true, replace: true });
}

function toggleOferta(oferta) {
    if (
        !window.confirm(
            "¿Está seguro de cambiar el estado de esta oferta académica?",
        )
    ) {
        return;
    }

    router.delete(route("admin.ofertas-academicas.destroy", oferta.id), {
        preserveScroll: true,
        preserveState: true,
    });
}

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

watch(
    () => filters.buscar,
    () => search(),
);
</script>
