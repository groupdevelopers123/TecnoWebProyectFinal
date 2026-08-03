<template>
    <Head title="Gestión de Usuarios" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Listado de usuarios
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Busca, filtra, edita o desactiva usuarios del sistema.
                </p>
            </div>

            <a
                :href="createUrl"
                class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                >+ Nuevo usuario</a
            >
        </div>

        <form
            id="usuarios-search-form"
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
                    placeholder="Nombre, CI o email"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Rol</label
                >
                <select
                    v-model="filters.role_id"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Todos los roles</option>
                    <option v-for="r in roles" :key="r.id" :value="r.id">
                        {{
                            r.nombre.charAt(0).toUpperCase() + r.nombre.slice(1)
                        }}
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button
                    type="button"
                    @click="search"
                    class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800"
                >
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
        id="usuarios-list"
        class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
    >
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            CI
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Nombre completo
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Email
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Rol
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
                        v-for="u in usuariosData"
                        :key="u.id"
                        :data-user-row="u.id"
                        :class="rowClass(u.id)"
                    >
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700"
                        >
                            {{ u.ci }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-black text-blue-700"
                                >
                                    {{ (u.nombres || "").charAt(0) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">
                                        {{ u.nombres }} {{ u.apellidos }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                        >
                            {{ u.email }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span
                                class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100"
                                >{{ u.role }}</span
                            >
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span
                                :class="
                                    u.estado
                                        ? 'text-green-700 font-bold'
                                        : 'text-red-700 font-bold'
                                "
                                >{{ u.estado ? "Activo" : "Inactivo" }}</span
                            >
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a
                                    :href="showUrl(u.id)"
                                    class="inline-flex items-center justify-center rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200"
                                    :aria-label="`Ver usuario ${u.nombres} ${u.apellidos}`"
                                    ><i class="fa-solid fa-eye"></i
                                ></a>
                                <a
                                    :href="editUrl(u.id)"
                                    class="inline-flex items-center justify-center rounded-full p-2 bg-blue-600 text-white hover:bg-blue-700"
                                    :aria-label="`Editar usuario ${u.nombres} ${u.apellidos}`"
                                    ><i class="fa-solid fa-pen-to-square"></i
                                ></a>
                                <button
                                    @click="toggleEstado(u.id)"
                                    class="inline-flex items-center justify-center rounded-full p-2 bg-rose-50 text-rose-700 hover:bg-rose-100"
                                    :aria-label="`Cambiar estado de ${u.nombres} ${u.apellidos}`"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="usuariosData.length === 0">
                        <td
                            colspan="6"
                            class="px-6 py-12 text-center text-sm text-slate-500"
                        >
                            No se encontraron usuarios registrados.
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
import { Head, usePage } from "@inertiajs/vue3";
import {
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    computed,
    watch,
} from "vue";

const page = usePage();
const initial = page.props.usuarios ?? {};
const roles = page.props.roles || [];

const filters = ref({
    buscar: page.props.request?.buscar ?? "",
    role_id: page.props.request?.role_id ?? "",
});
const highlightedUserId = ref(
    page.props.request?.highlight_user
        ? Number(page.props.request.highlight_user)
        : null,
);
const usuariosData = ref((initial.data ?? []).map((u) => ({ ...u })));
const pagination = ref(
    initial.pagination ?? {
        current_page: 1,
        last_page: 1,
        total: usuariosData.value.length,
    },
);

let highlightTimer = null;

function clearHighlight() {
    highlightedUserId.value = null;
    if (highlightTimer) {
        clearTimeout(highlightTimer);
        highlightTimer = null;
    }
}

function flashHighlightedUser() {
    if (!highlightedUserId.value) return;

    highlightTimer = setTimeout(() => {
        highlightedUserId.value = null;
        highlightTimer = null;
    }, 3000);

    nextTick(() => {
        const row = document.querySelector(
            `[data-user-row="${highlightedUserId.value}"]`,
        );

        if (row) {
            row.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    });
}

onMounted(() => {
    flashHighlightedUser();
});

onBeforeUnmount(() => {
    clearHighlight();
});

const createUrl = computed(() => route("admin.usuarios.create"));
function showUrl(id) {
    return route("admin.usuarios.show", id);
}
function editUrl(id) {
    return route("admin.usuarios.edit", id);
}

function rowClass(id) {
    return id === highlightedUserId.value
        ? "bg-emerald-50 outline outline-2 outline-emerald-400 outline-offset-[-2px] transition-shadow duration-300"
        : "transition hover:bg-slate-50";
}

let debounceTimer = null;
function buildQuery() {
    const params = new URLSearchParams();
    if (filters.value.buscar) params.set("buscar", filters.value.buscar);
    if (filters.value.role_id) params.set("role_id", filters.value.role_id);
    return params.toString();
}
function fetchList(url) {
    fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
        .then((r) => r.json())
        .then(handleResponse)
        .catch(() => {
            usuariosData.value = [];
            pagination.value = { current_page: 1, last_page: 1, total: 0 };
        });
}
function handleResponse(data) {
    usuariosData.value = data.data || [];
    pagination.value = data.pagination || {
        current_page: 1,
        last_page: 1,
        total: usuariosData.value.length,
    };
}
function search() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const qs = buildQuery();
        const url = qs
            ? route("admin.usuarios.index") + "?" + qs
            : route("admin.usuarios.index");
        fetchList(url);
    }, 250);
}
function reset() {
    filters.value.buscar = "";
    filters.value.role_id = "";
    search();
}
watch(filters, () => search(), { deep: true });
function toggleEstado(id) {
    if (!confirm("¿Cambiar estado del usuario?")) return;
    fetch(route("admin.usuarios.destroy", id), {
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
        ? `<button onclick="fetch('${pagination.value.prev_page_url}', {headers: {'X-Requested-With': 'XMLHttpRequest'}}).then(r => r.json()).then(data => { window.dispatchEvent(new CustomEvent('usuarios:update',{detail:data})); })" class=\"rounded-xl px-4 py-2 text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200\">Anterior</button>`
        : "";
    const next = pagination.value.next_page_url
        ? `<button onclick="fetch('${pagination.value.next_page_url}', {headers: {'X-Requested-With': 'XMLHttpRequest'}}).then(r => r.json()).then(data => { window.dispatchEvent(new CustomEvent('usuarios:update',{detail:data})); })" class=\"rounded-xl px-4 py-2 text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200\">Siguiente</button>`
        : "";
    return `<div class=\"flex items-center justify-between gap-3\"><p class=\"text-sm text-slate-500\">Página ${pagination.value.current_page} de ${pagination.value.last_page} — ${pagination.value.total} registros</p><div class=\"flex gap-2\">${prev}${next}</div></div>`;
});
window.addEventListener("usuarios:update", (e) => {
    handleResponse(e.detail);
});
</script>
