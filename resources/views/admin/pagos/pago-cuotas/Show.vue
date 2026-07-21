<template>
    <Head :title="'Detalle cuota #' + (cuota?.id ?? '')" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"
        >
            <div>
                <p
                    class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500"
                >
                    Detalle de cuota
                </p>
                <h2 class="mt-2 text-2xl font-black text-slate-900">
                    Cuota #{{ cuota?.id }}
                </h2>
            </div>

            <div class="flex flex-wrap gap-3">
                <Link
                    :href="route('admin.pago-cuotas.index')"
                    class="rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    Volver al listado
                </Link>

                <Link
                    v-if="cuota?.estado_cuota !== 'pagado'"
                    :href="route('admin.pago-cuotas.edit', cuota.id)"
                    class="rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700"
                >
                    Registrar pago
                </Link>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <p
                    class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500"
                >
                    Alumno
                </p>
                <h3 class="mt-3 text-lg font-black text-slate-900">
                    {{ nombreCompleto(cuota?.alumno) }}
                </h3>
                <p class="mt-2 text-sm text-slate-600">
                    CI: {{ cuota?.alumno?.ci ?? "No registrado" }}
                </p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <p
                    class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500"
                >
                    Concepto
                </p>
                <h3 class="mt-3 text-lg font-black text-slate-900">
                    {{ cuota?.concepto?.nombre ?? "Sin concepto" }}
                </h3>
                <p class="mt-2 text-sm text-slate-600">
                    Cuota #{{ cuota?.numero_cuota }}
                </p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <p
                    class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500"
                >
                    Estado
                </p>
                <span
                    class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                    :class="estadoClasses(cuota?.estado_cuota)"
                >
                    {{ capitalizar(cuota?.estado_cuota) }}
                </span>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 p-5">
                <h4 class="text-lg font-black text-slate-900">
                    Información de pago
                </h4>
                <dl class="mt-4 space-y-3 text-sm text-slate-600">
                    <div
                        class="flex justify-between gap-4 border-b border-slate-100 pb-3"
                    >
                        <dt class="font-bold text-slate-700">Monto</dt>
                        <dd class="font-semibold text-slate-900">
                            Bs {{ formatoMonto(cuota?.monto) }}
                        </dd>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-b border-slate-100 pb-3"
                    >
                        <dt class="font-bold text-slate-700">
                            Fecha de vencimiento
                        </dt>
                        <dd>{{ cuota?.fecha_vencimiento || "-" }}</dd>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-b border-slate-100 pb-3"
                    >
                        <dt class="font-bold text-slate-700">Método de pago</dt>
                        <dd>{{ cuota?.metodo_pago || "-" }}</dd>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-b border-slate-100 pb-3"
                    >
                        <dt class="font-bold text-slate-700">Fecha de pago</dt>
                        <dd>{{ cuota?.fecha_pago || "-" }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-bold text-slate-700">
                            Código de transacción
                        </dt>
                        <dd>{{ cuota?.codigo_transaccion || "-" }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-3xl border border-slate-200 p-5">
                <h4 class="text-lg font-black text-slate-900">Observaciones</h4>
                <p class="mt-4 whitespace-pre-line text-sm text-slate-600">
                    {{ cuota?.observacion || "Sin observaciones registradas." }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    cuota: { type: Object, default: () => ({}) },
});

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
