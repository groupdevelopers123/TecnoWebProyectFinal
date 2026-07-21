<template>
    <Head title="Editar Materia" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Editar materia
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Modifica solo los datos necesarios.
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

        <FormMateria
            :materia="materia"
            :carreras="carreras"
            :docentes="docentes"
            :action="action"
            method="put"
            :cancelUrl="cancelUrl"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormMateria from "./FormMateria.vue";

const page = usePage();
const materia = computed(() => page.props.materia || {});
const carreras = computed(() => page.props.carreras || []);
const docentes = computed(() => page.props.docentes || []);
const action = computed(() => page.props.action || "#");
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.materias.index"),
);
const showUrl = computed(() =>
    materia.value?.id
        ? route("admin.materias.show", materia.value.id)
        : route("admin.materias.index"),
);
</script>
