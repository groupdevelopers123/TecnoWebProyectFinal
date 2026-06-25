<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";

import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    mostrar: {
        type: Boolean,
        default: false,
    },

    oferta: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["cerrar"]);

const formulario = useForm({
    oferta_academica_id: null,
    metodo_pago: "",
    tipo_pago: "matricula",
});

const qrGeneradoUrl = ref("");
const mensajeQr = ref("");
const tipoMensajeQr = ref("info");

const nombreCarrera = computed(() => {
    return props.oferta?.carrera?.nombre ?? "Carrera no registrada";
});

const nombreOferta = computed(() => {
    return props.oferta?.nombre ?? nombreCarrera.value;
});

const montoSeleccionado = computed(() => {
    if (!props.oferta) {
        return 0;
    }

    if (formulario.tipo_pago === "carrera_completa") {
        return Number(props.oferta.precio_carrera_completa ?? 0);
    }

    return Number(props.oferta.precio_matricula ?? 0);
});

watch(
    () => [props.mostrar, props.oferta?.id],
    ([mostrar, ofertaId]) => {
        if (!mostrar || !ofertaId) {
            return;
        }

        formulario.reset();
        formulario.clearErrors();
        qrGeneradoUrl.value = "";
        mensajeQr.value = "";
        tipoMensajeQr.value = "info";

        formulario.oferta_academica_id = ofertaId;
        formulario.tipo_pago = "matricula";
        formulario.metodo_pago = "";
    },
    {
        immediate: true,
    },
);

