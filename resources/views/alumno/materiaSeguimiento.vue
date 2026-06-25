<script setup>
import { computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import HeaderAlumno from "../partials/headerAlumno.vue";
import PageVisitCounter from "../partials/PageVisitCounter.vue";

const props = defineProps({
    materia: {
        type: Object,
        default: () => ({}),
    },
});

const materiaData = computed(() => props.materia || {});

const volverAMaterias = () => {
    router.visit("/alumno/materias-inscritas");
};
</script>

<template>
    <Head title="Seguimiento de materia" />

    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50"
    >
        <HeaderAlumno />

        <main class="px-5 pb-20 pt-10 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-5xl">
                <div
                    class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div>
                            <p
                                class="text-sm font-black uppercase tracking-[0.18em] text-slate-500"
                            >
                                Seguimiento
                            </p>
                            <h1 class="mt-3 text-4xl font-black text-slate-900">
                                {{ materiaData.nombre ?? "Materia" }}
                            </h1>
                            <p class="mt-2 text-sm text-slate-500">
                                Detalle de tu seguimiento académico en esta
                                materia.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="volverAMaterias"
                            class="inline-flex h-12 items-center justify-center rounded-2xl bg-slate-100 px-5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                        >
                            Volver a mis materias
                        </button>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <span
                            class="rounded-full bg-blue-50 px-3 py-2 text-sm font-bold text-blue-700"
                        >
                            {{ materiaData.codigo ?? "SIN-COD" }}
                        </span>
                        <span
                            class="rounded-full bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-700"
                        >
                            {{ materiaData.carga_horaria ?? 0 }} horas
                        </span>
                        <span
                            class="rounded-full bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700"
                        >
                            Periodo {{ materiaData.periodo_numero ?? 0 }}
                        </span>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div
                        class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <p
                            class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500"
                        >
                            Información de la materia
                        </p>
                        <div class="mt-5 space-y-4 text-sm text-slate-700">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        Código
                                    </p>
                                    <p class="mt-1">
                                        {{ materiaData.codigo ?? "-" }}
                                    </p>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        Carga horaria
                                    </p>
                                    <p class="mt-1">
                                        {{ materiaData.carga_horaria ?? "-" }}
                                        horas
                                    </p>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        Periodo
                                    </p>
                                    <p class="mt-1">
                                        {{ materiaData.periodo_numero ?? "-" }}
                                    </p>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        Estado
                                    </p>
                                    <p class="mt-1">
                                        {{ materiaData.estado ?? "Sin estado" }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <p
                            class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500"
                        >
                            Información de la carrera
                        </p>
                        <div class="mt-5 space-y-4 text-sm text-slate-700">
                            <p>
                                <span class="font-semibold">Carrera:</span>
                                {{ materiaData.carrera?.nombre ?? "-" }}
                            </p>
                            <p>
                                <span class="font-semibold"
                                    >Código de carrera:</span
                                >
                                {{ materiaData.carrera?.codigo ?? "-" }}
                            </p>
                            <p>
                                <span class="font-semibold">Oferta:</span>
                                {{ materiaData.inscripcion?.oferta ?? "-" }}
                            </p>
                            <p>
                                <span class="font-semibold">Gestión:</span>
                                {{ materiaData.inscripcion?.gestion ?? "-" }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p
                                class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500"
                            >
                                Seguimiento académico
                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                Tu nota, asistencia y observaciones registradas.
                            </p>
                        </div>
                        <span
                            class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600"
                        >
                            {{
                                materiaData.inscripcion?.fecha_inscripcion ??
                                "Sin inscripción"
                            }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm font-semibold text-slate-500">
                                Nota final
                            </p>
                            <p class="mt-3 text-3xl font-black text-slate-900">
                                {{
                                    materiaData.seguimiento?.nota_final ??
                                    "Sin nota"
                                }}
                            </p>
                        </div>

                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm font-semibold text-slate-500">
                                Asistencia
                            </p>
                            <p class="mt-3 text-3xl font-black text-slate-900">
                                {{
                                    materiaData.seguimiento
                                        ?.porcentaje_asistencia ?? "0"
                                }}%
                            </p>
                        </div>

                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm font-semibold text-slate-500">
                                Estado académico
                            </p>
                            <p class="mt-3 text-2xl font-black text-slate-900">
                                {{
                                    materiaData.seguimiento?.estado_academico ??
                                    "Sin seguimiento"
                                }}
                            </p>
                        </div>

                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm font-semibold text-slate-500">
                                Fecha de registro
                            </p>
                            <p class="mt-3 text-2xl font-black text-slate-900">
                                {{
                                    materiaData.seguimiento?.fecha_registro ??
                                    "-"
                                }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-6 rounded-3xl border border-slate-200 bg-white p-5"
                    >
                        <p class="text-sm font-semibold text-slate-500">
                            Observación
                        </p>
                        <p class="mt-3 text-sm leading-7 text-slate-700">
                            {{
                                materiaData.seguimiento?.observacion ??
                                "No hay observaciones registradas."
                            }}
                        </p>
                    </div>
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
