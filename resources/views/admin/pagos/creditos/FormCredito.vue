<template>
    <form @submit.prevent="submit" class="mt-8 space-y-6">
        <div
            v-if="errores.length"
            class="rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700"
        >
            <p class="font-bold">Revisa los siguientes errores:</p>
            <ul class="mt-2 list-inside list-disc">
                <li
                    v-for="(error, index) in errores"
                    :key="`${error}-${index}`"
                >
                    {{ error }}
                </li>
            </ul>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Inscripción</label
                >
                <select
                    v-model="form.inscripcion_id"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option :value="null">Seleccione una inscripción</option>
                    <option
                        v-for="inscripcion in inscripciones"
                        :key="inscripcion.id"
                        :value="inscripcion.id"
                    >
                        {{ inscripcionLabel(inscripcion) }}
                    </option>
                </select>
                <p
                    v-if="form.errors.inscripcion_id"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.inscripcion_id }}
                </p>
            </div>
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Concepto de pago</label
                >
                <select
                    v-model="form.concepto_pago_id"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option :value="null">Seleccione un concepto</option>
                    <option
                        v-for="concepto in conceptos"
                        :key="concepto.id"
                        :value="concepto.id"
                    >
                        {{ concepto.nombre }}
                    </option>
                </select>
                <p
                    v-if="form.errors.concepto_pago_id"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.concepto_pago_id }}
                </p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Tipo de pago</label
                >
                <input
                    v-model="form.tipo_pago"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.tipo_pago"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.tipo_pago }}
                </p>
            </div>
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Estado</label
                >
                <select
                    v-model="form.estado"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option
                        v-for="estado in estados"
                        :key="estado"
                        :value="estado"
                    >
                        {{ capitalizar(estado) }}
                    </option>
                </select>
                <p v-if="form.errors.estado" class="mt-2 text-sm text-red-600">
                    {{ form.errors.estado }}
                </p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Monto total</label
                >
                <input
                    v-model="form.monto_total"
                    type="number"
                    step="0.01"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.monto_total"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.monto_total }}
                </p>
            </div>
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Saldo pendiente</label
                >
                <input
                    v-model="form.saldo_pendiente"
                    type="number"
                    step="0.01"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.saldo_pendiente"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.saldo_pendiente }}
                </p>
            </div>
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Cantidad de cuotas</label
                >
                <input
                    v-model="form.cantidad_cuotas"
                    type="number"
                    min="1"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.cantidad_cuotas"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.cantidad_cuotas }}
                </p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Fecha de otorgamiento</label
                >
                <input
                    v-model="form.fecha_otorgamiento"
                    type="date"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.fecha_otorgamiento"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.fecha_otorgamiento }}
                </p>
            </div>
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Fecha de vencimiento</label
                >
                <input
                    v-model="form.fecha_vencimiento"
                    type="date"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.fecha_vencimiento"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.fecha_vencimiento }}
                </p>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-200 pt-6">
            <Link
                :href="cancelUrl"
                class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                >Cancelar</Link
            >
            <button
                type="submit"
                class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700"
            >
                {{ submitLabel }}
            </button>
        </div>
    </form>
</template>

<script setup>
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { computed, watch } from "vue";

const props = defineProps({
    credito: { type: Object, default: () => ({}) },
    inscripciones: { type: Array, default: () => [] },
    conceptos: { type: Array, default: () => [] },
    submitUrl: { type: String, default: "" },
    submitMethod: { type: String, default: "post" },
    cancelUrl: { type: String, default: "" },
    submitLabel: { type: String, default: "Guardar crédito" },
});

const page = usePage();
const estados = ["pendiente", "activo", "pagado", "anulado"];

const form = useForm({
    inscripcion_id: props.credito?.inscripcion_id ?? null,
    concepto_pago_id: props.credito?.concepto_pago_id ?? null,
    tipo_pago: props.credito?.tipo_pago ?? "CREDITO",
    estado: props.credito?.estado ?? "pendiente",
    monto_total: props.credito?.monto_total ?? "",
    saldo_pendiente: props.credito?.saldo_pendiente ?? "",
    cantidad_cuotas: props.credito?.cantidad_cuotas ?? "",
    fecha_otorgamiento: props.credito?.fecha_otorgamiento ?? "",
    fecha_vencimiento: props.credito?.fecha_vencimiento ?? "",
});

const errores = computed(() =>
    Object.values(page.props.errors ?? {})
        .flat()
        .filter(Boolean),
);

watch(
    () => props.credito,
    (value) => {
        const next = value ?? {};
        form.inscripcion_id = next.inscripcion_id ?? null;
        form.concepto_pago_id = next.concepto_pago_id ?? null;
        form.tipo_pago = next.tipo_pago ?? "CREDITO";
        form.estado = next.estado ?? "pendiente";
        form.monto_total = next.monto_total ?? "";
        form.saldo_pendiente = next.saldo_pendiente ?? "";
        form.cantidad_cuotas = next.cantidad_cuotas ?? "";
        form.fecha_otorgamiento = next.fecha_otorgamiento ?? "";
        form.fecha_vencimiento = next.fecha_vencimiento ?? "";
    },
    { immediate: true, deep: true },
);

function submit() {
    if (props.submitMethod === "put") {
        form.put(props.submitUrl);
        return;
    }

    form.post(props.submitUrl);
}

function inscripcionLabel(inscripcion) {
    const alumno = inscripcion.alumnoDetalle?.user;
    const carrera = inscripcion.ofertaAcademica?.carrera?.nombre;
    const periodo = inscripcion.ofertaAcademica?.periodoAcademico?.nombre;
    const gestion =
        inscripcion.ofertaAcademica?.periodoAcademica?.gestion ?? "";
    const nombre = `${alumno?.nombres ?? ""} ${alumno?.apellidos ?? ""}`.trim();
    return nombre || `Inscripción #${inscripcion.id}`;
}

function capitalizar(valor) {
    return (
        String(valor ?? "")
            .charAt(0)
            .toUpperCase() + String(valor ?? "").slice(1)
    );
}
</script>
