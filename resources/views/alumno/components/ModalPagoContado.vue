<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";

import { useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
    mostrar: {
        type: Boolean,
        default: false,
    },

    inscripciones: {
        type: Array,
        default: () => [],
    },

    conceptos: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["cerrar", "qr-generado"]);

const page = usePage();

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") ?? "";

const formulario = useForm({
    inscripcion_id: null,
    concepto_pago_id: null,
    monto_pagado: "",
    fecha_pago: new Date().toISOString().slice(0, 10),
    metodo_pago: "Efectivo",
    correo_solicitante: "",
    observacion: "",
});

const mensajeEstado = ref("");
const tipoEstado = ref("info");
const procesandoQr = ref(false);

const estaEnQr = computed(() => formulario.metodo_pago === "QR");
const ocupado = computed(() => formulario.processing || procesandoQr.value);
const tituloAccion = computed(() =>
    estaEnQr.value ? "Generar QR PagoFácil" : "Registrar pago contado",
);

const inscripcionSeleccionada = computed(() => {
    return (
        props.inscripciones.find(
            (inscripcion) =>
                Number(inscripcion.id) === Number(formulario.inscripcion_id),
        ) ?? null
    );
});

const montoMensualidad = computed(() => {
    return Number(inscripcionSeleccionada.value?.precio_mensualidad ?? 0);
});

