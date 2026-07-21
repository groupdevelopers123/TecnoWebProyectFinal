<template>
    <Head title="Editar Aula" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">Editar aula</h2>
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

        <FormAula
            :aula="aula"
            :action="action"
            method="put"
            :cancelUrl="cancelUrl"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import FormAula from "./FormAula.vue";
import { computed } from "vue";

const page = usePage();

const aula = computed(() => page.props.aula || {});
const action = computed(() => page.props.action || "#");
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.aulas.index"),
);
const showUrl = computed(() =>
    aula.value && aula.value.id
        ? route("admin.aulas.show", aula.value.id)
        : route("admin.aulas.index"),
);
</script>