const formatearPrecio = (valor) => {
    const monto = Number(valor ?? 0);

    return `Bs ${monto.toLocaleString("es-BO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};

const cerrarModal = () => {
    if (formulario.processing) {
        return;
    }

    formulario.clearErrors();
    qrGeneradoUrl.value = "";
    mensajeQr.value = "";
    tipoMensajeQr.value = "info";

    emit("cerrar");
};

const limpiarEstadoQr = () => {
    qrGeneradoUrl.value = "";
    mensajeQr.value = "";
    tipoMensajeQr.value = "info";
};

const manejarExitoInscripcion = (pagina) => {
    const pagoGenerado = pagina?.props?.flash?.pago_generado;
    const errorFlash = pagina?.props?.flash?.error;

    if (formulario.metodo_pago === "QR") {
        if (pagoGenerado?.qr_url) {
            qrGeneradoUrl.value = pagoGenerado.qr_url;
            mensajeQr.value =
                "La inscripción fue registrada y el QR quedó listo para el pago.";
            tipoMensajeQr.value = "success";

            return;
        }

        if (errorFlash) {
            qrGeneradoUrl.value = "";
            mensajeQr.value = errorFlash;
            tipoMensajeQr.value = "error";

            return;
        }

        mensajeQr.value =
            "La inscripción fue registrada, pero no se pudo mostrar el QR.";
        tipoMensajeQr.value = "error";

        return;
    }

    formulario.reset();
    limpiarEstadoQr();
    emit("cerrar");
};

const enviarInscripcion = () => {
    formulario.post("/alumno/inscripciones", {
        preserveScroll: true,

        onSuccess: (pagina) => {
            manejarExitoInscripcion(pagina);
        },

        onError: () => {
            limpiarEstadoQr();
        },
    });
};

const generarQr = () => {
    if (formulario.processing || formulario.metodo_pago !== "QR") {
        return;
    }

    limpiarEstadoQr();
    enviarInscripcion();
};

const puedeEnviar = computed(() => {
    if (!formulario.metodo_pago) {
        return false;
    }

    return true;
});

const cerrarConEscape = (evento) => {
    if (evento.key === "Escape" && props.mostrar) {
        cerrarModal();
    }
};

onMounted(() => {
    document.addEventListener("keydown", cerrarConEscape);
});

onBeforeUnmount(() => {
    document.removeEventListener("keydown", cerrarConEscape);
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
                v-if="mostrar && oferta"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
                @click.self="cerrarModal"
            >
                <div
                    class="max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-3xl bg-white shadow-2xl"
                >
                    <!-- Encabezado -->
                    <div
                        class="bg-gradient-to-r from-blue-700 to-indigo-700 px-6 py-5 text-white"
                    >
                        <div class="flex items-start justify-between gap-5">
                            <div>
                                <p
                                    class="text-xs font-black uppercase tracking-[0.2em] text-blue-200"
                                >
                                    Solicitud de inscripción
                                </p>

                                <h2 class="mt-2 text-2xl font-black">
                                    {{ nombreCarrera }}
                                </h2>

                                <p class="mt-2 text-sm text-blue-100">
                                    {{ nombreOferta }}
                                </p>
                            </div>

                            <button
                                type="button"
                                aria-label="Cerrar modal"
                                class="flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-full bg-white/10 text-xl font-bold transition hover:bg-white/20 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="formulario.processing"
                                @click="cerrarModal"
                            >
                                ×
                            </button>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form class="p-6" @submit.prevent="enviarInscripcion">
                        <!-- Información -->
                        <div
                            class="rounded-2xl border border-blue-200 bg-blue-50 p-4"
                        >
                            <div class="flex gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 font-black text-white"
                                >
                                    i
                                </div>

                                <div>
                                    <p class="font-black text-slate-900">
                                        Confirma tu inscripción
                                    </p>

                                    <p
                                        class="mt-1 text-sm leading-6 text-slate-600"
                                    >
                                        Selecciona qué deseas pagar y el método
                                        que utilizarás. Al confirmar, se
                                        registrará tu solicitud de inscripción.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Error relacionado con la oferta -->
                        <div
                            v-if="formulario.errors.oferta_academica_id"
                            class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600"
                        >
                            {{ formulario.errors.oferta_academica_id }}
                        </div>

                        <!-- Tipo de pago -->
                        <fieldset class="mt-6">
                            <legend class="text-sm font-black text-slate-900">
                                ¿Qué desea pagar?
                            </legend>

                            <p class="mt-1 text-xs text-slate-500">
                                Selecciona la matrícula o la carrera completa.
                            </p>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <!-- Matrícula -->
                                <label
                                    class="cursor-pointer rounded-2xl border p-4 transition"
                                    :class="
                                        formulario.tipo_pago === 'matricula'
                                            ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100'
                                            : 'border-slate-200 bg-white hover:border-blue-200'
                                    "
                                >
                                    <input
                                        v-model="formulario.tipo_pago"
                                        type="radio"
                                        value="matricula"
                                        class="sr-only"
                                    />

                                    <div
                                        class="flex items-start justify-between gap-3"
                                    >
                                        <div>
                                            <p
                                                class="font-black text-slate-900"
                                            >
                                                Matrícula
                                            </p>

                                            <p
                                                class="mt-1 text-xs leading-5 text-slate-500"
                                            >
                                                Pago inicial para confirmar la
                                                inscripción.
                                            </p>
                                        </div>

                                        <span
                                            class="whitespace-nowrap text-sm font-black text-blue-700"
                                        >
                                            {{
                                                formatearPrecio(
                                                    oferta.precio_matricula,
                                                )
                                            }}
                                        </span>
                                    </div>
                                </label>

                                <!-- Carrera completa -->
                                <label
                                    class="cursor-pointer rounded-2xl border p-4 transition"
                                    :class="
                                        formulario.tipo_pago ===
                                        'carrera_completa'
                                            ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100'
                                            : 'border-slate-200 bg-white hover:border-blue-200'
                                    "
                                >
                                    <input
                                        v-model="formulario.tipo_pago"
                                        type="radio"
                                        value="carrera_completa"
                                        class="sr-only"
                                    />

                                    <div
                                        class="flex items-start justify-between gap-3"
                                    >
                                        <div>
                                            <p
                                                class="font-black text-slate-900"
                                            >
                                                Carrera completa
                                            </p>

                                            <p
                                                class="mt-1 text-xs leading-5 text-slate-500"
                                            >
                                                Pago del precio total
                                                configurado para la carrera.
                                            </p>
                                        </div>

                                        <span
                                            class="whitespace-nowrap text-sm font-black text-blue-700"
                                        >
                                            {{
                                                formatearPrecio(
                                                    oferta.precio_carrera_completa,
                                                )
                                            }}
                                        </span>
                                    </div>
                                </label>
                            </div>

                            <p
                                v-if="formulario.errors.tipo_pago"
                                class="mt-2 text-sm font-semibold text-red-600"
                            >
                                {{ formulario.errors.tipo_pago }}
                            </p>
                        </fieldset>

                        <!-- Método de pago -->
                        <fieldset class="mt-7">
                            <legend class="text-sm font-black text-slate-900">
                                Método de pago
                            </legend>

                            <p class="mt-1 text-xs text-slate-500">
                                Selecciona cómo deseas realizar el pago.
                            </p>

                            <div class="mt-3 space-y-3">
                                <!-- QR -->
                                <label
                                    class="flex cursor-pointer items-center gap-4 rounded-2xl border p-4 transition"
                                    :class="
                                        formulario.metodo_pago === 'QR'
                                            ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100'
                                            : 'border-slate-200 bg-white hover:border-blue-200'
                                    "
                                >
                                    <input
                                        v-model="formulario.metodo_pago"
                                        type="radio"
                                        value="QR"
                                        class="h-4 w-4 accent-blue-600"
                                    />

                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-sm font-black text-white"
                                    >
                                        QR
                                    </div>

                                    <div>
                                        <p class="font-black text-slate-900">
                                            Pago mediante QR
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Se generará un código QR para
                                            realizar el pago con PagoFácil.
                                        </p>
                                    </div>
                                </label>

                                <div
                                    v-if="formulario.metodo_pago === 'QR'"
                                    class="rounded-2xl border border-dashed border-blue-200 bg-blue-50 p-4"
                                >
                                    <div
                                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                                    >
                                        <div>
                                            <p
                                                class="text-sm font-black text-blue-900"
                                            >
                                                Listo para generar QR
                                            </p>

                                            <p
                                                class="mt-1 text-xs text-blue-700"
                                            >
                                                Presiona el botón principal al
                                                final para registrar la
                                                inscripción y obtener el QR de
                                                PagoFácil.
                                            </p>
                                        </div>

                                        <span
                                            class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-black text-blue-700"
                                        >
                                            QR habilitado
                                        </span>
                                    </div>
                                </div>

                                <!-- Tarjeta -->
                                <label
                                    class="flex cursor-pointer items-center gap-4 rounded-2xl border p-4 transition"
                                    :class="
                                        formulario.metodo_pago === 'Tarjeta'
                                            ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100'
                                            : 'border-slate-200 hover:border-blue-200'
                                    "
                                >
                                    <input
                                        v-model="formulario.metodo_pago"
                                        type="radio"
                                        value="Tarjeta"
                                        class="h-4 w-4 accent-blue-600"
                                    />

                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-700"
                                    >
                                        <svg
                                            class="h-6 w-6"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <rect
                                                x="2"
                                                y="5"
                                                width="20"
                                                height="14"
                                                rx="2"
                                            />

                                            <path d="M2 10h20" />
                                        </svg>
                                    </div>

                                    <div>
                                        <p class="font-black text-slate-900">
                                            Tarjeta de débito o crédito
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            El pago será procesado mediante una
                                            pasarela de tarjetas.
                                        </p>
                                    </div>
                                </label>

                                <!-- PayPal -->
                                <label
                                    class="flex cursor-pointer items-center gap-4 rounded-2xl border p-4 transition"
                                    :class="
                                        formulario.metodo_pago === 'PayPal'
                                            ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100'
                                            : 'border-slate-200 hover:border-blue-200'
                                    "
                                >
                                    <input
                                        v-model="formulario.metodo_pago"
                                        type="radio"
                                        value="PayPal"
                                        class="h-4 w-4 accent-blue-600"
                                    />

                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-100 text-lg font-black text-cyan-700"
                                    >
                                        P
                                    </div>

                                    <div>
                                        <p class="font-black text-slate-900">
                                            PayPal
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            El pago será procesado mediante una
                                            cuenta de PayPal.
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <p
                                v-if="formulario.errors.metodo_pago"
                                class="mt-2 text-sm font-semibold text-red-600"
                            >
                                {{ formulario.errors.metodo_pago }}
                            </p>
                        </fieldset>

                        <div
                            v-if="mensajeQr || qrGeneradoUrl"
                            class="mt-7 rounded-2xl p-5"
                            :class="
                                tipoMensajeQr === 'success'
                                    ? 'border border-emerald-200 bg-emerald-50'
                                    : 'border border-red-200 bg-red-50'
                            "
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p
                                        class="text-sm font-black"
                                        :class="
                                            tipoMensajeQr === 'success'
                                                ? 'text-emerald-900'
                                                : 'text-red-900'
                                        "
                                    >
                                        {{
                                            tipoMensajeQr === "success"
                                                ? "QR de PagoFácil generado"
                                                : "No se pudo generar el QR"
                                        }}
                                    </p>

                                    <p
                                        class="mt-1 text-xs"
                                        :class="
                                            tipoMensajeQr === 'success'
                                                ? 'text-emerald-700'
                                                : 'text-red-700'
                                        "
                                    >
                                        {{ mensajeQr }}
                                    </p>
                                </div>

                                <span
                                    class="rounded-full px-3 py-1 text-xs font-black"
                                    :class="
                                        tipoMensajeQr === 'success'
                                            ? 'bg-emerald-100 text-emerald-800'
                                            : 'bg-red-100 text-red-800'
                                    "
                                >
                                    {{
                                        tipoMensajeQr === "success"
                                            ? "Listo"
                                            : "Atención"
                                    }}
                                </span>
                            </div>

                            <div
                                v-if="qrGeneradoUrl"
                                class="mt-4 flex justify-center rounded-2xl bg-white p-4 shadow-sm"
                            >
                                <img
                                    :src="qrGeneradoUrl"
                                    alt="QR de PagoFácil"
                                    class="max-h-72 w-full max-w-sm object-contain"
                                />
                            </div>
                        </div>

                        <!-- Resumen -->
                        <div
                            class="mt-7 rounded-2xl bg-slate-900 p-5 text-white"
                        >
                            <div
                                class="flex items-center justify-between gap-4"
                            >
                                <div>
                                    <p
                                        class="text-xs font-bold uppercase tracking-widest text-slate-400"
                                    >
                                        Total seleccionado
                                    </p>

                                    <p class="mt-1 text-sm text-slate-300">
                                        {{
                                            formulario.tipo_pago === "matricula"
                                                ? "Pago de matrícula"
                                                : "Pago de carrera completa"
                                        }}
                                    </p>
                                </div>

                                <p class="text-2xl font-black text-white">
                                    {{ formatearPrecio(montoSeleccionado) }}
                                </p>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div
                            class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                        >
                            <button
                                type="button"
                                class="cursor-pointer rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="formulario.processing"
                                @click="cerrarModal"
                            >
                                Cancelar
                            </button>

                            <button
                                v-if="!qrGeneradoUrl"
                                type="submit"
                                class="cursor-pointer rounded-xl bg-blue-600 px-6 py-3 text-sm font-black text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="
                                    !puedeEnviar || formulario.processing
                                "
                            >
                                <span v-if="formulario.processing">
                                    Procesando...
                                </span>

                                <span v-else>
                                    {{
                                        formulario.metodo_pago === "QR"
                                            ? "Generar QR de PagoFácil"
                                            : "Confirmar inscripción"
                                    }}
                                </span>
                            </button>

                            <button
                                v-else
                                type="button"
                                class="cursor-pointer rounded-xl bg-blue-600 px-6 py-3 text-sm font-black text-white transition hover:bg-blue-700"
                                @click="cerrarModal"
                            >
                                Cerrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
