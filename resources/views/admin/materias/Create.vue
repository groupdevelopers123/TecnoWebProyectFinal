<template>
    <Head title="Registrar Materia" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">Nueva materia</h2>
            <p class="mt-1 text-sm text-slate-500">
                Completa los datos de la materia.
            </p>
            <p
                v-if="carreraSeleccionada"
                class="mt-2 text-sm font-semibold text-blue-700"
            >
                La materia será asignada automáticamente a la carrera
                {{ carreraSeleccionada.nombre }}.
            </p>
        </div>

        <FormMateria
            :materia="{}"
            :carreras="carreras"
            :docentes="docentes"
            :carreraSeleccionada="carreraSeleccionada"
            :action="action"
            method="post"
            :cancelUrl="cancelUrl"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormMateria from "./FormMateria.vue";

const page = usePage();
const carreras = computed(() => page.props.carreras || []);
const docentes = computed(() => page.props.docentes || []);
const carreraSeleccionada = computed(
    () => page.props.carreraSeleccionada || null,
);
const action = computed(
    () => page.props.action || route("admin.materias.store"),
);
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.materias.index"),
);
</script>
