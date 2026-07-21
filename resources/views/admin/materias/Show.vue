<template>
    <Head title="Detalle de Materia" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-3xl bg-emerald-100 text-2xl text-emerald-700"
                >
                    <i class="fa-solid fa-book-open"></i>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900">
                        {{ materia.nombre }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Código: {{ materia.codigo }}
                    </p>
                </div>
            </div>

            <div class="flex gap-3">
                <a
                    :href="editUrl"
                    class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                >
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
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
                <p class="text-xs font-bold uppercase text-slate-400">Código</p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ materia.codigo }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Nombre</p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ materia.nombre }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Carga horaria
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{
                        materia.carga_horaria
                            ? materia.carga_horaria + " horas"
                            : "No registrada"
                    }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Docente
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    <span v-if="materia.docente_detalle">
                        {{ materia.docente_detalle.codigo }} -
                        {{ materia.docente_detalle.user.nombres }}
                        {{ materia.docente_detalle.user.apellidos }}
                    </span>
                    <span v-else class="text-slate-400">No asignado</span>
                </p>
            </div>

            <div class="rounded-2xl bg-emerald-50 p-4 md:col-span-2">
                <p class="text-xs font-bold uppercase text-emerald-500">
                    Estado
                </p>
                <p
                    class="mt-1 font-bold"
                    :class="materia.estado ? 'text-green-700' : 'text-red-700'"
                >
                    {{ materia.estado ? "Activa" : "Inactiva" }}
                </p>
            </div>

            <div class="mt-6 rounded-2xl bg-slate-50 p-4 md:col-span-2">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Carreras asignadas
                </p>

                <div
                    v-if="materia.carreras_asignadas?.length"
                    class="mt-3 flex flex-wrap gap-2"
                >
                    <span
                        v-for="asignacion in materia.carreras_asignadas"
                        :key="asignacion.id"
                        class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100"
                    >
                        {{ asignacion.carrera.nombre }}
                        <template v-if="asignacion.periodo_numero">
                            - Período {{ asignacion.periodo_numero }}
                        </template>
                    </span>
                </div>

                <p v-else class="mt-2 text-sm text-slate-500">
                    Esta materia todavía no está asignada a ninguna carrera.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const materia = computed(() => page.props.materia || {});
const editUrl = computed(() =>
    materia.value?.id
        ? route("admin.materias.edit", materia.value.id)
        : route("admin.materias.index"),
);
const indexUrl = computed(() => route("admin.materias.index"));
</script>
