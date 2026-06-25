<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { computed } from "vue";
import HeaderDocente from "@/views/partials/headerDocente.vue";
import PageVisitCounter from "@/views/partials/PageVisitCounter.vue";

const page = usePage();

const materias = computed(() => {
    return page.props.materias ?? [];
});

const tieneMateria = computed(() => {
    return materias.value.length > 0;
});

const irAlDetalle = (materiaId) => {
    router.visit(`/docente/materias/${materiaId}`);
};
</script>

<template>
    <Head title="Mis Materias - Docente" />

    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50"
    >
        <HeaderDocente />

        <main class="px-5 pb-20 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <!-- Encabezado -->
                <div class="mb-12">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-4xl font-black text-slate-900">
                                Mis Materias
                            </h1>
                            <p class="mt-2 text-lg text-slate-500">
                                Materias que enseñas en este período académico.
                            </p>
                        </div>
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50"
                        >
                            <svg
                                class="h-8 w-8 text-blue-600"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                <path
                                    d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tarjetas de materias o estado vacío -->
                <div
                    v-if="tieneMateria"
                    class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <template
                        v-for="(materia, index) in materias"
                        :key="materia.id"
                    >
                        <Transition
                            appear
                            appear-active-class="transition duration-500 ease-out"
                            appear-from-class="scale-95 opacity-0"
                            appear-to-class="scale-100 opacity-100"
                            :style="{
                                transitionDelay: `${index * 50}ms`,
                            }"
                        >
                            <button
                                type="button"
                                @click="irAlDetalle(materia.id)"
                                class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:shadow-xl hover:shadow-blue-100 text-left w-full"
                            >
                                <!-- Gradiente de fondo animado -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                                />

                                <!-- Línea superior -->
                                <div
                                    class="absolute top-0 left-0 h-1 w-0 bg-gradient-to-r from-blue-600 to-blue-400 transition-all duration-300 group-hover:w-full"
                                />

                                <!-- Contenido -->
                                <div class="relative p-6">
                                    <!-- Encabezado -->
                                    <div
                                        class="mb-4 flex items-start justify-between"
                                    >
                                        <div>
                                            <p
                                                class="inline-flex rounded-lg bg-blue-100 px-3 py-1 text-xs font-bold text-blue-600"
                                            >
                                                {{ materia.codigo }}
                                            </p>
                                        </div>
                                        <span
                                            v-if="materia.estado"
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="3"
                                            >
                                                <polyline
                                                    points="20 6 9 17 4 12"
                                                />
                                            </svg>
                                        </span>
                                        <span
                                            v-else
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-red-50 text-red-600"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                            >
                                                <circle cx="12" cy="12" r="2" />
                                            </svg>
                                        </span>
                                    </div>

                                    <!-- Nombre de la materia -->
                                    <h2
                                        class="mb-3 text-xl font-black text-slate-900 transition-colors duration-300 group-hover:text-blue-700"
                                    >
                                        {{ materia.nombre }}
                                    </h2>

                                    <!-- Detalles -->
                                    <div
                                        class="mb-4 space-y-2 border-b border-slate-100 pb-4"
                                    >
                                        <div
                                            class="flex items-center gap-2 text-sm text-slate-600"
                                        >
                                            <svg
                                                class="h-4 w-4 text-blue-500"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <circle cx="12" cy="12" r="9" />
                                                <polyline
                                                    points="12 6 12 12 16 14"
                                                />
                                            </svg>
                                            <span
                                                ><strong>{{
                                                    materia.carga_horaria
                                                }}</strong>
                                                horas de carga horaria</span
                                            >
                                        </div>
                                    </div>

                                    <!-- Carreras -->
                                    <div
                                        v-if="materia.carreras.length > 0"
                                        class="space-y-2"
                                    >
                                        <p
                                            class="text-xs font-bold uppercase text-slate-400"
                                        >
                                            Ofertas en:
                                        </p>
                                        <div class="flex flex-wrap gap-2">
                                            <span
                                                v-for="carrera in materia.carreras"
                                                :key="carrera"
                                                class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 transition-all duration-300 group-hover:bg-blue-100 group-hover:text-blue-700"
                                            >
                                                {{ carrera }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </Transition>
                    </template>
                </div>

                <!-- Estado vacío -->
                <div
                    v-else
                    class="rounded-2xl border-2 border-dashed border-slate-200 bg-white/50 py-16 text-center"
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
                        Sin materias asignadas
                    </h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Todavía no tienes materias asignadas para este período.
                        Contacta con el administrador para solicitar
                        asignaciones.
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
