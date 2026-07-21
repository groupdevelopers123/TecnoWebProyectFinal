<template>
    <Head title="Nuevo crédito" />

    <div
        class="mx-auto max-w-5xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
    >
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900">
                    Registrar crédito
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Complete los datos del crédito y se generarán las cuotas
                    automáticamente.
                </p>
            </div>
            <Link
                :href="route('admin.creditos.index')"
                class="inline-flex items-center gap-2 rounded-2xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Volver
            </Link>
        </div>

        <FormCredito
            :credito="credito"
            :inscripciones="inscripciones"
            :conceptos="conceptos"
            :submit-url="route('admin.creditos.store')"
            :cancel-url="route('admin.creditos.index')"
            submit-label="Guardar crédito"
        />
    </div>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import FormCredito from "./FormCredito.vue";

const props = defineProps({
    credito: Object,
    inscripciones: Array,
    conceptos: Array,
});
</script>
