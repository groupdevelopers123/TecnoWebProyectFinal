<script setup>
import { computed } from "vue";

const props = defineProps({
    mostrar: {
        type: Boolean,
        default: false,
    },
    estudiante: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["cerrar"]);

const cerrarModal = () => {
    emit("cerrar");
};

const nombreEstudiante = computed(() => {
    return props.estudiante
        ? `${props.estudiante.nombre} ${props.estudiante.apellido}`
        : "Alumno";
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="mostrar"
                class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm px-4 py-6"
                @click.self="cerrarModal"
            >
                <div
                    class="h-full w-full max-w-[750px] rounded-[32px] bg-white shadow-2xl ring-1 ring-slate-200 sm:w-[60vw]"
                >
                    <div class="flex h-full flex-col overflow-hidden">
                        <div
                            class="flex items-center justify-between gap-4 border-b border-slate-200 bg-slate-50 p-5"
                        >
                            <div>
                                <p
                                    class="text-sm font-black uppercase tracking-[0.18em] text-slate-500"
                                >
                                    Detalle del estudiante
                                </p>
                                <h2
                                    class="mt-2 text-3xl font-black text-slate-900"
                                >
                                    {{ nombreEstudiante }}
                                </h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    Información del alumno y su seguimiento
                                    académico.
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="cerrarModal"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 transition hover:bg-slate-200"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="grid min-h-[60vh] gap-6 p-6 sm:grid-cols-2">
                            <div class="space-y-5">
                                <div
                                    class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm"
                                >
                                    <p
                                        class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500"
                                    >
                                        Datos personales
                                    </p>
                                    <div
                                        class="mt-4 space-y-3 text-sm text-slate-700"
                                    >
                                        <p>
                                            <span class="font-semibold"
                                                >Código:</span
                                            >
                                            {{ estudiante?.codigo ?? "-" }}
                                        </p>
                                        <p>
                                            <span class="font-semibold"
                                                >Cédula:</span
                                            >
                                            {{ estudiante?.ci ?? "-" }}
                                        </p>
                                        <p>
                                            <span class="font-semibold"
                                                >Correo:</span
                                            >
                                            {{ estudiante?.email ?? "-" }}
                                        </p>
                                        <p>
                                            <span class="font-semibold"
                                                >Teléfono:</span
                                            >
                                            {{ estudiante?.telefono ?? "-" }}
                                        </p>
                                        <p>
                                            <span class="font-semibold"
                                                >Dirección:</span
                                            >
                                            {{ estudiante?.direccion ?? "-" }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm"
                                >
                                    <p
                                        class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500"
                                    >
                                        Información académica
                                    </p>
                                    <div
                                        class="mt-4 space-y-3 text-sm text-slate-700"
                                    >
                                        <p>
                                            <span class="font-semibold"
                                                >Estado académico:</span
                                            >
                                            {{
                                                estudiante?.estado_academico ??
                                                "-"
                                            }}
                                        </p>
                                        <p>
                                            <span class="font-semibold"
                                                >Colegio de origen:</span
                                            >
                                            {{
                                                estudiante?.colegio_origen ??
                                                "-"
                                            }}
                                        </p>
                                        <p>
                                            <span class="font-semibold"
                                                >Año de bachillerato:</span
                                            >
                                            {{
                                                estudiante?.anio_bachillerato ??
                                                "-"
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div
                                    class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm"
                                >
                                    <p
                                        class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500"
                                    >
                                        Seguimiento académico
                                    </p>
                                    <div
                                        class="mt-4 space-y-3 text-sm text-slate-700"
                                    >
                                        <p>
                                            <span class="font-semibold"
                                                >Nota final:</span
                                            >
                                            {{
                                                estudiante?.seguimiento
                                                    ?.nota_final ??
                                                "No registrada"
                                            }}
                                        </p>
                                        <p>
                                            <span class="font-semibold"
                                                >Asistencia:</span
                                            >
                                            {{
                                                estudiante?.seguimiento
                                                    ?.porcentaje_asistencia
                                                    ? estudiante.seguimiento
                                                          .porcentaje_asistencia +
                                                      "%"
                                                    : "No registrada"
                                            }}
                                        </p>
                                        <p>
                                            <span class="font-semibold"
                                                >Estado:</span
                                            >
                                            {{
                                                estudiante?.seguimiento
                                                    ?.estado_academico ??
                                                "No registrado"
                                            }}
                                        </p>
                                        <p>
                                            <span class="font-semibold"
                                                >Observación:</span
                                            >
                                            {{
                                                estudiante?.seguimiento
                                                    ?.observacion ??
                                                "Sin observación"
                                            }}
                                        </p>
                                        <p>
                                            <span class="font-semibold"
                                                >Fecha registro:</span
                                            >
                                            {{
                                                estudiante?.seguimiento
                                                    ?.fecha_registro ?? "-"
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm"
                                >
                                    <p
                                        class="text-sm font-bold uppercase tracking-[0.16em] text-slate-500"
                                    >
                                        Más detalles
                                    </p>
                                    <div
                                        class="mt-4 space-y-3 text-sm text-slate-700"
                                    >
                                        <p>
                                            <span class="font-semibold"
                                                >Materia actual:</span
                                            >
                                            {{
                                                estudiante?.materia_nombre ??
                                                "-"
                                            }}
                                        </p>
                                        <p>
                                            <span class="font-semibold"
                                                >Docente:</span
                                            >
                                            {{
                                                estudiante?.docente_nombre ??
                                                "-"
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="border-t border-slate-200 bg-white p-5 text-right"
                        >
                            <button
                                type="button"
                                @click="cerrarModal"
                                class="inline-flex rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
