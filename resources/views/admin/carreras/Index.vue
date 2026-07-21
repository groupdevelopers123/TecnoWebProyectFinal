<template>
    <Head title="Gestión de Carreras" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Listado de carreras
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Registra, busca, edita o cambia el estado de las carreras.
                </p>
            </div>

            <a
                :href="createUrl"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                Nueva carrera
            </a>
        </div>

        <form @submit.prevent class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Buscar</label
                >
                <input
                    type="text"
                    v-model="filters.buscar"
                    placeholder="Código, nombre o régimen"
                    autocomplete="off"
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
                            Carrera
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Duración
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Régimen
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
                        v-for="carrera in carrerasData"
                        :key="carrera.id"
                        class="transition hover:bg-slate-50"
                    >
                        <td class="px-6 py-4 text-sm font-bold text-slate-700">
                            {{ carrera.codigo }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-700"
                                >
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ carrera.nombre }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Carrera académica
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{
                                carrera.duracion
                                    ? carrera.duracion + " periodos"
                                    : "No registrada"
                            }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ carrera.regimen_academico ?? "No registrado" }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                                :class="
                                    carrera.estado
                                        ? 'bg-green-50 text-green-700 ring-green-100'
                                        : 'bg-red-50 text-red-700 ring-red-100'
                                "
                            >
                                {{ carrera.estado ? "Activa" : "Inactiva" }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <button
                                    type="button"
                                    @click="openModal(carrera.id)"
                                    title="Gestionar materias"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition hover:-translate-y-0.5 hover:bg-emerald-100"
                                >
                                    <i
                                        class="fa-solid fa-book-open text-sm"
                                    ></i>
                                </button>

                                <a
                                    :href="showUrl(carrera.id)"
                                    title="Ver carrera"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200"
                                >
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <a
                                    :href="editUrl(carrera.id)"
                                    title="Editar carrera"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100"
                                >
                                    <i
                                        class="fa-solid fa-pen-to-square text-sm"
                                    ></i>
                                </a>

                                <button
                                    type="button"
                                    @click="toggleCarreraEstado(carrera.id)"
                                    :title="
                                        carrera.estado
                                            ? 'Desactivar carrera'
                                            : 'Activar carrera'
                                    "
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5"
                                    :class="
                                        carrera.estado
                                            ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                            : 'bg-green-50 text-green-700 hover:bg-green-100'
                                    "
                                >
                                    <i
                                        class="fa-solid fa-trash-can text-sm"
                                    ></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="carrerasData.length === 0">
                        <td
                            colspan="6"
                            class="px-6 py-12 text-center text-sm text-slate-500"
                        >
                            No se encontraron carreras registradas.
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

    <div>
        <div
            v-for="carrera in carrerasData"
            :key="`modal-${carrera.id}`"
            v-show="activeModalId === carrera.id"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm"
            @click.self="closeModal"
        >
            <div
                class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl"
            >
                <div
                    class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-5"
                >
                    <div>
                        <h2 class="text-xl font-black text-slate-900">
                            Materias de {{ carrera.nombre }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Código: {{ carrera.codigo }} — Régimen:
                            {{ carrera.regimen_academico ?? "No registrado" }}
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="closeModal"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-red-50 hover:text-red-600"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-6">
                    <div
                        class="mb-6 rounded-3xl border border-blue-100 bg-blue-50 p-5"
                    >
                        <h3 class="text-lg font-black text-slate-900">
                            Agregar materia
                        </h3>

                        <form
                            class="mt-5 grid gap-4 md:grid-cols-3"
                            @submit.prevent="assignMateria(carrera.id)"
                        >
                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                    >Materia</label
                                >
                                <select
                                    v-model="modalForms[carrera.id].materia_id"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                >
                                    <option value="">Seleccione</option>
                                    <option
                                        v-for="materia in availableMaterias(
                                            carrera,
                                        )"
                                        :key="materia.id"
                                        :value="materia.id"
                                    >
                                        {{ materia.codigo }} -
                                        {{ materia.nombre }}
                                    </option>
                                </select>
                                <p
                                    class="mt-1 text-sm text-red-600"
                                    aria-live="polite"
                                >
                                    {{
                                        modalErrors[carrera.id]?.materia_id ||
                                        ""
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                    >{{ periodLabel(carrera) }}</label
                                >
                                <select
                                    v-model="
                                        modalForms[carrera.id].periodo_numero
                                    "
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                >
                                    <option value="">Sin asignar</option>
                                    <option
                                        v-for="periodo in periodOptions(
                                            carrera.duracion,
                                        )"
                                        :key="periodo"
                                        :value="periodo"
                                    >
                                        {{ periodo }}
                                    </option>
                                </select>
                                <p
                                    class="mt-1 text-sm text-red-600"
                                    aria-live="polite"
                                >
                                    {{
                                        modalErrors[carrera.id]
                                            ?.periodo_numero || ""
                                    }}
                                </p>
                            </div>

                            <div class="flex items-end">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                                >
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    Asignar materia
                                </button>
                            </div>
                        </form>
                    </div>

                    <div
                        class="overflow-hidden rounded-3xl border border-slate-200 bg-white"
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
                                            Carga horaria
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                        >
                                            {{ periodLabel(carrera) }}
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

                                <tbody
                                    class="divide-y divide-slate-100 bg-white"
                                >
                                    <tr
                                        v-if="
                                            !carrera.materias_asignadas.length
                                        "
                                    >
                                        <td
                                            colspan="6"
                                            class="px-6 py-12 text-center text-sm text-slate-500"
                                        >
                                            Esta carrera todavía no tiene
                                            materias asignadas.
                                        </td>
                                    </tr>

                                    <tr
                                        v-for="asignacion in carrera.materias_asignadas"
                                        :key="asignacion.id"
                                    >
                                        <td
                                            class="px-6 py-4 text-sm font-bold text-slate-700"
                                        >
                                            {{ asignacion.materia.codigo }}
                                        </td>

                                        <td
                                            class="px-6 py-4 text-sm font-bold text-slate-900"
                                        >
                                            {{ asignacion.materia.nombre }}
                                        </td>

                                        <td
                                            class="px-6 py-4 text-sm text-slate-600"
                                        >
                                            {{
                                                asignacion.materia.carga_horaria
                                                    ? asignacion.materia
                                                          .carga_horaria +
                                                      " horas"
                                                    : "No registrada"
                                            }}
                                        </td>

                                        <td class="px-6 py-4">
                                            <select
                                                v-model="
                                                    asignacion.periodo_numero
                                                "
                                                class="w-28 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                            >
                                                <option value="">-</option>
                                                <option
                                                    v-for="periodo in periodOptions(
                                                        carrera.duracion,
                                                    )"
                                                    :key="`periodo-${carrera.id}-${asignacion.id}-${periodo}`"
                                                    :value="periodo"
                                                >
                                                    {{ periodo }}
                                                </option>
                                            </select>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                                                :class="
                                                    asignacion.estado
                                                        ? 'bg-green-50 text-green-700 ring-green-100'
                                                        : 'bg-red-50 text-red-700 ring-red-100'
                                                "
                                            >
                                                {{
                                                    asignacion.estado
                                                        ? "Activa"
                                                        : "Inactiva"
                                                }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <button
                                                    type="button"
                                                    @click="
                                                        updateAsignacion(
                                                            carrera.id,
                                                            asignacion,
                                                        )
                                                    "
                                                    title="Guardar cambios"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100"
                                                >
                                                    <i
                                                        class="fa-solid fa-floppy-disk text-sm"
                                                    ></i>
                                                </button>

                                                <button
                                                    type="button"
                                                    @click="
                                                        toggleAsignacionEstado(
                                                            carrera.id,
                                                            asignacion,
                                                        )
                                                    "
                                                    :title="
                                                        asignacion.estado
                                                            ? 'Desactivar materia de la carrera'
                                                            : 'Activar materia de la carrera'
                                                    "
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5"
                                                    :class="
                                                        asignacion.estado
                                                            ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                                            : 'bg-green-50 text-green-700 hover:bg-green-100'
                                                    "
                                                >
                                                    <i
                                                        class="fa-solid fa-trash-can text-sm"
                                                    ></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed, reactive, ref, watch } from "vue";

const page = usePage();

const createUrl = computed(() => route("admin.carreras.create"));
const initialCarreras = page.props.carreras ?? { data: [], pagination: {} };
const carrerasData = ref(
    (initialCarreras.data ?? []).map((item) =>
        JSON.parse(JSON.stringify(item)),
    ),
);
const pagination = ref(
    initialCarreras.pagination ?? {
        current_page: 1,
        last_page: 1,
        total: carrerasData.value.length,
    },
);
const materiasGlobales = ref(
    (page.props.materias ?? []).map((item) => ({ ...item })),
);
const filters = reactive({ buscar: page.props.request?.buscar ?? "" });
const activeModalId = ref(null);
const modalForms = reactive({});
const modalErrors = reactive({});
let debounceTimer = null;

function cloneCarreras(items) {
    return (items ?? []).map((item) => JSON.parse(JSON.stringify(item)));
}

function ensureModalState(carreraId) {
    if (!modalForms[carreraId]) {
        modalForms[carreraId] = {
            materia_id: "",
            periodo_numero: "",
        };
    }

    if (!modalErrors[carreraId]) {
        modalErrors[carreraId] = {
            materia_id: "",
            periodo_numero: "",
        };
    }
}

watch(
    carrerasData,
    () => {
        carrerasData.value.forEach((carrera) => ensureModalState(carrera.id));
    },
    { immediate: true },
);

function showUrl(id) {
    return route("admin.carreras.show", id);
}

function editUrl(id) {
    return route("admin.carreras.edit", id);
}

function buildQuery() {
    const params = new URLSearchParams();
    if (filters.buscar) {
        params.set("buscar", filters.buscar);
    }
    return params.toString();
}

function handleResponse(data) {
    carrerasData.value = cloneCarreras(data.data || []);
    pagination.value = data.pagination || {
        current_page: 1,
        last_page: 1,
        total: carrerasData.value.length,
    };
    if (Array.isArray(data.materias)) {
        materiasGlobales.value = data.materias.map((item) => ({ ...item }));
    }
    carrerasData.value.forEach((carrera) => ensureModalState(carrera.id));
}

function fetchCarreras(url) {
    fetch(url, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then(handleResponse)
        .catch(() => {
            carrerasData.value = [];
            pagination.value = { current_page: 1, last_page: 1, total: 0 };
        });
}

function search(url = null) {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const query = buildQuery();
        const target =
            url || route("admin.carreras.index") + (query ? `?${query}` : "");
        fetchCarreras(target);
    }, 250);
}

