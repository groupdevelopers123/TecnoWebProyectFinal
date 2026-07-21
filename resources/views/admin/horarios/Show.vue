<template>
    <Head title="Detalle de Horario" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-2xl text-blue-700"
                >
                    <i class="fa-solid fa-clock"></i>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900">
                        {{ horario.dia }} / {{ horario.hora_inicio }} -
                        {{ horario.hora_fin }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ horario.turno }} —
                        {{
                            horario.periodo_academico?.nombre ||
                            horario.periodo_nombre
                        }}
                    </p>
                </div>
            </div>

            <div class="flex gap-3">
                <a
                    :href="editUrl"
                    class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white"
                >
                    Editar
                </a>

                <a
                    :href="indexUrl"
                    class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700"
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
                    {{ horario.carrera_nombre }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Materia
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ horario.materia_nombre }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Docente
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ horario.docente_nombre || "No asignado" }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Aula</p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ horario.aula_nombre }} - {{ horario.aula_codigo }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Día</p>
                <p class="mt-1 font-bold text-slate-800">{{ horario.dia }}</p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Turno</p>
                <p class="mt-1 font-bold text-slate-800">{{ horario.turno }}</p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Hora inicio
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ horario.hora_inicio }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Hora fin
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ horario.hora_fin }}
                </p>
            </div>

            <div class="rounded-2xl bg-blue-50 p-4 md:col-span-2">
                <p class="text-xs font-bold uppercase text-blue-500">
                    Periodo académico
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{
                        horario.periodo_academico?.nombre ||
                        horario.periodo_nombre
                    }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                <p class="text-xs font-bold uppercase text-slate-400">Estado</p>
                <p
                    class="mt-1 font-bold"
                    :class="horario.estado ? 'text-green-700' : 'text-red-700'"
                >
                    {{ horario.estado ? "Activo" : "Inactivo" }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const horario = computed(() => page.props.horario || {});
const editUrl = computed(() =>
    horario.value?.id
        ? route("admin.horarios.edit", horario.value.id)
        : route("admin.horarios.index"),
);
const indexUrl = computed(() => route("admin.horarios.index"));
</script>
