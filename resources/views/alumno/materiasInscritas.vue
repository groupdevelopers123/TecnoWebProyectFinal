<script setup>
import { computed, ref } from "vue";
import { Head, router } from "@inertiajs/vue3";

import HeaderAlumno from "../partials/headerAlumno.vue";
import PageVisitCounter from "../partials/PageVisitCounter.vue";

const props = defineProps({
    carrerasConMaterias: {
        type: Array,
        default: () => [],
    },

    totalCarreras: {
        type: Number,
        default: 0,
    },

    totalMaterias: {
        type: Number,
        default: 0,
    },
});

const selectedCarrera = ref("");

const carreras = computed(() =>
    props.carrerasConMaterias
        .map((item) => item.carrera)
        .filter((carrera) => carrera && carrera.id),
);

const materias = computed(() =>
    props.carrerasConMaterias.flatMap((item) =>
        (item.materias || []).map((materia) => ({
            ...materia,
            carrera_id: item.carrera?.id,
            carrera_nombre: item.carrera?.nombre,
        })),
    ),
);

const materiasFiltradas = computed(() => {
    if (!selectedCarrera.value) {
        return materias.value;
    }

    return materias.value.filter(
        (materia) =>
            String(materia.carrera_id) === String(selectedCarrera.value),
    );
});

const verSeguimiento = (materiaId) => {
    router.visit(`/alumno/materias-inscritas/${materiaId}/seguimiento`);
};

const formatearFecha = (fecha) => {
    if (!fecha) {
        return "Sin fecha";
    }

    const fechaObjeto = new Date(fecha);

    if (Number.isNaN(fechaObjeto.getTime())) {
        return "Sin fecha";
    }

    return new Intl.DateTimeFormat("es-BO", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    }).format(fechaObjeto);
};

const colorPorPeriodo = (periodo) => {
    const opciones = [
        "border-blue-100 bg-blue-50 text-blue-700",
        "border-emerald-100 bg-emerald-50 text-emerald-700",
        "border-violet-100 bg-violet-50 text-violet-700",
        "border-amber-100 bg-amber-50 text-amber-700",
    ];

    const numeroPeriodo = Math.max(1, Number(periodo) || 1);

    return opciones[(numeroPeriodo - 1) % opciones.length];
};

const colorPorEstado = (estado) => {
    const estadoNormalizado = String(estado ?? "").toLowerCase();

    if (estadoNormalizado.includes("aprob")) {
        return "bg-emerald-50 text-emerald-700";
    }

    if (estadoNormalizado.includes("reprob")) {
        return "bg-red-50 text-red-700";
    }

    if (estadoNormalizado.includes("curs")) {
        return "bg-blue-50 text-blue-700";
    }

    return "bg-slate-100 text-slate-600";
};
</script>

