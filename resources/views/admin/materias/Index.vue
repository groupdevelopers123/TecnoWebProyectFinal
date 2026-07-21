<template>
    <Head title="Gestión de Materias" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Listado de materias
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Registra, busca, edita o cambia el estado de las materias.
                </p>
            </div>

            <a
                :href="createUrl"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                Nueva materia
            </a>
        </div>

        <form @submit.prevent class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Buscar</label
                >
                <input
                    v-model="filters.buscar"
                    type="text"
                    placeholder="Código o nombre"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
            </div>

            <div class="flex items-end gap-3">
                <button
                    type="button"
                    @click="search"
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
                            Código
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Materia
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Docente
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Carga horaria
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
                        v-for="materia in materiasData"
                        :key="materia.id"
                        class="transition hover:bg-slate-50"
                    >
                        <td class="px-6 py-4 text-sm font-bold text-slate-700">
                            {{ materia.codigo }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"
                                >
                                    <i class="fa-solid fa-book-open"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ materia.nombre }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Materia académica
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ materia.docente || "No asignado" }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{
                                materia.carga_horaria
                                    ? materia.carga_horaria + " horas"
                                    : "No registrada"
                            }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                                :class="
                                    materia.estado
                                        ? 'bg-green-50 text-green-700 ring-green-100'
                                        : 'bg-red-50 text-red-700 ring-red-100'
                                "
                            >
                                {{ materia.estado ? "Activa" : "Inactiva" }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a
                                    :href="showUrl(materia.id)"
                                    title="Ver materia"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200"
                                >
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a
                                    :href="editUrl(materia.id)"
                                    title="Editar materia"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100"
                                >
                                    <i
                                        class="fa-solid fa-pen-to-square text-sm"
                                    ></i>
                                </a>

                                <button
                                    type="button"
                                    @click="toggleMateriaEstado(materia)"
                                    :title="
                                        materia.estado
                                            ? 'Desactivar materia'
                                            : 'Activar materia'
                                    "
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5"
                                    :class="
                                        materia.estado
                                            ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                            : 'bg-green-50 text-green-700 hover:bg-green-100'
                                    "
                                >
                                    <i
                                        class="fa-solid text-sm"
                                        :class="
                                            materia.estado
                                                ? 'fa-trash-can'
                                                : 'fa-circle-check'
                                        "
                                    ></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="materiasData.length === 0">
                        <td
                            colspan="6"
                            class="px-6 py-12 text-center text-sm text-slate-500"
                        >
                            No se encontraron materias registradas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-6 py-4">
            <div
                v-if="pagination.last_page > 1"
                class="flex items-center justify-between gap-3"
            >
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
const createUrl = computed(() => route("admin.materias.create"));
const materiasData = computed(() => page.props.materias?.data || []);
const pagination = computed(
    () =>
        page.props.materias?.pagination || {
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
            route("admin.materias.index"),
            { buscar: filters.buscar || undefined },
            { preserveState: true, replace: true },
        );
    }, 250);
}

function reset() {
    filters.buscar = "";
    router.get(
        route("admin.materias.index"),
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

function showUrl(id) {
    return route("admin.materias.show", id);
}

function editUrl(id) {
    return route("admin.materias.edit", id);
}

function toggleMateriaEstado(materia) {
    if (!window.confirm("¿Está seguro de cambiar el estado de esta materia?")) {
        return;
    }

    router.delete(route("admin.materias.destroy", materia.id), {
        preserveScroll: true,
        preserveState: true,
    });
}

watch(
    () => filters.buscar,
    () => search(),
);
</script>
