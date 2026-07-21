<template>
    <Head title="Registrar Periodo Académico" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">
                Registrar periodo académico
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Crea un nuevo periodo académico.
            </p>
        </div>

        <FormPeriodoAcademico
            :periodo="periodo"
            :action="action"
            method="post"
            :cancelUrl="cancelUrl"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormPeriodoAcademico from "./FormPeriodoAcademico.vue";

const page = usePage();
const periodo = computed(() => page.props.periodo || {});
const action = computed(
    () => page.props.action || route("admin.periodos-academicos.store"),
);
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.periodos-academicos.index"),
);
</script>
