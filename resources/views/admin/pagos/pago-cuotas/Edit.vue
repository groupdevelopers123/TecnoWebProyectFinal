<template>
    <Head :title="`Actualizar cuota #${form.id}`" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"
        >
            <div>
                <p
                    class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500"
                >
                    Registro de pago
                </p>
                <h2 class="mt-2 text-2xl font-black text-slate-900">
                    Cuota #{{ form.id }}
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    {{ nombreCompleto(cuota?.alumno) }} •
                    {{ cuota?.concepto?.nombre ?? "Sin concepto" }}
                </p>
            </div>

            <Link
                :href="route('admin.pago-cuotas.index')"
                class="rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
            >
                Volver al listado
            </Link>
        </div>

        <form @submit.prevent="submit" class="mt-8 space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 p-5">
                    <h3 class="text-lg font-black text-slate-900">
                        Datos del pago
                    </h3>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Método de pago</label
                            >
                            <select
                                v-model="form.metodo_pago"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="">Seleccione</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">
                                    Transferencia
                                </option>
                                <option value="QR">QR</option>
                            </select>
                            <p
                                v-if="form.errors.metodo_pago"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.metodo_pago }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Estado de cuota</label
                            >
                            <select
                                v-model="form.estado_cuota"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="pendiente">Pendiente</option>
                                <option value="pagado">Pagado</option>
                                <option value="anulado">Anulado</option>
                                <option value="fallido">Fallido</option>
                            </select>
                            <p
                                v-if="form.errors.estado_cuota"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.estado_cuota }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Fecha de pago</label
                            >
                            <input
                                v-model="form.fecha_pago"
                                type="date"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            />
                            <p
                                v-if="form.errors.fecha_pago"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.fecha_pago }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Correo solicitante</label
                            >
                            <input
                                v-model="form.correo_solicitante"
                                type="email"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            />
                            <p
                                v-if="form.errors.correo_solicitante"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.correo_solicitante }}
                            </p>
                        </div>

                        <div
                            v-if="form.metodo_pago === 'QR'"
                            class="rounded-3xl border border-emerald-200 bg-emerald-50 p-4"
                        >
                            <div
                                class="flex items-center justify-between gap-4"
                            >
                                <div>
                                    <p
                                        class="text-sm font-black uppercase tracking-[0.2em] text-emerald-700"
                                    >
                                        PagoFácil
                                    </p>
                                    <p class="mt-1 text-xs text-emerald-700">
                                        La confirmación se valida con el
                                        callback real del servicio.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="generarQrPagoFacil"
                                    :disabled="procesandoQr"
                                    class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <i
                                        class="fa-solid fa-qrcode text-[10px]"
                                    ></i>
                                    {{
                                        procesandoQr
                                            ? "Generando..."
                                            : "Generar QR"
                                    }}
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

                                <p
                                    class="mt-3 text-xs font-bold uppercase text-slate-500"
                                >
                                    Payment Number
                                </p>
                                <p
                                    class="mt-1 text-sm font-bold text-slate-800"
                                >
                                    {{ paymentNumber || "No disponible" }}
                                </p>

                                <div
                                    class="mt-4 rounded-2xl border px-3 py-2 text-left text-xs font-medium"
                                    :class="estadoBoxClasses"
                                >
                                    <div
                                        class="flex items-center justify-between gap-3"
                                    >
                                        <span
                                            class="font-bold uppercase tracking-[0.2em]"
                                        >
                                            Estado
                                        </span>
                                        <span>{{ estadoPago }}</span>
                                    </div>
                                    <p class="mt-2 whitespace-pre-line">
                                        {{ mensajeEstado }}
                                    </p>
                                </div>

                                <div
                                    class="mt-4 flex flex-wrap justify-center gap-3"
                                >
                                    <button
                                        type="button"
                                        @click="verificarManual"
                                        :disabled="verificando"
                                        class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                                    >
                                        <i
                                            class="fa-solid fa-rotate text-[10px]"
                                        ></i>
                                        {{
                                            verificando
                                                ? "Verificando..."
                                                : "Verificar ahora"
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 p-5">
                    <h3 class="text-lg font-black text-slate-900">
                        Detalle y observación
                    </h3>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Código de transacción</label
                            >
                            <input
                                v-model="form.codigo_transaccion"
                                type="text"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            />
                            <p
                                v-if="form.errors.codigo_transaccion"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.codigo_transaccion }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-bold text-slate-700"
                                >Observación</label
                            >
                            <textarea
                                v-model="form.observacion"
                                rows="6"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            ></textarea>
                            <p
                                v-if="form.errors.observacion"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.observacion }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <Link
                    :href="route('admin.pago-cuotas.index')"
                    class="rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    Cancelar
                </Link>

                <button
                    v-if="form.metodo_pago !== 'QR'"
                    type="submit"
                    class="rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700"
                >
                    Guardar cambios
                </button>

                <button
                    v-else
                    type="button"
                    @click="submit"
                    class="rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700"
                >
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { computed, onBeforeUnmount, ref, watch } from "vue";

const props = defineProps({
    cuota: { type: Object, default: () => ({}) },
});

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") ?? "";

