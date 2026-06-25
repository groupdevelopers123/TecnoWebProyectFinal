<script setup>
import { computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import HeaderAlumno from "../partials/headerAlumno.vue";
import PageVisitCounter from "../partials/PageVisitCounter.vue";

const props = defineProps({
    credito: {
        type: Object,
        default: () => ({}),
    },
});

const formatearMonto = (valor) => {
    const numero = Number(valor ?? 0);

    return `Bs ${numero.toLocaleString("es-BO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};

const formatearFecha = (fecha) => {
    if (!fecha) {
        return "Sin fecha";
    }

    const fechaObjeto = new Date(fecha);

    if (Number.isNaN(fechaObjeto.getTime())) {
        return "Sin fecha";
    }

    return new Intl.DateTimeFormat("es-BO", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    }).format(fechaObjeto);
};
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50"
    >
        <Head title="Detalle del crédito" />
        <HeaderAlumno />

        <main class="px-5 pb-20 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-5xl">
                <!-- Encabezado -->
                <div class="mb-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <Link
                                href="/alumno/mis-creditos"
                                class="mb-4 inline-flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-200"
                            >
                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M19 12H5M12 19l-7-7 7-7" />
                                </svg>
                                Volver
                            </Link>

                            <h1 class="text-4xl font-black text-slate-900">
                                Crédito #{{ credito.id }}
                            </h1>
                            <p class="mt-2 text-lg text-slate-500">
                                Revisa el detalle de este crédito y sus cuotas
                                asociadas.
                            </p>
                        </div>
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-100 to-amber-50"
                        >
                            <svg
                                class="h-8 w-8 text-amber-600"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M12 2v20m10-10H2" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Detalles -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white shadow-sm"
                >
                    <div class="p-6">
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-bold uppercase text-slate-400"
                                >
                                    Concepto
                                </p>
                                <p class="mt-2 font-bold text-slate-900">
                                    {{
                                        credito.concepto_pago?.nombre ??
                                        "Sin concepto"
                                    }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-bold uppercase text-slate-400"
                                >
                                    Estado
                                </p>
                                <p class="mt-2 font-bold text-slate-900">
                                    {{ credito.estado ?? "Desconocido" }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-bold uppercase text-slate-400"
                                >
                                    Monto total
                                </p>
                                <p class="mt-2 font-bold text-blue-600">
                                    {{ formatearMonto(credito.monto_total) }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-bold uppercase text-slate-400"
                                >
                                    Saldo pendiente
                                </p>
                                <p class="mt-2 font-bold text-amber-600">
                                    {{
                                        formatearMonto(credito.saldo_pendiente)
                                    }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-bold uppercase text-slate-400"
                                >
                                    Total de cuotas
                                </p>
                                <p class="mt-2 font-bold text-slate-900">
                                    {{ credito.cantidad_cuotas }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-bold uppercase text-slate-400"
                                >
                                    Vencimiento
                                </p>
                                <p class="mt-2 font-bold text-slate-900">
                                    {{
                                        formatearFecha(
                                            credito.fecha_vencimiento,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4"
                        >
                            <p class="font-bold text-slate-900">
                                Información de cuotas
                            </p>
                            <p class="mt-2 text-sm text-slate-600">
                                Abre la vista de cuotas desde la lista de
                                créditos para pagar o revisar el estado de cada
                                cuota.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer
            class="fixed left-4 bottom-4 z-50 bg-transparent p-0 sm:left-6 sm:bottom-6"
        >
            <PageVisitCounter compact />
        </footer>
    </div>
</template>