const montoFormateado = computed(() => {
    if (!montoMensualidad.value) {
        return "";
    }

    return `Bs ${montoMensualidad.value.toLocaleString("es-BO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
});

const tieneMensualidad = computed(() => montoMensualidad.value > 0);

const prepararFormulario = () => {
    formulario.reset();
    formulario.clearErrors();

    formulario.inscripcion_id = props.inscripciones[0]?.id ?? null;
    formulario.concepto_pago_id = props.conceptos[0]?.id ?? null;
    formulario.monto_pagado = "";
    formulario.fecha_pago = new Date().toISOString().slice(0, 10);
    formulario.metodo_pago = "Efectivo";
    formulario.correo_solicitante = page.props.auth?.user?.email ?? "";
    formulario.observacion = "";

    mensajeEstado.value = "";
    tipoEstado.value = "info";
};

watch(
    () => props.mostrar,
    (mostrar) => {
        if (mostrar) {
            prepararFormulario();
        }
    },
    { immediate: true },
);

watch(
    () => [formulario.inscripcion_id, props.inscripciones],
    () => {
        formulario.monto_pagado = tieneMensualidad.value
            ? montoMensualidad.value.toFixed(2)
            : "";
    },
    { deep: true },
);

const cerrarModal = () => {
    if (ocupado.value) {
        return;
    }

    mensajeEstado.value = "";
    tipoEstado.value = "info";
    formulario.clearErrors();

    emit("cerrar");
};

const registrarPagoNormal = () => {
    if (!tieneMensualidad.value) {
        tipoEstado.value = "error";
        mensajeEstado.value =
            "La inscripción seleccionada no tiene precio de mensualidad configurado.";

        return;
    }

    formulario.monto_pagado = montoMensualidad.value.toFixed(2);

    formulario.post("/alumno/mis-pagos", {
        preserveScroll: true,

        onSuccess: () => {
            cerrarModal();
        },

        onError: () => {
            tipoEstado.value = "error";
            mensajeEstado.value =
                "Revisa los datos ingresados y vuelve a intentar.";
        },
    });
};

const generarQr = async () => {
    if (ocupado.value || !estaEnQr.value) {
        return;
    }

    if (!tieneMensualidad.value) {
        tipoEstado.value = "error";
        mensajeEstado.value =
            "La inscripción seleccionada no tiene precio de mensualidad configurado.";

        return;
    }

    procesandoQr.value = true;
    mensajeEstado.value = "";
    tipoEstado.value = "info";

    formulario.monto_pagado = montoMensualidad.value.toFixed(2);

    try {
        const formData = new FormData();

        formData.append(
            "inscripcion_id",
            String(formulario.inscripcion_id ?? ""),
        );
        formData.append(
            "concepto_pago_id",
            String(formulario.concepto_pago_id ?? ""),
        );
        formData.append("monto_pagado", montoMensualidad.value.toFixed(2));
        formData.append("fecha_pago", String(formulario.fecha_pago ?? ""));
        formData.append("metodo_pago", String(formulario.metodo_pago ?? ""));
        formData.append(
            "correo_solicitante",
            String(formulario.correo_solicitante ?? ""),
        );
        formData.append("observacion", String(formulario.observacion ?? ""));
        formData.append("accion", "generar_qr");
        formData.append("_token", csrfToken);

        const response = await fetch("/alumno/mis-pagos/generar-qr", {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        });

        const respuestaTexto = await response.text();

        let data = null;

        try {
            data = respuestaTexto ? JSON.parse(respuestaTexto) : null;
        } catch {
            throw new Error(
                `La respuesta del servidor no fue JSON válido: ${respuestaTexto.slice(0, 180)}`,
            );
        }

        if (!response.ok || !data.ok) {
            throw new Error(data.message || "No se pudo generar el QR.");
        }

        emit("qr-generado", data.pago);
        emit("cerrar");
    } catch (error) {
        tipoEstado.value = "error";
        mensajeEstado.value = error?.message ?? "No se pudo generar el QR.";
    } finally {
        procesandoQr.value = false;
    }
};

const manejarEnvio = () => {
    if (estaEnQr.value) {
        generarQr();

        return;
    }

    registrarPagoNormal();
};

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
                v-if="mostrar"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
                @click.self="cerrarModal"
            >
                <div
                    class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-3xl bg-white shadow-2xl"
                >
                    <div class="border-b border-slate-200 px-6 py-5">
                        <div class="flex items-start justify-between gap-5">
                            <div>
                                <p
                                    class="text-xs font-black uppercase tracking-[0.2em] text-blue-500"
                                >
                                    Registrar pago contado
                                </p>

                                <h2
                                    class="mt-2 text-2xl font-black text-slate-900"
                                >
                                    Nuevo pago
                                </h2>

                                <p class="mt-2 text-sm text-slate-500">
                                    Completa los datos del pago. Si seleccionas
                                    QR, el botón cambiará para generar el código
                                    de PagoFácil.
                                </p>
                            </div>

                            <button
                                type="button"
                                aria-label="Cerrar modal"
                                class="flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-xl font-bold text-slate-600 transition hover:bg-slate-200 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="ocupado"
                                @click="cerrarModal"
                            >
                                ×
                            </button>
                        </div>
                    </div>

                    <form class="p-6" @submit.prevent="manejarEnvio">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Inscripción
                                </label>

                                <select
                                    v-model="formulario.inscripcion_id"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                >
                                    <option :value="null">
                                        Seleccione una inscripción
                                    </option>
                                    <option
                                        v-for="inscripcion in inscripciones"
                                        :key="inscripcion.id"
                                        :value="inscripcion.id"
                                    >
                                        {{
                                            inscripcion.carrera_codigo ??
                                            "SIN-COD"
                                        }}
                                        -
                                        {{
                                            inscripcion.carrera ?? "Sin carrera"
                                        }}
                                        /
                                        {{
                                            inscripcion.periodo ?? "Sin periodo"
                                        }}
                                        {{ inscripcion.gestion ?? "" }}
                                    </option>
                                </select>

                                <p
                                    v-if="formulario.errors.inscripcion_id"
                                    class="mt-2 text-xs font-semibold text-red-600"
                                >
                                    {{ formulario.errors.inscripcion_id }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Concepto de pago
                                </label>

                                <select
                                    v-model="formulario.concepto_pago_id"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                >
                                    <option :value="null">
                                        Seleccione un concepto
                                    </option>
                                    <option
                                        v-for="concepto in conceptos"
                                        :key="concepto.id"
                                        :value="concepto.id"
                                    >
                                        {{ concepto.nombre }}
                                    </option>
                                </select>

                                <p
                                    v-if="formulario.errors.concepto_pago_id"
                                    class="mt-2 text-xs font-semibold text-red-600"
                                >
                                    {{ formulario.errors.concepto_pago_id }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Mensualidad
                                </label>

                                <input
                                    :value="
                                        montoFormateado ||
                                        'Seleccione una inscripción'
                                    "
                                    type="text"
                                    readonly
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    placeholder="Ej. 120.00"
                                />

                                <p class="mt-2 text-xs text-slate-500">
                                    El sistema toma este monto desde la oferta
                                    académica de la inscripción seleccionada.
                                </p>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Fecha de pago
                                </label>

                                <input
                                    v-model="formulario.fecha_pago"
                                    type="date"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                />

                                <p
                                    v-if="formulario.errors.fecha_pago"
                                    class="mt-2 text-xs font-semibold text-red-600"
                                >
                                    {{ formulario.errors.fecha_pago }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Método de pago
                                </label>

                                <select
                                    v-model="formulario.metodo_pago"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                >
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Transferencia">
                                        Transferencia
                                    </option>
                                    <option value="QR">QR</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Correo solicitante
                                </label>

                                <input
                                    v-model="formulario.correo_solicitante"
                                    type="email"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    placeholder="correo@ejemplo.com"
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Observación
                                </label>

                                <textarea
                                    v-model="formulario.observacion"
                                    rows="3"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    placeholder="Observaciones del pago"
                                ></textarea>
                            </div>
                        </div>

                        <div
                            v-if="mensajeEstado"
                            class="mt-5 rounded-2xl px-4 py-3 text-sm font-semibold"
                            :class="
                                tipoEstado === 'error'
                                    ? 'border border-red-100 bg-red-50 text-red-700'
                                    : 'border border-blue-100 bg-blue-50 text-blue-700'
                            "
                        >
                            {{ mensajeEstado }}
                        </div>

                        <div
                            class="mt-6 flex flex-wrap gap-3 border-t border-slate-200 pt-5"
                        >
                            <button
                                v-if="!estaEnQr"
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="ocupado || !tieneMensualidad"
                            >
                                <span v-if="formulario.processing"
                                    >Procesando...</span
                                >
                                <span v-else>{{ tituloAccion }}</span>
                            </button>

                            <button
                                v-else
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="ocupado || !tieneMensualidad"
                            >
                                <span v-if="procesandoQr">Generando QR...</span>
                                <span v-else>{{ tituloAccion }}</span>
                            </button>

                            <button
                                type="button"
                                class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                                @click="cerrarModal"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
