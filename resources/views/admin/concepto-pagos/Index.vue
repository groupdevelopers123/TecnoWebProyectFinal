<template>
    <Head title="Conceptos de pago" />

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
                    Listado de conceptos de pago
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Registra, busca, edita o cambia el estado de los conceptos
                    de pago.
                </p>
            </div>

            <Link
                :href="route('admin.concepto-pagos.create')"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                Nuevo concepto
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
                    placeholder="Nombre, descripción o estado"
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
                            Concepto
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                        >
                            Descripción
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
                        v-for="concepto in conceptosData"
                        :key="concepto.id"
                        class="transition hover:bg-slate-50"
                    >
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-700"
                                >
                                    <i class="fa-solid fa-tags"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ concepto.nombre }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Concepto de pago
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ concepto.descripcion || "Sin descripción" }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                                :class="
                                    concepto.estado === 'Activo'
                                        ? 'bg-green-50 text-green-700 ring-green-100'
                                        : 'bg-red-50 text-red-700 ring-red-100'
                                "
                            >
                                {{ concepto.estado }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <Link
                                    :href="
                                        route(
                                            'admin.concepto-pagos.show',
                                            concepto.id,
                                        )
                                    "
                                    class="inline-flex items-center justify-center rounded-full bg-slate-100 p-2 text-slate-700 transition hover:bg-slate-200"
                                    :aria-label="`Ver concepto ${concepto.id}`"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                </Link>

                                <Link
                                    :href="
                                        route(
                                            'admin.concepto-pagos.edit',
                                            concepto.id,
                                        )
                                    "
                                    class="inline-flex items-center justify-center rounded-full bg-blue-600 p-2 text-white transition hover:bg-blue-700"
                                    :aria-label="`Editar concepto ${concepto.id}`"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </Link>

                                <button
                                    type="button"
                                    @click="cambiarEstado(concepto.id)"
                                    class="inline-flex items-center justify-center rounded-full bg-rose-50 p-2 text-rose-700 transition hover:bg-rose-100"
                                    :aria-label="`Cambiar estado ${concepto.id}`"
                                >
                                    <i class="fa-solid fa-rotate"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="conceptosData.length === 0">
                        <td
                            colspan="4"
                            class="px-6 py-12 text-center text-sm text-slate-500"
                        >
                            No se encontraron conceptos de pago registrados.
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
import PagosNav from "../pagos/partials/PagosNav.vue";

const props = defineProps({
    conceptos: { type: Object, default: () => ({ data: [], pagination: {} }) },
    request: { type: Object, default: () => ({ buscar: "" }) },
});

const page = usePage();
const buscarTexto = ref(props.request?.buscar ?? "");
const conceptosData = computed(() => props.conceptos?.data ?? []);
const pagination = computed(() => props.conceptos?.pagination ?? {});
const flashSuccess = computed(() => page.props.flash?.success || "");

function buscar() {
    router.get(
        route("admin.concepto-pagos.index"),
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
        route("admin.concepto-pagos.index"),
        { buscar: buscarTexto.value || undefined, page: pagina },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function cambiarEstado(id) {
    router.delete(route("admin.concepto-pagos.destroy", id), {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>
