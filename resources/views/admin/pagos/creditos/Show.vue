<template>
    <Head :title="`Detalle de crédito #${credito.id}`" />

    <div class="mx-auto max-w-6xl space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">
                        Detalle del crédito
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Información general del crédito y su estado actual.
                    </p>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('admin.creditos.index')"
                        class="inline-flex items-center gap-2 rounded-2xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        <i class="fa-solid fa-arrow-left"></i>
                        Volver
                    </Link>
                    <Link
                        :href="route('admin.creditos.edit', credito.id)"
                        class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700"
                    >
                        <i class="fa-solid fa-pen-to-square"></i>
                        Editar
                    </Link>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div
                    class="rounded-2xl border border-slate-200 bg-slate-50 p-5"
                >
                    <h3
                        class="text-sm font-black uppercase tracking-[0.2em] text-slate-500"
                    >
                        Alumno
                    </h3>
                    <p class="mt-3 text-lg font-black text-slate-900">
                        {{ nombreAlumno }}
                    </p>
                    <p class="mt-1 text-sm text-slate-500">
                        CI:
                        {{
                            credito.inscripcion?.alumnoDetalle?.user?.ci ??
                            "No registrado"
                        }}
                    </p>
                </div>
                <div
                    class="rounded-2xl border border-slate-200 bg-slate-50 p-5"
                >
                    <h3
                        class="text-sm font-black uppercase tracking-[0.2em] text-slate-500"
                    >
                        Concepto
                    </h3>
                    <p class="mt-3 text-lg font-black text-slate-900">
                        {{ credito.conceptoPago?.nombre ?? "Sin concepto" }}
                    </p>
                    <p class="mt-1 text-sm text-slate-500">
                        {{
                            credito.inscripcion?.ofertaAcademica?.carrera
                                ?.nombre ?? "Sin carrera"
                        }}
                    </p>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 p-5">
                    <p
                        class="text-sm font-black uppercase tracking-[0.2em] text-slate-500"
                    >
                        Monto total
                    </p>
                    <p class="mt-3 text-2xl font-black text-slate-900">
                        Bs {{ formatoMonto(credito.monto_total) }}
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-5">
                    <p
                        class="text-sm font-black uppercase tracking-[0.2em] text-slate-500"
                    >
                        Saldo pendiente
                    </p>
                    <p class="mt-3 text-2xl font-black text-slate-900">
                        Bs {{ formatoMonto(credito.saldo_pendiente) }}
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-5">
                    <p
                        class="text-sm font-black uppercase tracking-[0.2em] text-slate-500"
                    >
                        Estado
                    </p>
                    <div class="mt-3">
                        <span
                            class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                            :class="estadoClasses(credito.estado)"
                        >
                            {{ capitalizar(credito.estado) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">
                            Cuotas
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Cantidad de cuotas: {{ credito.cantidad_cuotas }}
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="abrirModalCuotas"
                        class="rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800"
                    >
                        Ver cuotas
                    </button>
                </div>

                <div
                    v-if="credito.pagoCuotas?.length"
                    class="mt-4 overflow-hidden rounded-2xl border border-slate-200"
                >
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                >
                                    N°
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                >
                                    Monto
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                >
                                    Fecha de vencimiento
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                >
                                    Estado
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr
                                v-for="cuota in credito.pagoCuotas"
                                :key="cuota.id"
                            >
                                <td
                                    class="px-4 py-3 text-sm font-bold text-slate-700"
                                >
                                    {{ cuota.numero_cuota }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">
                                    Bs {{ formatoMonto(cuota.monto) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">
                                    {{ cuota.fecha_vencimiento ?? "-" }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">
                                    {{ cuota.estado ?? "-" }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="mt-4 text-sm text-slate-500">
                    No hay cuotas registradas para este crédito.
                </p>
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
    </div>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import ModalCuotas from "../pago-cuotas/ModalCuotas.vue";

const props = defineProps({ credito: Object });
const modalOpen = ref(false);
const modalLoading = ref(false);
const modalData = ref(null);

const nombreAlumno = computed(() => {
    const user = props.credito?.inscripcion?.alumnoDetalle?.user;
    return user
        ? `${user.nombres ?? ""} ${user.apellidos ?? ""}`.trim() ||
              "Alumno sin usuario"
        : "Alumno sin usuario";
});

function abrirModalCuotas() {
    modalOpen.value = true;
    modalLoading.value = true;
    modalData.value = null;

    fetch(route("admin.creditos.cuotas.index", props.credito.id), {
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
