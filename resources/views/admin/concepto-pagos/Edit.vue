<template>
    <Head title="Editar Concepto de Pago" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">
                Editar concepto de pago
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Actualiza la información del concepto seleccionado.
            </p>
        </div>

        <FormConceptoPago
            :concepto="concepto"
            :submit-url="route('admin.concepto-pagos.update', concepto.id)"
            :cancel-url="route('admin.concepto-pagos.index')"
            submit-label="Guardar cambios"
            submit-method="put"
        />
    </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import FormConceptoPago from "./FormConceptoPago.vue";

const props = defineProps({
    concepto: { type: Object, default: () => ({}) },
});
</script>
