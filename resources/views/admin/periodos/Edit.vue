<template>
    <Head title="Editar Periodo Académico" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Editar periodo académico
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Actualiza la información del periodo.
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

        <FormPeriodoAcademico
            :periodo="periodo"
            :action="action"
            method="put"
            :cancelUrl="cancelUrl"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormPeriodoAcademico from "./FormPeriodoAcademico.vue";

const page = usePage();
const periodo = computed(() => page.props.periodo || {});
const action = computed(() => page.props.action || "#");
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.periodos-academicos.index"),
);
const showUrl = computed(() =>
    periodo.value?.id
        ? route("admin.periodos-academicos.show", periodo.value.id)
        : route("admin.periodos-academicos.index"),
);
</script>
