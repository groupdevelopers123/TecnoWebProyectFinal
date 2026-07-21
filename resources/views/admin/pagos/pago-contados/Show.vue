<template>
    <Head :title="`Detalle de pago #${pago.id}`" />

    <div class="mx-auto max-w-6xl space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-3xl bg-emerald-100 text-2xl text-emerald-700"
                    >
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>

                    <div>
                        <h2 class="text-2xl font-black text-slate-900">
                            Pago #{{ pago.id }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ pago.concepto_pago?.nombre ?? "Sin concepto" }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button
                        v-if="
                            pago.metodo_pago === 'QR' &&
                            pago.estado === 'Pendiente'
                        "
                        type="button"
                        @click="consultar"
                        class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:-translate-y-0.5 hover:bg-emerald-700"
                    >
                        <i class="fa-solid fa-rotate text-xs"></i>
                        Consultar PagoFácil
                    </button>

                    <Link
                        :href="route('admin.pago-contados.edit', pago.id)"
                        class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                    >
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                        Editar
                    </Link>

                    <Link
                        :href="route('admin.pago-contados.index')"
                        class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                    >
                        Volver
                    </Link>
                </div>
            </div>

            <div
                v-if="pago.metodo_pago === 'QR' && pago.qr_url"
                class="mt-6 rounded-3xl border border-emerald-100 bg-emerald-50 p-6 text-center"
            >
                <p class="mb-4 text-sm font-bold uppercase text-emerald-600">
                    QR PagoFácil
                </p>

                <img
                    :src="pago.qr_url"
                    alt="QR PagoFácil"
                    class="mx-auto h-72 w-72 rounded-3xl border border-white bg-white p-4 shadow-sm"
                />

                <p class="mt-4 text-sm font-bold text-slate-700">
                    Payment Number: {{ pago.payment_number ?? "No disponible" }}
                </p>
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Alumno
                    </p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{ nombreAlumno }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Carrera
                    </p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{
                            pago.inscripcion?.ofertaAcademica?.carrera
                                ?.nombre ?? "Sin carrera"
                        }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Concepto
                    </p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{ pago.concepto_pago?.nombre ?? "Sin concepto" }}
                    </p>
                </div>

                <div class="rounded-2xl bg-emerald-50 p-4">
                    <p class="text-xs font-bold uppercase text-emerald-500">
                        Monto pagado
                    </p>
                    <p class="mt-1 font-bold text-emerald-700">
                        Bs {{ formatoMonto(pago.monto_pagado) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Fecha de pago
                    </p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{ formatearFecha(pago.fecha_pago) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Método
                    </p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{ pago.metodo_pago }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Estado
                    </p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{ pago.estado }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Código transacción
                    </p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{ pago.codigo_transaccion || "No registrado" }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Observación
                    </p>
                    <p
                        class="mt-1 whitespace-pre-line text-sm font-medium text-slate-700"
                    >
                        {{ pago.observacion || "Sin observación registrada." }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    pago: { type: Object, default: () => ({}) },
});

const nombreAlumno = computed(() => {
    const user = props.pago?.inscripcion?.alumnoDetalle?.user;
    const completo = `${user?.nombres ?? ""} ${user?.apellidos ?? ""}`.trim();
    return completo || "Alumno sin usuario";
});

function consultar() {
    router.post(
        route("admin.pago-contados.consultar", props.pago.id),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
}

function formatoMonto(valor) {
    return Number(valor ?? 0).toFixed(2);
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
