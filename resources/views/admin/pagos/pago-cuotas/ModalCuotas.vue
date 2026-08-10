<template>
    <div>
        <div class="mb-6 rounded-3xl bg-slate-50 p-5">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Alumno
                    </p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{
                            nombreCompleto(
                                credito?.inscripcion?.alumnoDetalle?.user,
                            )
                        }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Concepto
                    </p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{ credito?.conceptoPago?.nombre ?? "Sin concepto" }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Monto total
                    </p>
                    <p class="mt-1 font-bold text-blue-700">
                        Bs {{ formatoMonto(credito?.monto_total) }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Saldo pendiente
                    </p>
                    <p class="mt-1 font-bold text-amber-700">
                        Bs {{ formatoMonto(credito?.saldo_pendiente) }}
                    </p>
                </div>
            </div>
        </div>

        <div
            class="overflow-hidden rounded-3xl border border-slate-200 bg-white"
        >
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                            >
                                Nro.
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                            >
                                Monto
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                            >
                                Vencimiento
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                            >
                                Pago
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                            >
                                Estado
                            </th>
                            <th
                                class="px-5 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-500"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr
                            v-for="cuota in cuotas"
                            :key="cuota.id"
                            class="transition hover:bg-slate-50"
                        >
                            <td
                                class="px-5 py-4 text-sm font-black text-slate-800"
                            >
                                #{{ cuota.numero_cuota }}
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100"
                                >
                                    Bs {{ formatoMonto(cuota.monto) }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ cuota.fecha_vencimiento || "-" }}
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ cuota.fecha_pago || "-" }}
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1"
                                    :class="estadoClasses(cuota.estado_cuota)"
                                >
                                    {{ capitalizar(cuota.estado_cuota) }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
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

                        <tr v-if="!cuotas?.length">
                            <td
                                colspan="6"
                                class="px-6 py-12 text-center text-sm text-slate-500"
                            >
                                Este crédito todavía no tiene cuotas generadas.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    credito: { type: Object, default: () => ({}) },
    cuotas: { type: Array, default: () => [] },
});

function nombreCompleto(usuario) {
    const completo =
        `${usuario?.nombres ?? ""} ${usuario?.apellidos ?? ""}`.trim();
    return completo || "Alumno sin usuario";
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
    if (estado === "pagado") {
        return "bg-green-50 text-green-700 ring-green-100";
    }

    if (estado === "anulado" || estado === "fallido") {
        return "bg-red-50 text-red-700 ring-red-100";
    }

    return "bg-yellow-50 text-yellow-700 ring-yellow-100";
}
</script>
