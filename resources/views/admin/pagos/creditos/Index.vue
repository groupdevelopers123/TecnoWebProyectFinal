<template>
    <Head title="Créditos" />

    <PagosNav />

    <div
        v-if="flashSuccess"
        class="mb-6 rounded-2xl border border-green-100 bg-green-50 p-4 text-sm font-bold text-green-700"
    >
        {{ flashSuccess }}
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Listado de créditos
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Registra, busca, edita o cambia el estado de los créditos.
                </p>
            </div>

            <Link
                :href="route('admin.creditos.create')"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                Nuevo crédito
            </Link>
        </div>

        <form @submit.prevent="buscar" class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Buscar</label
                >
                <input
                    v-model="buscarTexto"
                    type="text"
                    placeholder="Alumno, CI, concepto, carrera o estado"
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
                    @click="limpiar"
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
                            Concepto
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Monto
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Saldo
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Cuotas
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
                        v-for="credito in creditosData"
                        :key="credito.id"
                        class="transition hover:bg-slate-50"
                    >
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ nombreAlumno(credito) }}
                            </p>
                            <p class="text-xs text-slate-500">
                                CI:
                                {{
                                    credito.inscripcion?.alumnoDetalle?.user
                                        ?.ci ?? "No registrado"
                                }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-800">
                                {{
                                    credito.conceptoPago?.nombre ??
                                    "Sin concepto"
                                }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{
                                    credito.inscripcion?.ofertaAcademica
                                        ?.carrera?.nombre ?? "Sin carrera"
                                }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100"
                                >Bs
                                {{ formatoMonto(credito.monto_total) }}</span
                            >
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-100"
                                >Bs
                                {{
                                    formatoMonto(credito.saldo_pendiente)
                                }}</span
                            >
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-slate-600">
                            {{ credito.cantidad_cuotas }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                                :class="estadoClasses(credito.estado)"
                            >
                                {{ capitalizar(credito.estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <button
                                    type="button"
                                    @click="abrirModalCuotas(credito.id)"
                                    class="inline-flex items-center justify-center rounded-full bg-slate-100 p-2 text-slate-700 transition hover:bg-slate-200"
                                    :aria-label="`Ver cuotas ${credito.id}`"
                                >
                                    <i class="fa-solid fa-list-check"></i>
                                </button>
                                <Link
                                    :href="
                                        route('admin.creditos.show', credito.id)
                                    "
                                    class="inline-flex items-center justify-center rounded-full bg-slate-100 p-2 text-slate-700 transition hover:bg-slate-200"
                                    :aria-label="`Ver crédito ${credito.id}`"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                </Link>
                                <Link
                                    :href="
                                        route('admin.creditos.edit', credito.id)
                                    "
                                    class="inline-flex items-center justify-center rounded-full bg-blue-600 p-2 text-white transition hover:bg-blue-700"
                                    :aria-label="`Editar crédito ${credito.id}`"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </Link>
                                <button
                                    type="button"
                                    @click="cambiarEstado(credito.id)"
                                    class="inline-flex items-center justify-center rounded-full bg-rose-50 p-2 text-rose-700 transition hover:bg-rose-100"
                                    :aria-label="`Cambiar estado ${credito.id}`"
                                >
                                    <i class="fa-solid fa-rotate"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="creditosData.length === 0">
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

    <div
        v-if="modalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm"
    >
        <div
            class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl"
        >
            <div
                class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-5"
            >
                <div>
                    <h2 class="text-xl font-black text-slate-900">
                        Cuotas del crédito
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Detalle de cuotas generadas automáticamente.
                    </p>
                </div>
                <button
                    type="button"
                    @click="cerrarModalCuotas"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-red-50 hover:text-red-600"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6">
                <div
                    v-if="modalLoading"
                    class="py-12 text-center text-sm text-slate-500"
                >
                    Cargando cuotas...
                </div>
                <ModalCuotas
                    v-else-if="modalData"
                    :credito="modalData.credito"
                    :cuotas="modalData.cuotas"
                />
                <div
                    v-else
                    class="rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700"
                >
                    Error al cargar las cuotas del crédito.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import ModalCuotas from "../pago-cuotas/ModalCuotas.vue";
import PagosNav from "../partials/PagosNav.vue";

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success || "");
const creditosPage = computed(
    () =>
        page.props.creditos ?? {
            data: [],
            pagination: { current_page: 1, last_page: 1, total: 0 },
        },
);
const creditosData = computed(() => creditosPage.value.data ?? []);
const pagination = computed(
    () =>
        creditosPage.value.pagination ?? {
            current_page: 1,
            last_page: 1,
            total: 0,
        },
);
const buscarTexto = ref(page.props.request?.buscar ?? "");
const modalOpen = ref(false);
const modalLoading = ref(false);
const modalData = ref(null);

function buscar() {
    router.get(
        route("admin.creditos.index"),
        { buscar: buscarTexto.value || undefined },
        { preserveState: true, replace: true, preserveScroll: true },
    );
}

function limpiar() {
    buscarTexto.value = "";
    buscar();
}

function cambiarPagina(pagina) {
    if (!pagina || pagina < 1) return;
    router.get(
        route("admin.creditos.index"),
        { buscar: buscarTexto.value || undefined, page: pagina },
        { preserveState: true, replace: true, preserveScroll: true },
    );
}

function abrirModalCuotas(id) {
    modalOpen.value = true;
    modalLoading.value = true;
    modalData.value = null;

    fetch(route("admin.creditos.cuotas.index", id), {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((payload) => {
            modalData.value = payload;
        })
        .catch(() => {
            modalData.value = null;
        })
        .finally(() => {
            modalLoading.value = false;
        });
}

function cerrarModalCuotas() {
    modalOpen.value = false;
    modalLoading.value = false;
    modalData.value = null;
}

function cambiarEstado(id) {
    if (!window.confirm("¿Deseas cambiar el estado de este crédito?")) return;
    router.patch(
        route("admin.creditos.destroy", id),
        {},
        { preserveState: true, preserveScroll: true },
    );
}

function nombreAlumno(credito) {
    const user = credito.inscripcion?.alumnoDetalle?.user;
    return user
        ? `${user.nombres ?? ""} ${user.apellidos ?? ""}`.trim() ||
              "Alumno sin usuario"
        : "Alumno sin usuario";
}

function formatoMonto(valor) {
    return Number(valor ?? 0).toFixed(2);
}

function capitalizar(valor) {
    return (
        String(valor ?? "")
            .charAt(0)
            .toUpperCase() + String(valor ?? "").slice(1)
    );
}

function estadoClasses(estado) {
    if (estado === "pagado") return "bg-green-50 text-green-700 ring-green-100";
    if (estado === "anulado") return "bg-red-50 text-red-700 ring-red-100";
    if (estado === "activo") return "bg-blue-50 text-blue-700 ring-blue-100";
    return "bg-yellow-50 text-yellow-700 ring-yellow-100";
}
</script>
