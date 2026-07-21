<template>
    <Head title="Detalle de Asignación" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-2xl text-blue-700"
                >
                    <i class="fa-solid fa-link"></i>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900">
                        {{ asignacion.carrera?.nombre }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ asignacion.materia?.nombre }}
                    </p>
                </div>
            </div>

            <div class="flex gap-3">
                <a
                    :href="editUrl"
                    class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                >
                    Editar
                </a>

                <a
                    :href="indexUrl"
                    class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    Volver
                </a>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Carrera
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ asignacion.carrera?.nombre }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Materia
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ asignacion.materia?.nombre }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Período
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ asignacion.periodo_numero ?? "No definido" }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Estado</p>
                <p
                    class="mt-1 font-bold"
                    :class="
                        asignacion.estado ? 'text-green-700' : 'text-red-700'
                    "
                >
                    {{ asignacion.estado ? "Activa" : "Inactiva" }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const asignacion = computed(() => page.props.asignacion || {});
const editUrl = computed(() =>
    asignacion.value?.id
        ? route("admin.carrera-materias.edit", asignacion.value.id)
        : route("admin.carrera-materias.index"),
);
const indexUrl = computed(() => route("admin.carrera-materias.index"));
</script>
