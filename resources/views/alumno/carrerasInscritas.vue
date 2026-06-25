<script setup>
import { Head } from "@inertiajs/vue3";
import HeaderAlumno from "../partials/headerAlumno.vue";
import PageVisitCounter from "../partials/PageVisitCounter.vue";

const props = defineProps({
    carrerasInscritas: {
        type: Array,
        default: () => [],
    },
    totalCarreras: {
        type: Number,
        default: 0,
    },
});

const formatearFecha = (fecha) => {
    if (!fecha) return "Sin fecha";
    const fechaObjeto = new Date(fecha);
    if (Number.isNaN(fechaObjeto.getTime())) return "Sin fecha";
    return new Intl.DateTimeFormat("es-BO", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    }).format(fechaObjeto);
};
</script>

<template>
    <Head title="Carreras inscritas" />

    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50"
    >
        <HeaderAlumno />

        <main class="px-5 pb-20 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <!-- Encabezado -->
                <div class="mb-12">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-4xl font-black text-slate-900">
                                Carreras inscritas
                            </h1>
                            <p class="mt-2 text-lg text-slate-500">
                                Revisa las carreras en las que estás inscrito,
                                su última oferta y el historial de
                                inscripciones.
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
                                <path d="M22 10 12 5 2 10l10 5 10-5Z" />
                                <path d="M6 12v5c3 2 9 2 12 0v-5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Estado vacío -->
                <div
                    v-if="carrerasInscritas.length === 0"
                    class="rounded-2xl border-2 border-dashed border-slate-200 bg-white/50 py-16 text-center"
                >
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100"
                    >
                        <svg
                            class="h-8 w-8 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M22 10 12 5 2 10l10 5 10-5Z" />
                            <path d="M6 12v5c3 2 9 2 12 0v-5" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-black text-slate-900">
                        No tienes carreras inscritas
                    </h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Cuando realices una inscripción, aparecerá aquí la
                        carrera correspondiente.
                    </p>
                </div>

                <!-- Grid de cards -->
                <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="item in carrerasInscritas"
                        :key="item.carrera.id"
                        class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:shadow-xl hover:shadow-blue-100"
                    >
                        <!-- Línea superior -->
                        <div
                            class="absolute top-0 left-0 h-1 w-0 bg-gradient-to-r from-blue-600 to-blue-400 transition-all duration-300 group-hover:w-full"
                        />
                        <!-- Contenido -->
                        <div class="relative flex flex-1 flex-col gap-3 p-6">
                            <!-- Encabezado -->
                            <div>
                                <p
                                    class="inline-flex rounded-lg bg-blue-100 px-3 py-1 text-xs font-bold text-blue-600"
                                >
                                    {{ item.carrera.codigo ?? "Sin código" }}
                                </p>
                                <h2
                                    class="mt-3 text-xl font-black text-slate-900 transition-colors duration-300 group-hover:text-blue-700"
                                >
                                    {{
                                        item.carrera.nombre ??
                                        "Carrera no registrada"
                                    }}
                                </h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{
                                        item.carrera.regimen_academico ??
                                        "Régimen no definido"
                                    }}
                                </p>
                            </div>
                            <!-- Detalles -->
                            <div
                                class="space-y-2 border-b border-slate-100 pb-3"
                            >
                                <div
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span class="text-slate-600"
                                        >Duración:</span
                                    >
                                    <span class="font-bold text-slate-900">{{
                                        item.carrera.duracion ?? "No definida"
                                    }}</span>
                                </div>
                                <div
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span class="text-slate-600"
                                        >Total de inscripciones:</span
                                    >
                                    <span class="font-bold text-slate-900">{{
                                        item.total_inscripciones
                                    }}</span>
                                </div>
                            </div>

                            <!-- Última inscripción -->
                            <div class="mt-3 space-y-1">
                                <p
                                    class="text-xs font-bold uppercase text-slate-400"
                                >
                                    Última inscripción
                                </p>
                                <p class="font-bold text-slate-900">
                                    {{
                                        item.ultima_inscripcion.oferta ??
                                        "Oferta no registrada"
                                    }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{
                                        item.ultima_inscripcion.periodo ??
                                        "Periodo no registrado"
                                    }}
                                    <span
                                        v-if="item.ultima_inscripcion.gestion"
                                    >
                                        · {{ item.ultima_inscripcion.gestion }}
                                    </span>
                                    ·
                                    {{
                                        formatearFecha(
                                            item.ultima_inscripcion
                                                .fecha_inscripcion,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- Botón -->
                        <button
                            type="button"
                            class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-50 px-4 py-2.5 text-sm font-bold text-blue-600 transition hover:bg-blue-100"
                        >
                            <svg
                                class="h-4 w-4"
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
                            Ver materias
                        </button>
                    </article>
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
