<script setup>
import { Head, usePage, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import HeaderDocente from "@/views/partials/headerDocente.vue";
import PageVisitCounter from "@/views/partials/PageVisitCounter.vue";
import SeguimientoModal from "@/views/docente/materias/SeguimientoModal.vue";
import EstudianteDetalleModal from "@/views/docente/materias/EstudianteDetalleModal.vue";

const page = usePage();
const busqueda = ref("");
const estudiantes = ref([]);
const cargando = ref(false);
const mostrarSeguimientoModal = ref(false);
const mostrarDetalleModal = ref(false);
const estudianteSeleccionado = ref(null);
const estudianteDetalleSeleccionado = ref(null);
const materiaData = computed(() => page.props.materia);
const materiaId = computed(() => materiaData.value?.id);

const buscarEstudiantes = async (query = "") => {
    if (!materiaData.value) return;

    cargando.value = true;
    try {
        const response = await fetch(
            `/docente/materias/${materiaData.value.id}/estudiantes?q=${encodeURIComponent(query)}`,
        );
        const data = await response.json();
        estudiantes.value = data;
    } catch (error) {
        console.error("Error buscando estudiantes:", error);
    } finally {
        cargando.value = false;
    }
};

watch(busqueda, (newVal) => {
    buscarEstudiantes(newVal);
});

// Cargar estudiantes al montar
buscarEstudiantes("");

const verDetalles = (estudiante) => {
    estudianteDetalleSeleccionado.value = estudiante;
    mostrarDetalleModal.value = true;
};

const abrirSeguimiento = (estudiante) => {
    estudianteSeleccionado.value = estudiante;
    mostrarSeguimientoModal.value = true;
};

const cerrarSeguimiento = () => {
    mostrarSeguimientoModal.value = false;
    estudianteSeleccionado.value = null;
};

const cerrarDetalle = () => {
    mostrarDetalleModal.value = false;
    estudianteDetalleSeleccionado.value = null;
};

const handleGuardarSeguimiento = (datos) => {
    console.log("Seguimiento guardado:", datos, estudianteSeleccionado.value);

    const indice = estudiantes.value.findIndex(
        (item) => item.id === datos.inscripcion_materia_id,
    );

    if (indice !== -1) {
        estudiantes.value[indice] = {
            ...estudiantes.value[indice],
            seguimiento: datos.seguimiento,
        };
    }

    if (estudianteSeleccionado.value?.id === datos.inscripcion_materia_id) {
        estudianteSeleccionado.value = {
            ...estudianteSeleccionado.value,
            seguimiento: datos.seguimiento,
        };
    }
};

const volverAMaterias = () => {
    router.visit("/docente/materias");
};
</script>

<template>
    <Head :title="`${materiaData.nombre} - Docente`" />

    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50"
    >
        <HeaderDocente />

        <main class="px-5 pb-20 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-5xl">
                <!-- Encabezado -->
                <div class="mb-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <button
                                type="button"
                                @click="volverAMaterias"
                                class="mb-4 inline-flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-200"
                            >
                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M19 12H5M12 19l-7-7 7-7" />
                                </svg>
                                Volver
                            </button>

                            <h1 class="text-4xl font-black text-slate-900">
                                {{ materiaData.nombre }}
                            </h1>
                            <p class="mt-2 text-lg text-slate-500">
                                <span class="font-bold text-slate-700">{{
                                    materiaData.codigo
                                }}</span>
                                • {{ materiaData.carga_horaria }} horas
                            </p>
                        </div>
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-50"
                        >
                            <svg
                                class="h-8 w-8 text-emerald-600"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                                />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Buscador -->
                <div
                    class="mb-8 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
                >
                    <div class="flex items-center gap-3">
                        <svg
                            class="h-5 w-5 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        <input
                            v-model="busqueda"
                            type="text"
                            placeholder="Buscar por nombre, email o código..."
                            class="flex-1 bg-transparent text-sm outline-none placeholder:text-slate-400"
                        />
                        <svg
                            v-if="cargando"
                            class="h-5 w-5 animate-spin text-blue-600"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M21.5 2v6h-6M2.5 22v-6h6" />
                            <path
                                d="M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 2.2"
                            />
                        </svg>
                    </div>
                </div>

                <!-- Lista de estudiantes -->
                <div class="space-y-3">
                    <div
                        v-if="estudiantes.length === 0"
                        class="rounded-2xl border-2 border-dashed border-slate-200 bg-white/50 py-12 text-center"
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
                                <path
                                    d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                                />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-black text-slate-900">
                            Sin estudiantes
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{
                                busqueda
                                    ? "No se encontraron resultados para tu búsqueda"
                                    : "Aún no hay estudiantes inscritos en esta materia"
                            }}
                        </p>
                    </div>

                    <template
                        v-for="(estudiante, index) in estudiantes"
                        :key="estudiante.id"
                    >
                        <Transition
                            appear
                            appear-active-class="transition duration-500 ease-out"
                            appear-from-class="scale-95 opacity-0"
                            appear-to-class="scale-100 opacity-100"
                            :style="{
                                transitionDelay: `${index * 30}ms`,
                            }"
                        >
                            <div
                                class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:shadow-xl hover:shadow-blue-100"
                            >
                                <!-- Línea superior -->
                                <div
                                    class="absolute top-0 left-0 h-1 w-0 bg-gradient-to-r from-emerald-600 to-emerald-400 transition-all duration-300 group-hover:w-full"
                                />

                                <div
                                    class="flex items-center justify-between gap-4 p-4 sm:p-6"
                                >
                                    <!-- Foto y datos del estudiante -->
                                    <div
                                        class="flex min-w-0 flex-1 items-center gap-4"
                                    >
                                        <div>
                                            <img
                                                v-if="estudiante.foto_url"
                                                :src="estudiante.foto_url"
                                                :alt="estudiante.nombre"
                                                class="h-12 w-12 rounded-xl object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-violet-600 text-sm font-black text-white"
                                            >
                                                {{
                                                    estudiante.nombre
                                                        .charAt(0)
                                                        .toUpperCase() +
                                                    estudiante.apellido
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </div>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="truncate font-bold text-slate-900"
                                            >
                                                {{ estudiante.nombre }}
                                                {{ estudiante.apellido }}
                                            </p>
                                            <p
                                                class="mt-0.5 truncate text-sm text-slate-500"
                                            >
                                                {{ estudiante.email }}
                                            </p>
                                            <p
                                                class="mt-1 inline-flex rounded-lg bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-600"
                                            >
                                                {{ estudiante.codigo }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Acciones -->
                                    <div
                                        class="flex shrink-0 items-center gap-2"
                                    >
                                        <button
                                            type="button"
                                            :title="
                                                estudiante.seguimiento
                                                    ? 'Editar seguimiento académico'
                                                    : 'Registrar seguimiento académico'
                                            "
                                            @click="
                                                abrirSeguimiento(estudiante)
                                            "
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition hover:bg-blue-100 hover:text-blue-700"
                                        >
                                            <svg
                                                class="h-5 w-5"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path d="M9 11l3 3L22 4" />
                                                <path
                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            title="Ver detalles del estudiante"
                                            @click="verDetalles(estudiante)"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-slate-700"
                                        >
                                            <svg
                                                class="h-5 w-5"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path
                                                    d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                                                />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span
                                            v-if="estudiante.seguimiento"
                                            class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700"
                                        >
                                            Seguimiento registrado
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </template>
                </div>
            </div>
        </main>
        <SeguimientoModal
            :mostrar="mostrarSeguimientoModal"
            :materia-id="materiaId"
            :estudiante="estudianteSeleccionado"
            @cerrar="cerrarSeguimiento"
            @guardado="handleGuardarSeguimiento"
        />
        <EstudianteDetalleModal
            :mostrar="mostrarDetalleModal"
            :estudiante="estudianteDetalleSeleccionado"
            @cerrar="cerrarDetalle"
        />

        <footer
            class="fixed left-4 bottom-4 z-50 bg-transparent p-0 sm:left-6 sm:bottom-6"
        >
            <PageVisitCounter compact />
        </footer>
    </div>
</template>
