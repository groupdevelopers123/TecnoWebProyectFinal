<template>
    <Head title="Seguimiento Académico" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Listado de seguimientos
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Registra, busca, edita o elimina seguimientos académicos.
                </p>
            </div>

            <Link
                :href="route('admin.seguimientos-academicos.create')"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                Nuevo seguimiento
            </Link>
        </div>

        <form
            @submit.prevent="buscarSeguimientos"
            class="mt-6 grid gap-4 md:grid-cols-3"
        >
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Buscar</label
                >
                <input
                    v-model="buscarTexto"
                    type="text"
                    placeholder="Alumno, docente, materia, carrera, estado u observación"
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
                    @click="limpiarBusqueda"
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
                            Alumno
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
                            Nota / Asistencia
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Estado
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
                        v-for="seguimiento in seguimientosData"
                        :key="seguimiento.id"
                        v-bind="rowAttrs(seguimiento.id)"
                    >
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ nombreCompleto(seguimiento.alumno) }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ seguimiento.carrera?.codigo }} -
                                {{ seguimiento.carrera?.nombre }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ seguimiento.materia?.codigo }} -
                                {{ seguimiento.materia?.nombre }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{
                                    seguimiento.observacion || "Sin observación"
                                }}
                            </p>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ nombreCompleto(seguimiento.docente) }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100"
                            >
                                Nota: {{ seguimiento.nota_final ?? "Sin nota" }}
                            </span>

                            <span
                                class="mt-1 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100"
                            >
                                Asis:
                                {{
                                    seguimiento.porcentaje_asistencia ??
                                    "Sin asistencia"
                                }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                                :class="
                                    badgeClasses(seguimiento.estado_academico)
                                "
                            >
                                {{ seguimiento.estado_academico }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ formatearFecha(seguimiento.fecha_registro) }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <Link
                                    :href="
                                        route(
                                            'admin.seguimientos-academicos.show',
                                            seguimiento.id,
                                        )
                                    "
                                    class="inline-flex items-center justify-center rounded-full bg-slate-100 p-2 text-slate-700 transition hover:bg-slate-200"
                                    :aria-label="`Ver seguimiento ${seguimiento.id}`"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                </Link>

                                <Link
                                    :href="
                                        route(
                                            'admin.seguimientos-academicos.edit',
                                            seguimiento.id,
                                        )
                                    "
                                    class="inline-flex items-center justify-center rounded-full bg-blue-600 p-2 text-white transition hover:bg-blue-700"
                                    :aria-label="`Editar seguimiento ${seguimiento.id}`"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </Link>

                                <button
                                    type="button"
                                    @click="eliminar(seguimiento.id)"
                                    class="inline-flex items-center justify-center rounded-full bg-rose-50 p-2 text-rose-700 transition hover:bg-rose-100"
                                    :aria-label="`Eliminar seguimiento ${seguimiento.id}`"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="seguimientosData.length === 0">
                        <td
                            colspan="7"
                            class="px-6 py-12 text-center text-sm text-slate-500"
                        >
                            No se encontraron seguimientos académicos
                            registrados.
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
                        @click="cambiarPagina(pagination.current_page - 1)"
                        :disabled="pagination.current_page <= 1"
                        class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-200 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Anterior
                    </button>

                    <button
                        type="button"
                        @click="cambiarPagina(pagination.current_page + 1)"
                        :disabled="
                            pagination.current_page >= pagination.last_page
                        "
                        class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-200 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useRowHighlight } from "../../composables/useRowHighlight";

const page = usePage();
const seguimientosPage = computed(
    () =>
        page.props.seguimientos ?? {
            data: [],
            pagination: { current_page: 1, last_page: 1, total: 0 },
        },
);
const seguimientosData = computed(() => seguimientosPage.value.data ?? []);
const pagination = computed(
    () =>
        seguimientosPage.value.pagination ?? {
            current_page: 1,
            last_page: 1,
            total: 0,
        },
);
const buscarTexto = ref(page.props.request?.buscar ?? "");
const { rowAttrs } = useRowHighlight("highlight_seguimiento");

function buscarSeguimientos() {
    router.get(
        route("admin.seguimientos-academicos.index"),
        { buscar: buscarTexto.value || undefined },
        { preserveState: true, replace: true, preserveScroll: true },
    );
}

function limpiarBusqueda() {
    buscarTexto.value = "";
    buscarSeguimientos();
}

function cambiarPagina(pagina) {
    if (!pagina || pagina < 1) {
        return;
    }

    router.get(
        route("admin.seguimientos-academicos.index"),
        { buscar: buscarTexto.value || undefined, page: pagina },
        { preserveState: true, replace: true, preserveScroll: true },
    );
}

function eliminar(id) {
    if (!window.confirm("¿Deseas eliminar este seguimiento académico?")) {
        return;
    }

    router.delete(route("admin.seguimientos-academicos.destroy", id), {
        preserveState: true,
        preserveScroll: true,
    });
}

function nombreCompleto(datos) {
    if (!datos) {
        return "Sin nombre";
    }

    const completo = `${datos.nombres ?? ""} ${datos.apellidos ?? ""}`.trim();
    return completo || "Sin nombre";
}

function badgeClasses(estado) {
    if (estado === "Aprobado") {
        return "bg-green-50 text-green-700 ring-green-100";
    }

    if (estado === "Reprobado") {
        return "bg-red-50 text-red-700 ring-red-100";
    }

    if (estado === "Retirado") {
        return "bg-yellow-50 text-yellow-700 ring-yellow-100";
    }

    return "bg-blue-50 text-blue-700 ring-blue-100";
}

function formatearFecha(fecha) {
    if (!fecha) {
        return "";
    }

    const partes = fecha.split("-");
    if (partes.length !== 3) {
        return fecha;
    }

    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}
</script>
