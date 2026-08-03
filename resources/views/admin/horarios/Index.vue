<template>
    <Head title="Gestión de Horarios" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Listado de horarios
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Organiza los horarios académicos evitando cruces de aula y
                    docente.
                </p>
            </div>

            <a
                :href="route('admin.horarios.create')"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                Nuevo horario
            </a>
        </div>

        <form class="mt-6 grid gap-4 md:grid-cols-4" @submit.prevent="search">
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Buscar</label
                >
                <input
                    v-model="filters.buscar"
                    type="text"
                    placeholder="Aula, docente, carrera o materia"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Día</label
                >
                <select
                    v-model="filters.dia"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Todos</option>
                    <option v-for="dia in dias" :key="dia" :value="dia">
                        {{ dia }}
                    </option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Turno</label
                >
                <select
                    v-model="filters.turno"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Todos</option>
                    <option v-for="turno in turnos" :key="turno" :value="turno">
                        {{ turno }}
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-3 md:col-span-4">
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
                            Día
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Horario
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
                            Aula
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
                        v-for="horario in horariosData"
                        :key="horario.id"
                        v-bind="rowAttrs(horario.id)"
                    >
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100"
                            >
                                {{ horario.dia }}
                            </span>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ horario.turno }}
                            </p>
                        </td>

                        <td class="px-6 py-4 text-sm font-bold text-slate-800">
                            {{ horario.hora_inicio }} - {{ horario.hora_fin }}
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ horario.materia_nombre }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ horario.carrera_nombre }}
                            </p>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ horario.docente_nombre || "No asignado" }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ horario.aula_nombre }} -
                            {{ horario.aula_codigo }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                                :class="
                                    horario.estado
                                        ? 'bg-green-50 text-green-700 ring-green-100'
                                        : 'bg-red-50 text-red-700 ring-red-100'
                                "
                            >
                                {{ horario.estado ? "Activo" : "Inactivo" }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a
                                    :href="
                                        route('admin.horarios.show', horario.id)
                                    "
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200"
                                >
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a
                                    :href="
                                        route('admin.horarios.edit', horario.id)
                                    "
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100"
                                >
                                    <i
                                        class="fa-solid fa-pen-to-square text-sm"
                                    ></i>
                                </a>

                                <button
                                    type="button"
                                    @click="toggleEstado(horario)"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5"
                                    :class="
                                        horario.estado
                                            ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                            : 'bg-green-50 text-green-700 hover:bg-green-100'
                                    "
                                >
                                    <i
                                        class="fa-solid text-sm"
                                        :class="
                                            horario.estado
                                                ? 'fa-trash-can'
                                                : 'fa-circle-check'
                                        "
                                    ></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="horariosData.length === 0">
                        <td
                            colspan="7"
                            class="px-6 py-12 text-center text-sm text-slate-500"
                        >
                            No existen horarios registrados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            class="border-t border-slate-100 px-6 py-4"
            v-if="pagination.last_page > 1"
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
import { useRowHighlight } from "../../composables/useRowHighlight";

const page = usePage();
const horariosData = computed(() => page.props.horarios?.data || []);
const pagination = computed(
    () =>
        page.props.horarios?.pagination || {
            current_page: 1,
            last_page: 1,
            total: 0,
        },
);
const dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
const turnos = ["Mañana", "Tarde", "Noche"];
const filters = reactive({
    buscar: page.props.request?.buscar || "",
    dia: page.props.request?.dia || "",
    turno: page.props.request?.turno || "",
});
const { rowAttrs } = useRowHighlight("highlight_horario");
let debounceTimer = null;

function search() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            route("admin.horarios.index"),
            {
                buscar: filters.buscar || undefined,
                dia: filters.dia || undefined,
                turno: filters.turno || undefined,
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
    filters.dia = "";
    filters.turno = "";
    router.get(
        route("admin.horarios.index"),
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

function toggleEstado(horario) {
    if (!window.confirm("¿Está seguro de cambiar el estado de este horario?")) {
        return;
    }

    router.delete(route("admin.horarios.destroy", horario.id), {
        preserveScroll: true,
        preserveState: true,
    });
}

watch(
    () => [filters.buscar, filters.dia, filters.turno],
    () => search(),
);
</script>
