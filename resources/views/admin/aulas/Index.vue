<template>
    <Head title="Gestión de Aulas" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Listado de aulas
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Busca, registra, edita o cambia la disponibilidad de las
                    aulas.
                </p>
            </div>

            <a
                :href="createUrl"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                Nueva aula
            </a>
        </div>

        <form
            id="aulas-search-form"
            @submit.prevent
            class="mt-6 grid gap-4 md:grid-cols-3"
        >
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Buscar</label
                >
                <input
                    type="text"
                    v-model="filters.buscar"
                    placeholder="Código, nombre, ubicación o piso"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Disponibilidad</label
                >
                <select
                    v-model="filters.disponible"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Todas</option>
                    <option value="1">Disponibles</option>
                    <option value="0">No disponibles</option>
                </select>
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
        id="aulas-list"
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
                            Aula
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Ubicación
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Capacidad
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Dimensiones
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
                        v-for="aula in aulasData"
                        :key="aula.id"
                        v-bind="rowAttrs(aula.id)"
                    >
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm font-bold text-slate-700"
                        >
                            {{ aula.codigo }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-700"
                                >
                                    <i class="fa-solid fa-school"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">
                                        {{ aula.nombre }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                        >
                            {{ aula.ubicacion ?? "No registrada" }}
                        </td>
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700"
                        >
                            {{ aula.capacidad }} estudiantes
                        </td>
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                        >
                            {{
                                aula.largo && aula.ancho
                                    ? aula.largo + "m x " + aula.ancho + "m"
                                    : "No registradas"
                            }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span
                                v-if="aula.disponible"
                                class="text-green-700 font-bold"
                                >Disponible</span
                            >
                            <span v-else class="text-red-700 font-bold"
                                >No disponible</span
                            >
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a
                                    :href="showUrl(aula.id)"
                                    class="inline-flex items-center justify-center rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200"
                                    :aria-label="`Ver aula ${aula.nombre}`"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                <a
                                    :href="editUrl(aula.id)"
                                    class="inline-flex items-center justify-center rounded-full p-2 bg-blue-600 text-white hover:bg-blue-700"
                                    :aria-label="`Editar aula ${aula.nombre}`"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <button
                                    @click="toggleDisponibilidad(aula.id)"
                                    class="inline-flex items-center justify-center rounded-full p-2 bg-rose-50 text-rose-700 hover:bg-rose-100"
                                    :aria-label="`Eliminar / cambiar disponibilidad de ${aula.nombre}`"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="aulasData.length === 0">
                        <td
                            colspan="7"
                            class="px-6 py-12 text-center text-sm text-slate-500"
                        >
                            No se encontraron aulas registradas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            class="border-t border-slate-100 px-6 py-4"
            v-html="paginationHtml"
        ></div>
    </div>
</template>

<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { useRowHighlight } from "../../composables/useRowHighlight";

const page = usePage();

// initial aulas and pagination come from server
const initialAulas = page.props.aulas ?? {};

const filters = ref({
    buscar: page.props.request?.buscar ?? "",
    disponible: page.props.request?.disponible ?? "",
});

const aulasData = ref((initialAulas.data ?? []).map((a) => ({ ...a })));
const pagination = ref(
    initialAulas.pagination ?? {
        current_page: 1,
        last_page: 1,
        total: aulasData.value.length,
    },
);
const { rowAttrs } = useRowHighlight("highlight_aula");

const createUrl = computed(() => route("admin.aulas.create"));

function showUrl(id) {
    return route("admin.aulas.show", id);
}
function editUrl(id) {
    return route("admin.aulas.edit", id);
}

let debounceTimer = null;

function buildQuery() {
    const params = new URLSearchParams();
    if (filters.value.buscar) params.set("buscar", filters.value.buscar);
    if (filters.value.disponible !== "")
        params.set("disponible", filters.value.disponible);
    return params.toString();
}

function fetchList(url) {
    fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
        .then((r) => r.json())
        .then(handleResponse)
        .catch(() => {
            aulasData.value = [];
            pagination.value = { current_page: 1, last_page: 1, total: 0 };
        });
}

function handleResponse(data) {
    aulasData.value = data.data || [];
    pagination.value = data.pagination || {
        current_page: 1,
        last_page: 1,
        total: aulasData.value.length,
    };
}

function search() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const qs = buildQuery();
        const url = qs
            ? route("admin.aulas.index") + "?" + qs
            : route("admin.aulas.index");
        fetchList(url);
    }, 250);
}

function reset() {
    filters.value.buscar = "";
    filters.value.disponible = "";
    search();
}

watch(
    filters,
    () => {
        search();
    },
    { deep: true },
);

function toggleDisponibilidad(id) {
    // call toggle via form submit to controller route (destroy toggles disponibilidad)
    if (!confirm("¿Cambiar disponibilidad del aula?")) return;
    fetch(route("admin.aulas.destroy", id), {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    }).then(() => search());
}

const paginationHtml = computed(() => {
    if (!pagination.value || pagination.value.last_page <= 1) return "";
    const prev = pagination.value.prev_page_url
        ? `<button onclick="fetch('${pagination.value.prev_page_url}', {headers: {'X-Requested-With': 'XMLHttpRequest'}}).then(r => r.json()).then(data => { window.dispatchEvent(new CustomEvent('aulas:update', {detail: data})); })" class=\"rounded-xl px-4 py-2 text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200\">Anterior</button>`
        : "";
    const next = pagination.value.next_page_url
        ? `<button onclick="fetch('${pagination.value.next_page_url}', {headers: {'X-Requested-With': 'XMLHttpRequest'}}).then(r => r.json()).then(data => { window.dispatchEvent(new CustomEvent('aulas:update', {detail: data})); })" class=\"rounded-xl px-4 py-2 text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200\">Siguiente</button>`
        : "";
    return `<div class=\"flex items-center justify-between gap-3\"><p class=\"text-sm text-slate-500\">Página ${pagination.value.current_page} de ${pagination.value.last_page} — ${pagination.value.total} registros</p><div class=\"flex gap-2\">${prev}${next}</div></div>`;
});

// listen for custom events dispatched by inline pagination buttons to update data
window.addEventListener("aulas:update", (e) => {
    handleResponse(e.detail);
});
</script>
