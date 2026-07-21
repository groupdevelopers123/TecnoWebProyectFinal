<template>
    <Head title="Editar Horario" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Editar horario
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Actualiza el horario académico.
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

        <FormHorario
            :horario="horario"
            :action="action"
            method="put"
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
const action = computed(() => page.props.action || "#");
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.horarios.index"),
);
const carreraMaterias = computed(() => page.props.carreraMaterias || []);
const periodos = computed(() => page.props.periodos || []);
const aulas = computed(() => page.props.aulas || []);
const docentes = computed(() => page.props.docentes || []);
const showUrl = computed(() =>
    horario.value?.id
        ? route("admin.horarios.show", horario.value.id)
        : route("admin.horarios.index"),
);
</script>
