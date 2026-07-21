<template>
    <Head title="Registrar Aula" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">Nueva aula</h2>
            <p class="mt-1 text-sm text-slate-500">
                Completa los datos del aula, su capacidad y dimensiones.
            </p>
        </div>

        <FormAula :action="action" method="post" :cancelUrl="cancelUrl" />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import FormAula from "./FormAula.vue";
import { computed } from "vue";

const page = usePage();

const action = computed(() => page.props.action || route("admin.aulas.store"));
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.aulas.index"),
);
</script>
