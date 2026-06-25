<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    mostrar: {
        type: Boolean,
        default: false,
    },
    materiaId: {
        type: [Number, String],
        required: true,
    },
    estudiante: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["cerrar", "guardado"]);

const formulario = useForm({
    inscripcion_materia_id: null,
    nota_final: "",
    porcentaje_asistencia: "",
    estado_academico: "Cursando",
    observacion: "",
    fecha_registro: new Date().toISOString().slice(0, 10),
});

const guardando = ref(false);
const mensajeExito = ref("");
const mostrarMensajeExito = ref(false);

const esEdicion = computed(() => {
    return Boolean(props.estudiante?.seguimiento?.id);
});

const tituloModal = computed(() =>
    esEdicion.value ? "Editar seguimiento" : "Registrar seguimiento",
);

const botonTexto = computed(() =>
    esEdicion.value ? "Actualizar seguimiento" : "Guardar seguimiento",
);

const nombreEstudiante = computed(() => {
    return props.estudiante
        ? `${props.estudiante.nombre} ${props.estudiante.apellido}`
        : "Alumno";
});

const estadoOpciones = ["Cursando", "Aprobado", "Reprobado", "Retirado"];

watch(
    () => [props.mostrar, props.estudiante?.id],
    ([mostrar, estudianteId]) => {
        if (!mostrar || !estudianteId) {
            return;
        }

        formulario.reset();
        formulario.clearErrors();
        mensajeExito.value = "";
        mostrarMensajeExito.value = false;

        formulario.inscripcion_materia_id = props.estudiante.id;
        formulario.nota_final = props.estudiante.seguimiento?.nota_final ?? "";
        formulario.porcentaje_asistencia =
            props.estudiante.seguimiento?.porcentaje_asistencia ?? "";
        formulario.estado_academico =
            props.estudiante.seguimiento?.estado_academico ?? "Cursando";
        formulario.observacion =
            props.estudiante.seguimiento?.observacion ?? "";
        formulario.fecha_registro =
            props.estudiante.seguimiento?.fecha_registro ??
            new Date().toISOString().slice(0, 10);
    },
    { immediate: true },
);

const cerrarModal = () => {
    if (guardando.value) {
        return;
    }

    formulario.clearErrors();
    mensajeExito.value = "";
    mostrarMensajeExito.value = false;
    emit("cerrar");
};

const obtenerCsrfToken = () => {
    return (
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") ?? ""
    );
};

const enviarSeguimiento = async () => {
    if (guardando.value || !props.materiaId || !props.estudiante) {
        return;
    }

    formulario.clearErrors();
    guardando.value = true;

    const url = `/docente/materias/${props.materiaId}/seguimientos${
        esEdicion.value ? `/${props.estudiante.seguimiento.id}` : ""
    }`;
    const method = esEdicion.value ? "PUT" : "POST";

    const payload = {
        inscripcion_materia_id: formulario.inscripcion_materia_id,
        nota_final: formulario.nota_final,
        porcentaje_asistencia: formulario.porcentaje_asistencia,
        estado_academico: formulario.estado_academico,
        observacion: formulario.observacion,
        fecha_registro: formulario.fecha_registro,
    };

    try {
        const response = await fetch(url, {
            method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": obtenerCsrfToken(),
                Accept: "application/json",
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 422 && data.errors) {
                formulario.setErrors(data.errors);
            } else {
                formulario.setErrors({
                    general:
                        data.message ||
                        "Ocurrió un error al guardar el seguimiento.",
                });
            }
            return;
        }

        mensajeExito.value =
            data.message ||
            (esEdicion.value
                ? "Actualización del seguimiento exitosa"
                : "Registro del seguimiento exitoso");
        mostrarMensajeExito.value = true;

        emit("guardado", {
            inscripcion_materia_id: formulario.inscripcion_materia_id,
            seguimiento: {
                id: data.seguimiento?.id ?? props.estudiante.seguimiento?.id,
                nota_final: formulario.nota_final,
                porcentaje_asistencia: formulario.porcentaje_asistencia,
                estado_academico: formulario.estado_academico,
                observacion: formulario.observacion,
                fecha_registro: formulario.fecha_registro,
            },
        });

        setTimeout(() => {
            cerrarModal();
        }, 1300);
    } catch (error) {
        formulario.setErrors({
            general:
                "Ocurrió un error de comunicación. Intenta de nuevo más tarde.",
        });
        console.error("Error al enviar seguimiento:", error);
    } finally {
        guardando.value = false;
    }
};

