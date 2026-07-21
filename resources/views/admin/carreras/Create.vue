<template>
    <Head title="Registrar Carrera" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">Registrar carrera</h2>
            <p class="mt-1 text-sm text-slate-500">
                Completa los datos generales de la carrera académica.
            </p>
        </div>

        <FormCarrera :action="action" method="post" :cancelUrl="cancelUrl" />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormCarrera from "./FormCarrera.vue";

const page = usePage();
const action = computed(
    () => page.props.action || route("admin.carreras.store"),
);
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.carreras.index"),
);
</script>
