<template>
    <form @submit.prevent="submit" class="mt-8 space-y-6">
        <div
            v-if="errores.length"
            class="rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700"
        >
            <p class="font-bold">Revisa los siguientes errores:</p>
            <ul class="mt-2 list-inside list-disc">
                <li
                    v-for="(error, index) in errores"
                    :key="`${error}-${index}`"
                >
                    {{ error }}
                </li>
            </ul>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <h3 class="text-lg font-black text-slate-900">
                    Datos del pago al contado
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Registra un pago manual o genera un QR con PagoFácil si el
                    método seleccionado es QR.
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Inscripción</label
                >
                <select
                    v-model="form.inscripcion_id"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option :value="null">Seleccione una inscripción</option>
                    <option
                        v-for="inscripcion in inscripciones"
                        :key="inscripcion.id"
                        :value="inscripcion.id"
                    >
                        {{ inscripcionLabel(inscripcion) }}
                    </option>
                </select>
                <p
                    v-if="form.errors.inscripcion_id"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.inscripcion_id }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Concepto de pago</label
                >
                <select
                    v-model="form.concepto_pago_id"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option :value="null">Seleccione un concepto</option>
                    <option
                        v-for="concepto in conceptos"
                        :key="concepto.id"
                        :value="concepto.id"
                    >
                        {{ concepto.nombre }}
                    </option>
                </select>
                <p
                    v-if="form.errors.concepto_pago_id"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.concepto_pago_id }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Monto pagado</label
                >
                <input
                    v-model="form.monto_pagado"
                    type="number"
                    step="0.01"
                    min="0.01"
                    required
                    placeholder="Ejemplo: 100.00"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.monto_pagado"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.monto_pagado }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Fecha de pago</label
                >
                <input
                    v-model="form.fecha_pago"
                    type="date"
                    required
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.fecha_pago"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.fecha_pago }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Método de pago</label
                >
                <select
                    v-model="form.metodo_pago"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option
                        v-for="metodo in metodosPago"
                        :key="metodo"
                        :value="metodo"
                    >
                        {{ metodo }}
                    </option>
                </select>
                <p
                    v-if="form.errors.metodo_pago"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.metodo_pago }}
                </p>
                <p
                    v-if="form.metodo_pago === 'QR'"
                    class="mt-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100"
                >
                    Para pruebas, el QR de PagoFácil se generará por Bs 0.01
                    aunque el monto real del pago sea diferente.
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Estado</label
                >
                <select
                    v-model="form.estado"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option
                        v-for="estado in estadosPago"
                        :key="estado"
                        :value="estado"
                    >
                        {{ capitalizar(estado) }}
                    </option>
                </select>
                <p v-if="form.errors.estado" class="mt-2 text-sm text-red-600">
                    {{ form.errors.estado }}
                </p>
                <p class="mt-2 text-xs text-slate-500">
                    Si el método es QR, el estado se guardará como Pendiente
                    hasta confirmar el pago.
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Correo solicitante</label
                >
                <input
                    v-model="form.correo_solicitante"
                    type="email"
                    placeholder="cliente@correo.com"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.correo_solicitante"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.correo_solicitante }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Código transacción</label
                >
                <input
                    v-model="form.codigo_transaccion"
                    type="text"
                    placeholder="Opcional"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.codigo_transaccion"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.codigo_transaccion }}
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Observación</label
                >
                <textarea
                    v-model="form.observacion"
                    rows="4"
                    placeholder="Observaciones del pago"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                ></textarea>
                <p
                    v-if="form.errors.observacion"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.observacion }}
                </p>
            </div>

            <div
                v-if="form.metodo_pago === 'QR'"
                class="md:col-span-2 rounded-3xl border border-emerald-200 bg-emerald-50 p-4"
            >
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p
                            class="text-sm font-black uppercase tracking-[0.2em] text-emerald-700"
                        >
                            PagoFácil
                        </p>
                        <p class="mt-1 text-xs text-emerald-700">
                            La confirmación se valida con el callback real del
                            servicio.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="generarQrPagoFacil"
                        :disabled="procesandoQr"
                        class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:-translate-y-0.5 hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <i class="fa-solid fa-qrcode text-xs"></i>
                        {{ procesandoQr ? "Generando..." : "Generar QR" }}
                    </button>
                </div>

                <div
                    v-if="qrUrl"
                    class="mt-4 rounded-2xl border border-white/60 bg-white p-4 text-center"
                >
                    <img
                        :src="qrUrl"
                        alt="QR PagoFácil"
                        class="mx-auto h-52 w-52 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm"
                    />

                    <p class="mt-3 text-xs font-bold uppercase text-slate-500">
                        Payment Number
                    </p>
                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ paymentNumber || "No disponible" }}
                    </p>

                    <div
                        class="mt-4 rounded-2xl border px-3 py-2 text-left text-xs font-medium"
                        :class="estadoBoxClasses"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-bold uppercase tracking-[0.2em]">
                                Estado
                            </span>
                            <span>{{ estadoPago }}</span>
                        </div>
                        <p class="mt-2 whitespace-pre-line">
                            {{ mensajeEstado }}
                        </p>
                    </div>

                    <div class="mt-4 flex flex-wrap justify-center gap-3">
                        <button
                            type="button"
                            @click="verificarManual"
                            :disabled="verificando"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <i class="fa-solid fa-rotate text-[10px]"></i>
                            {{
                                verificando
                                    ? "Verificando..."
                                    : "Verificar ahora"
                            }}
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2"
            >
                <button
                    v-if="form.metodo_pago !== 'QR'"
                    type="button"
                    @click="submit('guardar')"
                    class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                >
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Guardar pago
                </button>

                <button
                    v-else
                    type="button"
                    @click="submit('guardar')"
                    class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                >
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Guardar pago
                </button>

                <Link
                    :href="cancelUrl"
                    class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    Cancelar
                </Link>
            </div>
        </div>
    </form>