<template>
    <Head title="Mis materias" />

    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50"
    >
        <HeaderAlumno />

        <main class="px-5 pb-20 pt-10 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <!-- Encabezado principal -->
                <section class="mb-12">
                    <div
                        class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div>
                            <h1
                                class="text-3xl font-black text-slate-900 sm:text-4xl"
                            >
                                Mis materias inscritas
                            </h1>

                            <p
                                class="mt-2 max-w-3xl text-base text-slate-500 sm:text-lg"
                            >
                                Revisa las materias correspondientes a cada
                                carrera, sus periodos y el estado de tus
                                inscripciones.
                            </p>

                            <!-- Contadores -->
                            <div class="mt-5 flex flex-wrap gap-3">
                                <div
                                    class="inline-flex items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="M22 10 12 5 2 10l10 5 10-5Z" />

                                        <path d="M6 12v5c3 2 9 2 12 0v-5" />
                                    </svg>

                                    {{ totalCarreras }}

                                    {{
                                        totalCarreras === 1
                                            ? "carrera"
                                            : "carreras"
                                    }}
                                </div>

                                <div
                                    class="inline-flex items-center gap-2 rounded-xl border border-violet-100 bg-violet-50 px-4 py-2 text-sm font-bold text-violet-700"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"
                                        />

                                        <path
                                            d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"
                                        />
                                    </svg>

                                    {{ totalMaterias }}

                                    {{
                                        totalMaterias === 1
                                            ? "materia"
                                            : "materias"
                                    }}
                                </div>
                            </div>
                        </div>

                        <!-- Icono -->
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
                </section>

                <!-- Estado vacío -->
                <section
                    v-if="carrerasConMaterias.length === 0"
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
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />

                            <path
                                d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"
                            />
                        </svg>
                    </div>

                    <h2 class="mt-6 text-xl font-black text-slate-900">
                        No tienes materias inscritas
                    </h2>

                    <p
                        class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500"
                    >
                        Cuando se registren materias en una de tus carreras
                        inscritas, aparecerán en esta sección.
                    </p>
                </section>

                <!-- Carreras con materias -->
                <section
                    v-if="carrerasConMaterias.length > 0"
                    class="space-y-8"
                >
                    <section
                        class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <div
                            class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
                        >
                            <div>
                                <p
                                    class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400"
                                >
                                    Filtrar por carrera
                                </p>
                                <p class="mt-2 text-sm text-slate-500">
                                    Selecciona una carrera y revisa las materias
                                    inscritas en ella.
                                </p>
                            </div>

                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-center"
                            >
                                <label class="sr-only" for="filtro-carrera"
                                    >Carrera</label
                                >
                                <select
                                    id="filtro-carrera"
                                    v-model="selectedCarrera"
                                    class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                >
                                    <option value="">Todas las carreras</option>
                                    <option
                                        v-for="carrera in carreras"
                                        :key="carrera.id"
                                        :value="carrera.id"
                                    >
                                        {{ carrera.nombre }}
                                    </option>
                                </select>

                                <div
                                    class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700"
                                >
                                    {{ materiasFiltradas.length }} materia{{
                                        materiasFiltradas.length === 1
                                            ? ""
                                            : "s"
                                    }}
                                </div>
                            </div>
                        </div>
                    </section>

                    <section
                        v-if="materiasFiltradas.length === 0"
                        class="rounded-2xl border-2 border-dashed border-slate-200 bg-white/50 p-16 text-center"
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
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                <path
                                    d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"
                                />
                            </svg>
                        </div>

                        <h2 class="mt-6 text-xl font-black text-slate-900">
                            No hay materias para este filtro
                        </h2>
                        <p
                            class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500"
                        >
                            Cambia el filtro de carrera o revisa tus materias
                            inscritas para ver más información.
                        </p>
                    </section>

                    <section
                        v-else
                        class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"
                    >
                        <article
                            v-for="materia in materiasFiltradas"
                            :key="materia.id"
                            @click="verSeguimiento(materia.id)"
                            class="group cursor-pointer overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p
                                        class="truncate text-[11px] font-black uppercase tracking-[0.18em] text-slate-400"
                                    >
                                        {{ materia.codigo ?? "SIN-COD" }}
                                    </p>
                                    <h3
                                        class="mt-3 line-clamp-2 text-xl font-black text-slate-900 transition-colors duration-300 group-hover:text-blue-700"
                                    >
                                        {{
                                            materia.nombre ??
                                            "Materia no registrada"
                                        }}
                                    </h3>
                                </div>

                                <span
                                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700"
                                >
                                    P{{ materia.periodo_numero ?? 0 }}
                                </span>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-2">
                                <span
                                    class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"
                                >
                                    {{
                                        materia.carrera_nombre ??
                                        "Carrera desconocida"
                                    }}
                                </span>
                                <span
                                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"
                                >
                                    {{ materia.carga_horaria ?? 0 }} hrs
                                </span>
                            </div>

                            <div
                                class="mt-6 space-y-3 border-t border-slate-100 pt-4 text-sm text-slate-600"
                            >
                                <div
                                    class="flex items-center justify-between gap-3"
                                >
                                    <span>Estado</span>
                                    <span class="font-bold text-slate-900">{{
                                        materia.estado ?? "Sin estado"
                                    }}</span>
                                </div>
                                <div
                                    class="flex items-center justify-between gap-3"
                                >
                                    <span>Última inscripción</span>
                                    <span class="font-bold text-slate-900">{{
                                        formatearFecha(
                                            materia.fecha_inscripcion,
                                        )
                                    }}</span>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-2">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-bold"
                                    :class="colorPorEstado(materia.estado)"
                                >
                                    {{ materia.estado ?? "Sin estado" }}
                                </span>
                                <span
                                    class="rounded-full border px-3 py-1 text-xs font-semibold text-slate-500"
                                    :class="
                                        colorPorPeriodo(materia.periodo_numero)
                                    "
                                >
                                    Periodo {{ materia.periodo_numero ?? 0 }}
                                </span>
                            </div>
                        </article>
                    </section>
                </section>
            </div>
        </main>

        <footer
            class="fixed left-4 bottom-4 z-50 bg-transparent p-0 sm:left-6 sm:bottom-6"
        >
            <PageVisitCounter compact />
        </footer>
    </div>
</template>
