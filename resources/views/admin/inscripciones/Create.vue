<template>
    <Head title="Registrar Inscripción" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">Nueva inscripción</h2>
            <p class="mt-1 text-sm text-slate-500">
                Completa los datos para registrar la inscripción del alumno.
            </p>
        </div>

        <FormInscripcion
            :inscripcion="inscripcion"
            :action="action"
            method="post"
            :cancelUrl="cancelUrl"
            :alumnos="alumnos"
            :ofertas="ofertas"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormInscripcion from "./FormInscripcion.vue";

const page = usePage();
const inscripcion = computed(() => page.props.inscripcion || {});
const action = computed(
    () => page.props.action || route("admin.inscripciones.store"),
);
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.inscripciones.index"),
);
const alumnos = computed(() => page.props.alumnos || []);
const ofertas = computed(() => page.props.ofertas || []);
</script>
