<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import HeaderAlumno from "../partials/headerAlumno.vue";
import PageVisitCounter from "../partials/PageVisitCounter.vue";

const page = usePage();

const horarios = computed(() => page.props.horarios ?? []);
const tieneHorarios = computed(() => horarios.value.length > 0);
</script>

<template>
    <Head title="Mi Horario - Alumno" />

    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50"
    >
        <HeaderAlumno />

        <main class="px-5 pb-20 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-12">
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div class="max-w-3xl">
                            <p
                                class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-blue-700"
                            >
                                Horario alumna/o
                            </p>
                            <h1 class="mt-4 text-4xl font-black text-slate-900">
                                Mi Horario
                            </h1>
                            <p class="mt-3 text-lg text-slate-500">
                                Revisa tus materias agrupadas por asignatura con
                                día, hora y aula.
                            </p>
                        </div>
                        <div
                            class="flex items-center gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-700"
                            >
                                <svg
                                    class="h-6 w-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M12 7v5l4 2" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">
                                    Materias con horario
                                </p>
                                <p class="text-sm text-slate-500">
                                    {{ horarios.length }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="tieneHorarios" class="grid gap-6 lg:grid-cols-2">
                    <template
                        v-for="materia in horarios"
                        :key="materia.materia_id"
                    >
                        <div
                            class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:shadow-xl hover:shadow-blue-100"
                        >
                            <div
                                class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-blue-500 via-blue-400 to-cyan-400"
                            ></div>
                            <div class="relative p-6">
                                <div
                                    class="mb-4 flex items-center justify-between gap-4"
                                >
                                    <div>
                                        <p
                                            class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400"
                                        >
                                            {{ materia.codigo }}
                                        </p>
                                        <h2
                                            class="mt-2 text-2xl font-black text-slate-900"
                                        >
                                            {{ materia.nombre }}
                                        </h2>
                                    </div>
                                    <span
                                        class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"
                                    >
                                        {{ materia.horarios.length }} sesión{{
                                            materia.horarios.length === 1
                                                ? ""
                                                : "es"
                                        }}
                                    </span>
                                </div>

                                <div
                                    v-if="materia.carreras.length > 0"
                                    class="mb-4 flex flex-wrap gap-2"
                                >
                                    <span
                                        v-for="carrera in materia.carreras"
                                        :key="carrera"
                                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"
                                    >
                                        {{ carrera }}
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    <template
                                        v-for="horario in materia.horarios"
                                        :key="`${horario.dia}-${horario.hora_inicio}-${horario.hora_fin}-${horario.aula}`"
                                    >
                                        <div
                                            class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <div
                                                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                                            >
                                                <div>
                                                    <p
                                                        class="text-sm font-semibold text-slate-600"
                                                    >
                                                        {{ horario.dia }}
                                                    </p>
                                                    <p
                                                        class="mt-1 text-sm text-slate-500"
                                                    >
                                                        {{
                                                            horario.hora_inicio
                                                        }}
                                                        -
                                                        {{ horario.hora_fin }} •
                                                        Aula {{ horario.aula }}
                                                    </p>
                                                </div>
                                                <span
                                                    class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200"
                                                >
                                                    {{ horario.estado }}
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div
                    v-else
                    class="rounded-3xl border border-dashed border-slate-200 bg-white/60 py-16 text-center"
                >
                    <div
                        class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-100"
                    >
                        <svg
                            class="h-10 w-10 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.996 10-10.747S17.5 6.253 12 6.253z"
                            />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-black text-slate-900">
                        Horario no disponible
                    </h3>
                    <p class="mt-2 text-sm text-slate-500">
                        No se encontraron horarios para tus materias inscritas.
                    </p>
                </div>
            </div>
        </main>

        <footer
            class="fixed left-4 bottom-4 z-50 bg-transparent p-0 sm:left-6 sm:bottom-6"
        >
            <PageVisitCounter compact />
        </footer>
    </div>
</template>