</template>

<script setup>
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import { computed, onBeforeUnmount, ref, watch } from "vue";

const props = defineProps({
    pago: { type: Object, default: () => ({}) },
    inscripciones: { type: Array, default: () => [] },
    conceptos: { type: Array, default: () => [] },
    submitUrl: { type: String, default: "" },
    cancelUrl: { type: String, default: "" },
    method: { type: String, default: "post" },
});

const page = usePage();
const metodosPago = ["Efectivo", "Transferencia", "QR"];
const estadosPago = ["Pendiente", "Confirmado", "Anulado", "Fallido"];
const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") ?? "";

const qrUrl = ref("");
const paymentNumber = ref("");
const estadoUrl = ref("");
const consultarUrl = ref("");
const estadoPago = ref("Pendiente");
const mensajeEstado = ref(
    "El sistema está verificando el pago con el callback real de PagoFácil.",
);
const tipoEstado = ref("info");
const verificando = ref(false);
const procesandoQr = ref(false);
const intervaloVerificacion = ref(null);
const confirmacionEmitida = ref(false);

const estadoBoxClasses = computed(() => {
    if (confirmacionEmitida.value || estadoPago.value === "Confirmado") {
        return "border-emerald-200 bg-emerald-50 text-emerald-700";
    }

    if (tipoEstado.value === "error") {
        return "border-red-200 bg-red-50 text-red-700";
    }

    return "border-slate-200 bg-slate-50 text-slate-700";
});

const form = useForm({
    inscripcion_id: props.pago?.inscripcion_id ?? null,
    concepto_pago_id: props.pago?.concepto_pago_id ?? null,
    monto_pagado: props.pago?.monto_pagado ?? "",
    fecha_pago: props.pago?.fecha_pago ?? "",
    metodo_pago: props.pago?.metodo_pago ?? "Efectivo",
    estado: props.pago?.estado ?? "Pendiente",
    codigo_transaccion: props.pago?.codigo_transaccion ?? "",
    correo_solicitante: props.pago?.correo_solicitante ?? "",
    observacion: props.pago?.observacion ?? "",
    accion: "guardar",
});