const qrUrl = ref(props.cuota?.qr_url ?? "");
const paymentNumber = ref(props.cuota?.payment_number ?? "");
const estadoUrl = ref(props.cuota?.estado_url ?? "");
const consultarUrl = ref(props.cuota?.consultar_url ?? "");
const estadoPago = ref(props.cuota?.estado_cuota ?? "pendiente");
const mensajeEstado = ref(
    "El sistema está verificando el pago con el callback real de PagoFácil.",
);
const tipoEstado = ref("info");
const verificando = ref(false);
const procesandoQr = ref(false);
const intervaloVerificacion = ref(null);
const confirmacionEmitida = ref(false);

const estadoBoxClasses = computed(() => {
    if (confirmacionEmitida.value || estadoPago.value === "pagado") {
        return "border-emerald-200 bg-emerald-50 text-emerald-700";
    }

    if (tipoEstado.value === "error") {
        return "border-red-200 bg-red-50 text-red-700";
    }

    return "border-slate-200 bg-slate-50 text-slate-700";
});

const form = useForm({
    id: props.cuota?.id ?? null,
    metodo_pago: props.cuota?.metodo_pago ?? "",
    fecha_pago: props.cuota?.fecha_pago ?? "",
    estado_cuota: props.cuota?.estado_cuota ?? "pendiente",
    correo_solicitante: props.cuota?.correo_solicitante ?? "",
    codigo_transaccion: props.cuota?.codigo_transaccion ?? "",
    observacion: props.cuota?.observacion ?? "",
});

watch(
    () => props.cuota,
    (cuota) => {
        form.id = cuota?.id ?? null;
        form.metodo_pago = cuota?.metodo_pago ?? "";
        form.fecha_pago = cuota?.fecha_pago ?? "";
        form.estado_cuota = cuota?.estado_cuota ?? "pendiente";
        form.correo_solicitante = cuota?.correo_solicitante ?? "";
        form.codigo_transaccion = cuota?.codigo_transaccion ?? "";
        form.observacion = cuota?.observacion ?? "";
        qrUrl.value = cuota?.qr_url ?? "";
        paymentNumber.value = cuota?.payment_number ?? "";
        estadoUrl.value = cuota?.estado_url ?? "";
        consultarUrl.value = cuota?.consultar_url ?? "";
        estadoPago.value = cuota?.estado_cuota ?? "pendiente";
    },
    { deep: true },
);

watch(
    () => form.metodo_pago,
    (valor) => {
        if (valor === "QR") {
            form.estado_cuota = "pendiente";
            qrUrl.value = qrUrl.value || props.cuota?.qr_url || "";
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
    return valor === "confirmado" || valor === "pagado" || valor === "2";
}

function confirmarEstado() {
    if (confirmacionEmitida.value) {
        return;
    }

    confirmacionEmitida.value = true;
    limpiarIntervalo();
    estadoPago.value = "pagado";
    form.estado_cuota = "pagado";
    tipoEstado.value = "success";
    mensajeEstado.value =
        "Pago confirmado correctamente por el callback real de PagoFácil.";

    router.visit(route("admin.pago-cuotas.index"));
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
            data.cuota?.estado ?? data.pago?.estado ?? estadoPago.value;
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
            data.cuota?.estado ?? data.pago?.estado ?? estadoPago.value;
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
        for (const [key, value] of Object.entries({
            metodo_pago: form.metodo_pago,
            fecha_pago: form.fecha_pago ?? "",
            estado_cuota: "pendiente",
            correo_solicitante: form.correo_solicitante ?? "",
            codigo_transaccion: form.codigo_transaccion ?? "",
            observacion: form.observacion ?? "",
            accion: "generar_qr",
            _token: csrfToken,
            _method: "PUT",
        })) {
            formData.append(key, String(value));
        }

        const response = await fetch(
            route("admin.pago-cuotas.update", props.cuota?.id),
            {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            },
        );

        const texto = await response.text();
        const data = texto ? JSON.parse(texto) : null;

        if (!response.ok || !data?.ok) {
            throw new Error(data?.message || "No se pudo generar el QR.");
        }

        pagoGenerado(data.cuota);
        iniciarAutoVerificacion();
    } catch (error) {
        tipoEstado.value = "error";
        mensajeEstado.value = error?.message ?? "No se pudo generar el QR.";
    } finally {
        procesandoQr.value = false;
    }
}

function pagoGenerado(cuota) {
    qrUrl.value = cuota?.qr_url ?? qrUrl.value ?? "";
    paymentNumber.value = cuota?.payment_number ?? paymentNumber.value ?? "";
    estadoUrl.value = cuota?.estado_url ?? estadoUrl.value ?? "";
    consultarUrl.value = cuota?.consultar_url ?? consultarUrl.value ?? "";
    estadoPago.value = "pendiente";
    confirmacionEmitida.value = false;
    mensajeEstado.value =
        "QR generado. Esperando la confirmación automática del callback real de PagoFácil.";
}

function submit() {
    form.put(route("admin.pago-cuotas.update", props.cuota?.id));
}

function nombreCompleto(alumno) {
    const completo =
        `${alumno?.nombres ?? ""} ${alumno?.apellidos ?? ""}`.trim();
    return completo || "Sin nombre";
}

onBeforeUnmount(() => {
    limpiarIntervalo();
});
</script>
