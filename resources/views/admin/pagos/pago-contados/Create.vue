<template>
    <Head title="Registrar pago al contado" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">
                Nuevo pago al contado
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Registra un pago al contado. Si seleccionas QR, podrás generar
                el QR con PagoFácil.
            </p>
        </div>

        <FormPagoContado
            :pago="pago"
            :inscripciones="inscripciones"
            :conceptos="conceptos"
            :submit-url="action"
            :cancel-url="cancelUrl"
            :method="method"
        />
    </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import FormPagoContado from "./FormPagoContado.vue";

const props = defineProps({
    pago: { type: Object, default: () => ({}) },
    inscripciones: { type: Array, default: () => [] },
    conceptos: { type: Array, default: () => [] },
    action: { type: String, default: "" },
    method: { type: String, default: "post" },
    cancelUrl: { type: String, default: "" },
});
</script>
