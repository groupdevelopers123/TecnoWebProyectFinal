<template>
    <Head title="Editar Asignación" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Editar asignación
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Actualizar relación entre carrera y materia.
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

        <FormCarreraMateria
            :asignacion="asignacion"
            :carreras="carreras"
            :materias="materias"
            :action="action"
            method="put"
            :cancelUrl="cancelUrl"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormCarreraMateria from "./FormCarreraMateria.vue";

const page = usePage();
const asignacion = computed(() => page.props.asignacion || {});
const carreras = computed(() => page.props.carreras || []);
const materias = computed(() => page.props.materias || []);
const action = computed(() => page.props.action || "#");
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.carrera-materias.index"),
);
const showUrl = computed(() =>
    asignacion.value?.id
        ? route("admin.carrera-materias.show", asignacion.value.id)
        : route("admin.carrera-materias.index"),
);
</script>