const errores = computed(() =>
    Object.values(page.props.errors ?? {})
        .flat()
        .filter(Boolean),
);

watch(
    () => props.pago,
    (value) => {
        const next = value ?? {};
        form.inscripcion_id = next.inscripcion_id ?? null;
        form.concepto_pago_id = next.concepto_pago_id ?? null;
        form.monto_pagado = next.monto_pagado ?? "";
        form.fecha_pago = next.fecha_pago ?? "";
        form.metodo_pago = next.metodo_pago ?? "Efectivo";
        form.estado = next.estado ?? "Pendiente";
        form.codigo_transaccion = next.codigo_transaccion ?? "";
        form.correo_solicitante = next.correo_solicitante ?? "";
        form.observacion = next.observacion ?? "";
    },
    { immediate: true, deep: true },
);

watch(
    () => form.metodo_pago,
    (valor) => {
        if (valor === "QR") {
            form.estado = "Pendiente";
            return;
        }

        limpiarIntervalo();
    },
);

function limpiarIntervalo() {
    if (intervaloVerificacion.value) {
        clearInterval(intervaloVerificacion.value);
        intervaloVerificacion.value = null;
    }
}

function iniciarAutoVerificacion() {
    limpiarIntervalo();

    intervaloVerificacion.value = setInterval(() => {
        verificarEstado(false);
    }, 5000);
}

function esEstadoConfirmado(estado) {
    const valor = String(estado ?? "")
        .toLowerCase()
        .trim();
    return (
        valor === "confirmado" ||
        valor === "pagado" ||
        valor === "2" ||
        valor === "true"
    );
}

function confirmarEstado() {
    if (confirmacionEmitida.value) {
        return;
    }

    confirmacionEmitida.value = true;
    limpiarIntervalo();
    estadoPago.value = "Confirmado";
    form.estado = "Confirmado";
    tipoEstado.value = "success";
    mensajeEstado.value =
        "Pago confirmado correctamente por el callback real de PagoFácil.";

    router.visit(route("admin.pago-contados.index"));
}

async function verificarEstado(mostrarCarga = true) {
    if (!estadoUrl.value || confirmacionEmitida.value) {
        return;
    }

    if (mostrarCarga) {
        verificando.value = true;
    }

    try {
        const response = await fetch(estadoUrl.value, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        });

        const texto = await response.text();
        const data = texto ? JSON.parse(texto) : null;

        if (!response.ok || !data?.ok) {
            return;
        }

        const estado =
            data.pago?.estado ?? data.cuota?.estado ?? estadoPago.value;
        estadoPago.value = estado;

        if (esEstadoConfirmado(estado)) {
            confirmarEstado();
            return;
        }

        tipoEstado.value = "info";
        mensajeEstado.value =
            "Aún no aparece la confirmación automática del callback. El sistema sigue verificando en segundo plano.";
    } catch (error) {
        tipoEstado.value = "error";
        mensajeEstado.value =
            error?.message ?? "No se pudo verificar el estado automáticamente.";
    } finally {
        verificando.value = false;
    }
}