function reset() {
    filters.buscar = "";
    search();
}

watch(
    () => filters.buscar,
    () => search(),
);

function openModal(id) {
    ensureModalState(id);
    activeModalId.value = id;
}

function closeModal() {
    activeModalId.value = null;
}

function periodLabel(carrera) {
    if (carrera.regimen_academico === "Anual") {
        return "Año";
    }

    if (carrera.regimen_academico === "Modular") {
        return "Módulo";
    }

    return "Semestre";
}

function periodOptions(duracion) {
    const limit = duracion || 12;
    return Array.from({ length: limit }, (_, index) => index + 1);
}

function availableMaterias(carrera) {
    const assignedIds = new Set(
        (carrera.materias_asignadas || []).map(
            (asignacion) => asignacion.materia_id,
        ),
    );
    return materiasGlobales.value.filter(
        (materia) => !assignedIds.has(materia.id),
    );
}

function flashMessage(message) {
    window.dispatchEvent(
        new CustomEvent("flash-message", {
            detail: { type: "success", message },
        }),
    );
}

function validateModalForm(carreraId) {
    ensureModalState(carreraId);
    modalErrors[carreraId].materia_id = "";
    modalErrors[carreraId].periodo_numero = "";

    const form = modalForms[carreraId];
    let valid = true;

    if (!form.materia_id) {
        modalErrors[carreraId].materia_id = "Debe seleccionar una materia.";
        valid = false;
    }

    if (
        form.periodo_numero !== "" &&
        form.periodo_numero !== null &&
        !/^[0-9]+$/.test(String(form.periodo_numero))
    ) {
        modalErrors[carreraId].periodo_numero =
            "El periodo debe ser un número válido.";
        valid = false;
    }

    return valid;
}

