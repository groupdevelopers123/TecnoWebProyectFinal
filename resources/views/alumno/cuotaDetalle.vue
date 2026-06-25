<script setup>
import { Head, Link } from "@inertiajs/vue3";
import HeaderAlumno from "../partials/headerAlumno.vue";
import PageVisitCounter from "../partials/PageVisitCounter.vue";

const props = defineProps({
    cuota: {
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
    <div class="min-h-screen bg-slate-100">
        <Head title="Detalle de cuota" />
        <HeaderAlumno />

        <main class="px-5 pb-10 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <section
                    class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div
                        class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <div>
                            <h1
                                class="text-3xl font-black tracking-tight text-slate-900"
                            >
                                Cuota #{{ cuota.numero_cuota }}
                            </h1>
                            <p class="mt-2 text-sm text-slate-500">
                                Detalle de la cuota asociada al crédito #{{
                                    cuota.credito?.id
                                }}.
                            </p>
                        </div>

                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:items-center"
                        >
                            <Link
                                href="/alumno/mis-creditos"
                                class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-black text-white transition hover:-translate-y-0.5 hover:bg-slate-800"
                            >
                                Volver a Mis Créditos
                            </Link>
                        </div>
                    </div>
                </section>

                <section
                    class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div>
                            <p
                                class="text-xs font-bold uppercase text-slate-400"
                            >
                                Monto de la cuota
                            </p>
                            <p class="mt-2 text-lg font-black text-blue-700">
                                {{ formatearMonto(cuota.monto) }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-xs font-bold uppercase text-slate-400"
                            >
                                Estado
                            </p>
                            <p class="mt-2 font-black text-slate-900">
                                {{ cuota.estado_cuota || "Pendiente" }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-xs font-bold uppercase text-slate-400"
                            >
                                Vencimiento
                            </p>
                            <p class="mt-2 text-slate-900">
                                {{ formatearFecha(cuota.fecha_vencimiento) }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-xs font-bold uppercase text-slate-400"
                            >
                                Pago registrado
                            </p>
                            <p class="mt-2 text-slate-900">
                                {{ formatearFecha(cuota.fecha_pago) }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5"
                    >
                        <p class="text-sm font-black text-slate-900">Crédito</p>
                        <p class="mt-2 text-sm text-slate-600">
                            {{
                                cuota.credito?.concepto_pago?.nombre ??
                                "Sin concepto"
                            }}
                            • Saldo pendiente:
                            {{ formatearMonto(cuota.credito?.saldo_pendiente) }}
                        </p>
                    </div>
                </section>
            </div>
        </main>

        <footer
            class="fixed left-4 bottom-4 z-50 bg-transparent p-0 sm:left-6 sm:bottom-6"
        >
            <PageVisitCounter compact />
        </footer>
    </div>
</template>
