<template>
    <Head title="Nueva Asignación" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">Nueva asignación</h2>
            <p class="mt-1 text-sm text-slate-500">
                Asignar materia a una carrera.
            </p>
        </div>

        <FormCarreraMateria
            :carreras="carreras"
            :materias="materias"
            :action="action"
            method="post"
            :cancelUrl="cancelUrl"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormCarreraMateria from "./FormCarreraMateria.vue";

const page = usePage();
const carreras = computed(() => page.props.carreras || []);
const materias = computed(() => page.props.materias || []);
const action = computed(
    () => page.props.action || route("admin.carrera-materias.store"),
);
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.carrera-materias.index"),
);
</script>