const cerrarConEscape = (evento) => {
    if (evento.key === "Escape" && props.mostrar) {
        cerrarModal();
    }
};

onMounted(() => document.addEventListener("keydown", cerrarConEscape));
onBeforeUnmount(() => document.removeEventListener("keydown", cerrarConEscape));
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
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
                @click.self="cerrarModal"
            >
                <div
                    class="w-full max-w-3xl rounded-[32px] bg-white shadow-2xl ring-1 ring-slate-200"
                >
                    <div
                        class="grid min-h-[60vh] grid-cols-1 gap-6 rounded-[32px] bg-gradient-to-br from-slate-50 via-white to-blue-50 p-6 sm:p-8"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p
                                    class="text-sm font-black uppercase tracking-[0.18em] text-blue-500"
                                >
                                    {{ tituloModal }}
                                </p>
                                <h2
                                    class="mt-3 text-2xl font-black text-slate-900"
                                >
                                    {{ nombreEstudiante }}
                                </h2>
                                <p class="mt-2 text-sm text-slate-500">
                                    Registra o actualiza la información de
                                    seguimiento académico del alumno.
                                </p>

                                <Transition
                                    enter-active-class="transition duration-200 ease-out"
                                    enter-from-class="opacity-0 scale-95"
                                    enter-to-class="opacity-100 scale-100"
                                    leave-active-class="transition duration-150 ease-in"
                                    leave-from-class="opacity-100 scale-100"
                                    leave-to-class="opacity-0 scale-95"
                                >
                                    <div
                                        v-if="mostrarMensajeExito"
                                        class="mt-4 rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700 shadow-sm"
                                    >
                                        {{ mensajeExito }}
                                    </div>
                                </Transition>
                                <div
                                    v-if="formulario.errors.general"
                                    class="mt-4 rounded-3xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700 shadow-sm"
                                >
                                    {{ formulario.errors.general }}
                                </div>
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

                        <div
                            class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                        >
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="text-sm font-bold text-slate-700"
                                        >Nota final</label
                                    >
                                    <input
                                        v-model="formulario.nota_final"
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        placeholder="Ej. 85.50"
                                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="text-sm font-bold text-slate-700"
                                        >Asistencia (%)</label
                                    >
                                    <input
                                        v-model="
                                            formulario.porcentaje_asistencia
                                        "
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.1"
                                        placeholder="Ej. 95"
                                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    />
                                </div>
                            </div>

                            <div class="mt-5">
                                <label class="text-sm font-bold text-slate-700"
                                    >Estado académico</label
                                >
                                <select
                                    v-model="formulario.estado_academico"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                >
                                    <option
                                        v-for="estado in estadoOpciones"
                                        :key="estado"
                                        :value="estado"
                                    >
                                        {{ estado }}
                                    </option>
                                </select>
                                <p
                                    v-if="formulario.errors.estado_academico"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ formulario.errors.estado_academico }}
                                </p>
                            </div>

                            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="text-sm font-bold text-slate-700"
                                        >Fecha de registro</label
                                    >
                                    <input
                                        v-model="formulario.fecha_registro"
                                        type="date"
                                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    />
                                    <p
                                        v-if="formulario.errors.fecha_registro"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{ formulario.errors.fecha_registro }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-5">
                                <label class="text-sm font-bold text-slate-700"
                                    >Observación</label
                                >
                                <textarea
                                    v-model="formulario.observacion"
                                    rows="4"
                                    placeholder="Escribe alguna observación sobre el seguimiento..."
                                    class="mt-2 w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                ></textarea>
                                <p
                                    v-if="formulario.errors.observacion"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ formulario.errors.observacion }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end"
                        >
                            <button
                                type="button"
                                @click="cerrarModal"
                                :disabled="guardando"
                                class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100 sm:w-auto disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                Cancelar
                            </button>
                            <button
                                type="button"
                                @click="enviarSeguimiento"
                                :disabled="guardando"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-black text-white transition hover:bg-blue-700 sm:w-auto disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                {{ botonTexto }}
                                <span
                                    v-if="guardando"
                                    class="ml-2 inline-block h-3.5 w-3.5 animate-spin rounded-full border-2 border-white border-t-transparent"
                                />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