function assignMateria(carreraId) {
    if (!validateModalForm(carreraId)) {
        return;
    }

    const form = modalForms[carreraId];
    const body = new FormData();
    body.append("materia_id", form.materia_id);
    if (form.periodo_numero !== "" && form.periodo_numero !== null) {
        body.append("periodo_numero", form.periodo_numero);
    }

    body.append(
        "_token",
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "",
    );

    fetch(route("admin.carreras.materias.store", carreraId), {
        method: "POST",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
        body,
    })
        .then(async (response) => {
            if (!response.ok) {
                const text = await response.text();
                throw new Error(text || "No se pudo asignar la materia.");
            }

            return response;
        })
        .then(() => {
            modalForms[carreraId].materia_id = "";
            modalForms[carreraId].periodo_numero = "";
            closeModal();
            search();
        })
        .catch(() => {
            modalErrors[carreraId].materia_id =
                "No se pudo asignar la materia.";
        });
}

function updateAsignacion(carreraId, asignacion) {
    const body = new FormData();
    body.append("_method", "PUT");
    body.append(
        "_token",
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "",
    );
    if (
        asignacion.periodo_numero !== "" &&
        asignacion.periodo_numero !== null
    ) {
        body.append("periodo_numero", asignacion.periodo_numero);
    }

    fetch(route("admin.carreras.materias.update", [carreraId, asignacion.id]), {
        method: "POST",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
        body,
    }).then(() => search());
}

