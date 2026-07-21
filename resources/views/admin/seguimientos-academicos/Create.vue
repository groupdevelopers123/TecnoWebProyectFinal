<template>
    <Head title="Registrar Seguimiento Académico" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">
                Nuevo seguimiento académico
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Completa los datos para registrar el seguimiento académico del
                alumno.
            </p>
        </div>

        <FormSeguimientoAcademico
            :seguimiento="seguimiento"
            :inscripcionMaterias="inscripcionMaterias"
            :docentes="docentes"
            :action="action"
            :method="method"
            :cancelUrl="cancelUrl"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormSeguimientoAcademico from "./FormSeguimientoAcademico.vue";

const page = usePage();
const seguimiento = computed(() => page.props.seguimiento ?? {});
const inscripcionMaterias = computed(
    () => page.props.inscripcionMaterias ?? [],
);
const docentes = computed(() => page.props.docentes ?? []);
const action = computed(
    () => page.props.action || route("admin.seguimientos-academicos.store"),
);
const method = computed(() => page.props.method || "post");
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.seguimientos-academicos.index"),
);
</script>
