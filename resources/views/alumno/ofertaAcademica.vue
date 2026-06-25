<script setup>
import { computed, ref } from "vue";

import { Head, usePage } from "@inertiajs/vue3";

import HeaderAlumno from "../partials/headerAlumno.vue";
import PageVisitCounter from "../partials/PageVisitCounter.vue";
import ModalDetalleOferta from "./components/ModalDetalleOferta.vue";
import ModalInscripcionOferta from "./components/ModalInscripcionOferta.vue";

const props = defineProps({
    ofertas: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const ofertasDisponibles = computed(() => {
    return props.ofertas ?? [];
});

const cantidadOfertas = computed(() => {
    return ofertasDisponibles.value.length;
});

const mensajeExito = computed(() => {
    return page.props.flash?.success ?? null;
});

const mensajeError = computed(() => {
    return page.props.flash?.error ?? null;
});

/*
|--------------------------------------------------------------------------
| Modal de detalle
|--------------------------------------------------------------------------
*/

const ofertaDetalle = ref(null);

const modalDetalleAbierto = computed(() => {
    return ofertaDetalle.value !== null;
});

const abrirDetalle = (oferta) => {
    ofertaDetalle.value = oferta;
};

const cerrarDetalle = () => {
    ofertaDetalle.value = null;
};

/*
|--------------------------------------------------------------------------
| Modal de inscripción
|--------------------------------------------------------------------------
*/

const modalInscripcionAbierto = ref(false);
const ofertaInscripcion = ref(null);

const abrirModalInscripcion = (oferta) => {
    ofertaInscripcion.value = oferta;
    modalInscripcionAbierto.value = true;
};

const abrirInscripcionDesdeDetalle = (oferta) => {
    cerrarDetalle();
    abrirModalInscripcion(oferta);
};

const cerrarModalInscripcion = () => {
    modalInscripcionAbierto.value = false;
    ofertaInscripcion.value = null;
};

/*
|--------------------------------------------------------------------------
| Datos de la oferta
|--------------------------------------------------------------------------
*/

const obtenerNombreOferta = (oferta) => {
    return oferta?.nombre ?? oferta?.carrera?.nombre ?? "Oferta académica";
};

const obtenerCarrera = (oferta) => {
    return oferta?.carrera?.nombre ?? "Carrera no registrada";
};

const obtenerCodigoCarrera = (oferta) => {
    return oferta?.carrera?.codigo ?? "Sin código";
};

const obtenerPeriodo = (oferta) => {
    const periodo = oferta?.periodo_academico;

    if (!periodo) {
        return "Periodo no registrado";
    }

    if (periodo.gestion) {
        return `${periodo.nombre ?? "Periodo"} - ${periodo.gestion}`;
    }

    return periodo.nombre ?? "Periodo no registrado";
};

const obtenerRegimen = (oferta) => {
    return oferta?.carrera?.regimen_academico ?? "No definido";
};

const obtenerDuracion = (oferta) => {
    return oferta?.carrera?.duracion ?? "No definida";
};

const obtenerCuposDisponibles = (oferta) => {
    return Number(oferta?.cupos_disponibles ?? 0);
};

const obtenerCuposTotales = (oferta) => {
    return Number(oferta?.cantidad_cupos ?? 0);
};

const estaDisponible = (oferta) => {
    return Boolean(oferta?.estado) && obtenerCuposDisponibles(oferta) > 0;
};

const yaInscrito = (oferta) => {
    return Boolean(oferta?.inscrito);
};

const porcentajeCupos = (oferta) => {
    const disponibles = obtenerCuposDisponibles(oferta);

    const total = obtenerCuposTotales(oferta);

    if (total <= 0) {
        return 0;
    }

    return Math.min(100, Math.max(0, (disponibles / total) * 100));
};

const formatearPrecio = (valor) => {
    const numero = Number(valor ?? 0);

    return `Bs ${numero.toLocaleString("es-BO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};
</script>

<template>
    <Head title="Ofertas Académicas" />

    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50"
    >
        <HeaderAlumno />

        <main class="px-5 pb-20 pt-10 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <!-- Mensaje exitoso -->
                <div
                    v-if="mensajeExito"
                    class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="m5 12 4 4L19 6" />
                            </svg>
                        </div>

                        <div>
                            <p class="font-bold text-emerald-800">
                                Operación realizada
                            </p>

                            <p class="mt-1 text-sm text-emerald-700">
                                {{ mensajeExito }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mensaje de error -->
                <div
                    v-if="mensajeError"
                    class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="12" cy="12" r="9" />

                                <path d="M12 8v5" />
                                <path d="M12 17h.01" />
                            </svg>
                        </div>

                        <div>
                            <p class="font-bold text-red-800">
                                No se pudo completar
                            </p>

                            <p class="mt-1 text-sm text-red-700">
                                {{ mensajeError }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Encabezado -->
                <section class="mb-12">
                    <div
                        class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div>
                            <h1
                                class="text-3xl font-black text-slate-900 sm:text-4xl"
                            >
                                Ofertas Académicas
                            </h1>

                            <p
                                class="mt-2 max-w-3xl text-base text-slate-500 sm:text-lg"
                            >
                                Revisa las carreras disponibles, consulta sus
                                costos y realiza tu inscripción.
                            </p>

                            <div
                                class="mt-5 inline-flex items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700"
                            >
                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <rect
                                        width="18"
                                        height="18"
                                        x="3"
                                        y="4"
                                        rx="2"
                                    />

                                    <path d="M16 2v4M8 2v4M3 10h18" />
                                </svg>

                                {{ cantidadOfertas }}

                                {{
                                    cantidadOfertas === 1
                                        ? "oferta disponible"
                                        : "ofertas disponibles"
                                }}
                            </div>
                        </div>

                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-100 to-violet-50"
                        >
                            <svg
                                class="h-8 w-8 text-violet-600"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect
                                    width="18"
                                    height="18"
                                    x="3"
                                    y="4"
                                    rx="2"
                                />

                                <path d="M16 2v4M8 2v4M3 10h18" />
                            </svg>
                        </div>
                    </div>
                </section>

                <!-- Estado vacío -->
                <section
                    v-if="ofertasDisponibles.length === 0"
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
                            <rect width="18" height="18" x="3" y="4" rx="2" />

                            <path d="M16 2v4M8 2v4M3 10h18" />
                        </svg>
                    </div>

                    <h2 class="mt-6 text-xl font-black text-slate-900">
                        No hay ofertas académicas disponibles
                    </h2>

                    <p
                        class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500"
                    >
                        Cuando administración registre nuevas ofertas
                        académicas, aparecerán en esta sección.
                    </p>
                </section>

                <!-- Tarjetas -->
                <section
                    v-else
                    class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <article
                        v-for="oferta in ofertasDisponibles"
                        :key="oferta.id"
                        class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-100"
                    >
                        <!-- Línea superior animada -->
                        <div
                            class="absolute left-0 top-0 h-1 w-0 bg-gradient-to-r from-blue-600 to-blue-400 transition-all duration-300 group-hover:w-full"
                        ></div>

                        <div class="flex flex-1 flex-col p-6">
                            <!-- Cabecera de tarjeta -->
                            <div class="flex items-start justify-between gap-3">
                                <span
                                    class="inline-flex rounded-lg bg-blue-100 px-3 py-1 text-xs font-bold text-blue-600"
                                >
                                    {{ obtenerCodigoCarrera(oferta) }}
                                </span>

                                <span
                                    class="rounded-full px-3 py-1 text-xs font-bold"
                                    :class="
                                        estaDisponible(oferta)
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-red-50 text-red-700'
                                    "
                                >
                                    {{
                                        estaDisponible(oferta)
                                            ? "Disponible"
                                            : "No disponible"
                                    }}
                                </span>
                            </div>

                            <!-- Carrera -->
                            <div class="mt-4">
                                <h2
                                    class="line-clamp-2 min-h-14 text-xl font-black leading-tight text-slate-900 transition-colors duration-300 group-hover:text-blue-700"
                                >
                                    {{ obtenerCarrera(oferta) }}
                                </h2>

                                <p
                                    class="mt-2 line-clamp-2 min-h-10 text-sm leading-5 text-slate-500"
                                >
                                    {{ obtenerNombreOferta(oferta) }}
                                </p>
                            </div>

                            <!-- Régimen y duración -->
                            <div class="mt-5 grid grid-cols-2 gap-3">
                                <div
                                    class="rounded-xl border border-slate-200 bg-slate-50 p-3"
                                >
                                    <p
                                        class="text-[11px] font-bold uppercase tracking-wider text-slate-400"
                                    >
                                        Régimen
                                    </p>

                                    <p
                                        class="mt-1 text-sm font-bold text-slate-900"
                                    >
                                        {{ obtenerRegimen(oferta) }}
                                    </p>
                                </div>

                                <div
                                    class="rounded-xl border border-slate-200 bg-slate-50 p-3"
                                >
                                    <p
                                        class="text-[11px] font-bold uppercase tracking-wider text-slate-400"
                                    >
                                        Duración
                                    </p>

                                    <p
                                        class="mt-1 text-sm font-bold text-slate-900"
                                    >
                                        {{ obtenerDuracion(oferta) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Periodo -->
                            <div class="mt-5 flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <rect
                                            width="18"
                                            height="18"
                                            x="3"
                                            y="4"
                                            rx="2"
                                        />

                                        <path d="M16 2v4M8 2v4M3 10h18" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <p
                                        class="text-xs font-semibold text-slate-400"
                                    >
                                        Periodo académico
                                    </p>

                                    <p
                                        class="truncate text-sm font-bold text-slate-700"
                                    >
                                        {{ obtenerPeriodo(oferta) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Cupos -->
                            <div class="mt-5 border-t border-slate-100 pt-5">
                                <div
                                    class="mb-2 flex items-center justify-between text-sm"
                                >
                                    <span class="font-semibold text-slate-600">
                                        Cupos disponibles
                                    </span>

                                    <span
                                        class="font-black"
                                        :class="
                                            estaDisponible(oferta)
                                                ? 'text-emerald-600'
                                                : 'text-red-500'
                                        "
                                    >
                                        {{ obtenerCuposDisponibles(oferta) }}
                                        /
                                        {{ obtenerCuposTotales(oferta) }}
                                    </span>
                                </div>

                                <div
                                    class="h-2.5 overflow-hidden rounded-full bg-slate-100"
                                >
                                    <div
                                        class="h-full rounded-full transition-all duration-500"
                                        :class="
                                            estaDisponible(oferta)
                                                ? 'bg-emerald-500'
                                                : 'bg-red-400'
                                        "
                                        :style="{
                                            width:
                                                porcentajeCupos(oferta) + '%',
                                        }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Costos -->
                            <div
                                class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4"
                            >
                                <p
                                    class="mb-3 text-xs font-black uppercase tracking-widest text-slate-400"
                                >
                                    Costos
                                </p>

                                <div class="space-y-2 text-sm">
                                    <div
                                        class="flex items-center justify-between gap-3"
                                    >
                                        <span class="text-slate-500">
                                            Matrícula
                                        </span>

                                        <strong class="text-slate-900">
                                            {{
                                                formatearPrecio(
                                                    oferta.precio_matricula,
                                                )
                                            }}
                                        </strong>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-3"
                                    >
                                        <span class="text-slate-500">
                                            Mensualidad
                                        </span>

                                        <strong class="text-slate-900">
                                            {{
                                                formatearPrecio(
                                                    oferta.precio_mensualidad,
                                                )
                                            }}
                                        </strong>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-3 border-t border-slate-200 pt-2"
                                    >
                                        <span class="font-bold text-slate-700">
                                            Carrera completa
                                        </span>

                                        <strong class="text-blue-700">
                                            {{
                                                formatearPrecio(
                                                    oferta.precio_carrera_completa,
                                                )
                                            }}
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="mt-auto flex gap-3 pt-6">
                                <button
                                    type="button"
                                    class="flex-1 cursor-pointer rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                    @click="abrirDetalle(oferta)"
                                >
                                    Ver detalle
                                </button>

                                <button
                                    type="button"
                                    class="flex-1 rounded-xl px-4 py-3 text-sm font-bold text-white transition"
                                    :class="
                                        yaInscrito(oferta)
                                            ? 'cursor-not-allowed bg-slate-300'
                                            : estaDisponible(oferta)
                                              ? 'cursor-pointer bg-blue-600 hover:bg-blue-700'
                                              : 'cursor-not-allowed bg-slate-300'
                                    "
                                    :disabled="
                                        !estaDisponible(oferta) ||
                                        yaInscrito(oferta)
                                    "
                                    @click="abrirModalInscripcion(oferta)"
                                >
                                    {{
                                        yaInscrito(oferta)
                                            ? "Ya estás inscrito"
                                            : "Inscribirme"
                                    }}
                                </button>
                            </div>
                        </div>
                    </article>
                </section>
            </div>
        </main>

        <footer
            class="fixed left-4 bottom-4 z-50 bg-transparent p-0 sm:left-6 sm:bottom-6"
        >
            <PageVisitCounter compact />
        </footer>

        <!-- Modal de detalle -->
        <ModalDetalleOferta
            :mostrar="modalDetalleAbierto"
            :oferta="ofertaDetalle"
            @cerrar="cerrarDetalle"
            @inscribirse="abrirInscripcionDesdeDetalle"
        />

        <!-- Modal de inscripción -->
        <ModalInscripcionOferta
            :mostrar="modalInscripcionAbierto"
            :oferta="ofertaInscripcion"
            @cerrar="cerrarModalInscripcion"
        />
    </div>
</template>