function toggleAsignacionEstado(carreraId, asignacion) {
    if (
        !window.confirm("¿Está seguro de retirar esta materia de la carrera?")
    ) {
        return;
    }

    const body = new FormData();
    body.append("_method", "DELETE");
    body.append(
        "_token",
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "",
    );

    fetch(
        route("admin.carreras.materias.destroy", [carreraId, asignacion.id]),
        {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
            body,
        },
    ).then(() => search());
}

function toggleCarreraEstado(id) {
    if (!window.confirm("¿Está seguro de cambiar el estado de esta carrera?")) {
        return;
    }

    const body = new FormData();
    body.append("_method", "DELETE");
    body.append(
        "_token",
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "",
    );

    fetch(route("admin.carreras.destroy", id), {
        method: "POST",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
        body,
    }).then(() => search());
}

const paginationHtml = computed(() => {
    if (!pagination.value || pagination.value.last_page <= 1) {
        return "";
    }

    const prev = pagination.value.prev_page_url
        ? `<button type="button" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-200" onclick="window.dispatchEvent(new CustomEvent('carreras-pagination', { detail: '${pagination.value.prev_page_url}' }))">Anterior</button>`
        : "";

    const next = pagination.value.next_page_url
        ? `<button type="button" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-200" onclick="window.dispatchEvent(new CustomEvent('carreras-pagination', { detail: '${pagination.value.next_page_url}' }))">Siguiente</button>`
        : "";

    return `
        <div class="flex items-center justify-between gap-3">
            <p class="text-sm text-slate-500">
                Página ${pagination.value.current_page} de ${pagination.value.last_page}
                — ${pagination.value.total} registros
            </p>

            <div class="flex gap-2">
                ${prev}
                ${next}
            </div>
        </div>
    `;
});

window.addEventListener("carreras-pagination", (event) => {
    if (typeof event.detail === "string") {
        fetchCarreras(event.detail);
    }
});
</script>
