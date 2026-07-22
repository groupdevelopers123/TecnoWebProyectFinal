<template>
    <Head title="Gestión de Inscripciones" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Listado de inscripciones
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Registra, busca, edita o elimina inscripciones académicas.
                </p>
            </div>

            <a
                :href="route('admin.inscripciones.create')"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                Nueva inscripción
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
                    placeholder="Alumno, CI, oferta, carrera, periodo u observación"
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
        v-if="messageSuccess"
        class="mt-6 mb-4 flex items-center gap-3 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-700 shadow-sm"
    >
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ messageSuccess }}</span>
    </div>

    <div
        v-if="messageError"
        class="mt-6 mb-4 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700 shadow-sm"
    >
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>{{ messageError }}</span>
        </div>
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
                            Alumno
                        </th>
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
                            Fecha
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
                        v-for="inscripcion in inscripcionesData"
                        :key="inscripcion.id"
                        class="transition hover:bg-slate-50"
                    >
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-cyan-100 text-cyan-700"
                                >
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ nombreCompletoAlumno(inscripcion) }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{
                                            inscripcion.alumno.codigo ||
                                            "SIN-COD"
                                        }}
                                        <span v-if="inscripcion.alumno.ci"
                                            >/ CI:
                                            {{ inscripcion.alumno.ci }}</span
                                        >
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ inscripcion.oferta_academica.nombre }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ inscripcion.oferta_academica.carrera.codigo }} -
                            {{ inscripcion.oferta_academica.carrera.nombre }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700 ring-1 ring-cyan-100"
                            >
                                {{
                                    inscripcion.oferta_academica
                                        .periodo_academico.nombre
                                }}
                                {{
                                    inscripcion.oferta_academica
                                        .periodo_academico.gestion
                                }}
                                / Periodo {{ inscripcion.periodo_numero }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ formatDate(inscripcion.fecha_inscripcion) }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <button
                                    type="button"
                                    @click="openMateriasModal(inscripcion)"
                                    title="Gestionar materias inscritas"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition hover:-translate-y-0.5 hover:bg-emerald-100"
                                >
                                    <i
                                        class="fa-solid fa-book-open-reader text-sm"
                                    ></i>
                                </button>

                                <a
                                    :href="
                                        route(
                                            'admin.inscripciones.show',
                                            inscripcion.id,
                                        )
                                    "
                                    title="Ver inscripción"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200"
                                >
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a
                                    :href="
                                        route(
                                            'admin.inscripciones.edit',
                                            inscripcion.id,
                                        )
                                    "
                                    title="Editar inscripción"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100"
                                >
                                    <i
                                        class="fa-solid fa-pen-to-square text-sm"
                                    ></i>
                                </a>

                                <button
                                    type="button"
                                    @click="remove(inscripcion)"
                                    title="Eliminar inscripción"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:-translate-y-0.5 hover:bg-red-100"
                                >
                                    <i
                                        class="fa-solid fa-trash-can text-sm"
                                    ></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="inscripcionesData.length === 0">
                        <td
                            colspan="6"
                            class="px-6 py-12 text-center text-sm text-slate-500"
                        >
                            No se encontraron inscripciones registradas.
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

    <div
        v-if="materiasModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm"
    >
        <div
            class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl"
        >
            <div
                class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-5"
            >
                <div>
                    <h2 class="text-xl font-black text-slate-900">
                        Materias de la inscripción
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Gestión de materias inscritas del alumno.
                    </p>
                </div>

                <button
                    type="button"
                    @click="closeMateriasModal"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-red-50 hover:text-red-600"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6">
                <MateriasModal
                    v-if="selectedInscripcion"
                    :inscripcion="selectedInscripcion"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import { computed, ref, reactive, watch } from "vue";
import MateriasModal from "../inscripcion-materias/MateriasModal.vue";

const page = usePage();
const inscripcionesData = computed(() => page.props.inscripciones?.data || []);
const pagination = computed(
    () =>
        page.props.inscripciones?.pagination || {
            current_page: 1,
            last_page: 1,
            total: 0,
        },
);
const filters = reactive({ buscar: page.props.request?.buscar || "" });
const messageSuccess = computed(() => page.props.flash?.success || "");
const messageError = computed(() => page.props.flash?.error || "");
const materiasModalOpen = ref(false);
const selectedInscripcionId = ref(null);
const selectedInscripcion = computed(() =>
    inscripcionesData.value.find(
        (inscripcion) => inscripcion.id === selectedInscripcionId.value,
    ),
);
let debounceTimer = null;

function search() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            route("admin.inscripciones.index"),
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
        route("admin.inscripciones.index"),
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

function remove(inscripcion) {
    if (
        !window.confirm(
            "¿Está seguro de eliminar esta inscripción? Se devolverá un cupo a la oferta académica.",
        )
    ) {
        return;
    }

    router.delete(route("admin.inscripciones.destroy", inscripcion.id), {
        preserveScroll: true,
        preserveState: true,
    });
}

function nombreCompletoAlumno(inscripcion) {
    const nombres = inscripcion.alumno?.nombres || "";
    const apellidos = inscripcion.alumno?.apellidos || "";
    const nombreCompleto = `${nombres} ${apellidos}`.trim();

    return nombreCompleto || "Alumno sin usuario";
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

function openMateriasModal(inscripcion) {
    materiasModalOpen.value = true;
    selectedInscripcionId.value = inscripcion.id;
}

function closeMateriasModal() {
    materiasModalOpen.value = false;
    selectedInscripcionId.value = null;
}

watch(
    () => filters.buscar,
    () => search(),
);
</script>