async function verificarManual() {
    if (!consultarUrl.value || confirmacionEmitida.value) {
        return;
    }

    verificando.value = true;
    tipoEstado.value = "info";
    mensajeEstado.value = "Consultando PagoFácil manualmente...";

    try {
        const formData = new FormData();
        formData.append("_token", csrfToken);

        const response = await fetch(consultarUrl.value, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        });

        const texto = await response.text();
        const data = texto ? JSON.parse(texto) : null;

        if (!response.ok || !data?.ok) {
            throw new Error(data?.message || "No se pudo consultar el pago.");
        }

        const estado =
            data.pago?.estado ?? data.cuota?.estado ?? estadoPago.value;
        estadoPago.value = estado;

        if (esEstadoConfirmado(estado)) {
            confirmarEstado();
            return;
        }

        tipoEstado.value = "info";
        mensajeEstado.value =
            "El pago todavía no está confirmado. El callback automático seguirá intentando detectar el cambio.";
    } catch (error) {
        tipoEstado.value = "error";
        mensajeEstado.value =
            error?.message ?? "No se pudo consultar manualmente el pago.";
    } finally {
        verificando.value = false;
    }
}

async function generarQrPagoFacil() {
    if (form.metodo_pago !== "QR") {
        return;
    }

    procesandoQr.value = true;
    tipoEstado.value = "info";
    mensajeEstado.value =
        "Generando QR con PagoFácil y esperando la confirmación por callback.";

    try {
        const formData = new FormData();

        formData.append("inscripcion_id", String(form.inscripcion_id ?? ""));
        formData.append(
            "concepto_pago_id",
            String(form.concepto_pago_id ?? ""),
        );
        formData.append("monto_pagado", String(form.monto_pagado ?? ""));
        formData.append("fecha_pago", String(form.fecha_pago ?? ""));
        formData.append("metodo_pago", String(form.metodo_pago ?? ""));
        formData.append(
            "correo_solicitante",
            String(form.correo_solicitante ?? ""),
        );
        formData.append("observacion", String(form.observacion ?? ""));
        formData.append("estado", "Pendiente");
        formData.append("accion", "generar_qr");
        formData.append("_token", csrfToken);

        const response = await fetch(props.submitUrl, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        });

        const texto = await response.text();
        const data = texto ? JSON.parse(texto) : null;

        if (!response.ok || !data?.ok) {
            throw new Error(data?.message || "No se pudo generar el QR.");
        }

        pagoGenerado(data.pago);
        iniciarAutoVerificacion();
    } catch (error) {
        tipoEstado.value = "error";
        mensajeEstado.value = error?.message ?? "No se pudo generar el QR.";
    } finally {
        procesandoQr.value = false;
    }
}

function pagoGenerado(pago) {
    qrUrl.value = pago?.qr_url ?? "";
    paymentNumber.value = pago?.payment_number ?? "";
    estadoUrl.value = pago?.estado_url ?? "";
    consultarUrl.value = pago?.consultar_url ?? "";
    estadoPago.value = "Pendiente";
    confirmacionEmitida.value = false;
    mensajeEstado.value =
        "QR generado. Esperando la confirmación automática del callback real de PagoFácil.";
}

function submit(accion = "guardar") {
    form.accion = accion;

    if (props.method === "put") {
        form.put(props.submitUrl);
        return;
    }

    form.post(props.submitUrl);
}

onBeforeUnmount(() => {
    limpiarIntervalo();
});

function inscripcionLabel(inscripcion) {
    const alumno =
        inscripcion.alumnoDetalle?.user ??
        inscripcion.alumno_detalle?.user ??
        null;

    const oferta =
        inscripcion.ofertaAcademica?.nombre ??
        inscripcion.oferta_academica?.nombre ??
        null;

    const carrera =
        inscripcion.ofertaAcademica?.carrera?.nombre ??
        inscripcion.oferta_academica?.carrera?.nombre ??
        null;

    const nombre = `${alumno?.nombres ?? alumno?.nombre ?? ""} ${
        alumno?.apellidos ?? ""
    }`.trim();

    const detalle = oferta || carrera || `Inscripción #${inscripcion.id}`;
    return nombre ? `${nombre} - ${detalle}` : detalle;
}

function capitalizar(valor) {
    return (
        String(valor ?? "")
            .charAt(0)
            .toUpperCase() + String(valor ?? "").slice(1)
    );
}
</script>
