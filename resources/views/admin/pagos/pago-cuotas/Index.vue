<template>
    <Head title="Pagos de Cuotas" />

    <PagosNav />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Listado de cuotas
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Visualiza cuotas pendientes, pagadas o anuladas.
                </p>
            </div>
        </div>

        <form @submit.prevent="buscar" class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Buscar</label
                >
                <input
                    v-model="buscarTexto"
                    type="text"
                    placeholder="Alumno, CI, concepto, estado o método"
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
                            Cuota
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Monto
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Vencimiento
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Estado
                        </th>
                        <th
                            class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    <tr
                        v-for="cuota in cuotasData"
                        :key="cuota.id"
                        class="transition hover:bg-slate-50"
                    >
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">
                                {{ nombreCompleto(cuota.alumno) }}
                            </p>
                            <p class="text-xs text-slate-500">
                                CI: {{ cuota.alumno?.ci ?? "No registrado" }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-800">
                                Cuota #{{ cuota.numero_cuota }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ cuota.concepto?.nombre ?? "Sin concepto" }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100"
                            >
                                Bs {{ formatoMonto(cuota.monto) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ cuota.fecha_vencimiento || "-" }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                                :class="estadoClasses(cuota.estado_cuota)"
                            >
                                {{ capitalizar(cuota.estado_cuota) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <Link
                                    :href="
                                        route(
                                            'admin.pago-cuotas.show',
                                            cuota.id,
                                        )
                                    "
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200"
                                    :aria-label="`Ver cuota ${cuota.id}`"
                                >
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </Link>

                                <Link
                                    v-if="cuota.estado_cuota !== 'pagado'"
                                    :href="
                                        route(
                                            'admin.pago-cuotas.edit',
                                            cuota.id,
                                        )
                                    "
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition hover:-translate-y-0.5 hover:bg-emerald-100"
                                    :aria-label="`Pagar cuota ${cuota.id}`"
                                >
                                    <i
                                        class="fa-solid fa-money-bill-wave text-sm"
                                    ></i>
                                </Link>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="cuotasData.length === 0">
                        <td
                            colspan="6"
                            class="px-6 py-12 text-center text-sm text-slate-500"
                        >
                            No se encontraron cuotas registradas.
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
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import PagosNav from "../partials/PagosNav.vue";

const props = defineProps({
    cuotas: { type: Object, default: () => ({ data: [], pagination: {} }) },
    request: { type: Object, default: () => ({ buscar: "" }) },
});

const buscarTexto = ref(props.request?.buscar ?? "");
const cuotasData = computed(() => props.cuotas?.data ?? []);
const pagination = computed(() => props.cuotas?.pagination ?? {});

function buscar() {
    router.get(
        route("admin.pago-cuotas.index"),
        { buscar: buscarTexto.value || undefined, page: 1 },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function limpiar() {
    buscarTexto.value = "";
    buscar();
}

function cambiarPagina(pagina) {
    router.get(
        route("admin.pago-cuotas.index"),
        { buscar: buscarTexto.value || undefined, page: pagina },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function nombreCompleto(alumno) {
    const completo =
        `${alumno?.nombres ?? ""} ${alumno?.apellidos ?? ""}`.trim();
    return completo || "Sin nombre";
}

function capitalizar(valor) {
    return (
        String(valor ?? "")
            .charAt(0)
            .toUpperCase() + String(valor ?? "").slice(1)
    );
}

function formatoMonto(valor) {
    return Number(valor ?? 0).toFixed(2);
}

function estadoClasses(estado) {
    if (estado === "pagado") {
        return "bg-green-50 text-green-700 ring-green-100";
    }

    if (estado === "anulado" || estado === "fallido") {
        return "bg-red-50 text-red-700 ring-red-100";
    }

    return "bg-yellow-50 text-yellow-700 ring-yellow-100";
}
</script>
