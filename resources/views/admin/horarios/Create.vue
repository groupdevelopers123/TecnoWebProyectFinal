<template>
    <Head title="Registrar Horario" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">Registrar horario</h2>
            <p class="mt-1 text-sm text-slate-500">
                Crea un nuevo horario académico.
            </p>
        </div>

        <FormHorario
            :horario="horario"
            :action="action"
            method="post"
            :cancelUrl="cancelUrl"
            :carreraMaterias="carreraMaterias"
            :periodos="periodos"
            :aulas="aulas"
            :docentes="docentes"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormHorario from "./FormHorario.vue";

const page = usePage();
const horario = computed(() => page.props.horario || {});
const action = computed(
    () => page.props.action || route("admin.horarios.store"),
);
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.horarios.index"),
);
const carreraMaterias = computed(() => page.props.carreraMaterias || []);
const periodos = computed(() => page.props.periodos || []);
const aulas = computed(() => page.props.aulas || []);
const docentes = computed(() => page.props.docentes || []);
</script>
