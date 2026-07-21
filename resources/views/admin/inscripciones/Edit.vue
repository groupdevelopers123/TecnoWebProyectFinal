<template>
    <Head title="Editar Inscripción" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Editar inscripción
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Actualiza la información de la inscripción seleccionada.
                </p>
            </div>

            <a
                :href="showUrl"
                class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
            >
                <i class="fa-solid fa-eye text-xs"></i>
                Ver detalle
            </a>
        </div>

        <FormInscripcion
            :inscripcion="inscripcion"
            :action="action"
            method="put"
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
const action = computed(() => page.props.action || "#");
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.inscripciones.index"),
);
const alumnos = computed(() => page.props.alumnos || []);
const ofertas = computed(() => page.props.ofertas || []);
const showUrl = computed(() =>
    inscripcion.value?.id
        ? route("admin.inscripciones.show", inscripcion.value.id)
        : route("admin.inscripciones.index"),
);
</script>
