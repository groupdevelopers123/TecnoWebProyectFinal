<template>
    <Head title="Editar Carrera" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Editar carrera
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Modifica sólo los datos necesarios.
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

        <FormCarrera
            :carrera="carrera"
            :action="action"
            method="put"
            :cancelUrl="cancelUrl"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormCarrera from "./FormCarrera.vue";

const page = usePage();
const carrera = computed(() => page.props.carrera || {});
const action = computed(() => page.props.action || "#");
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.carreras.index"),
);
const showUrl = computed(() =>
    carrera.value?.id
        ? route("admin.carreras.show", carrera.value.id)
        : route("admin.carreras.index"),
);
</script>
