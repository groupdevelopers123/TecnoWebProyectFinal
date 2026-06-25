<script setup>
import { computed, ref, watch } from "vue";
import { Link } from "@inertiajs/vue3";
import ModalQrPagoFacil from "./ModalQrPagoFacil.vue";

const props = defineProps({
    mostrar: {
        type: Boolean,
        default: false,
    },

    credito: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["cerrar"]);
const cuotas = ref([]);
const cargando = ref(false);
const error = ref("");
const pagoModalAbierto = ref(false);
const cuotaSeleccionada = ref(null);
const metodoPago = ref("Efectivo");
const fechaPago = ref(new Date().toISOString().slice(0, 10));
const correoSolicitante = ref("");
const observacion = ref("");
const codigoTransaccion = ref("");
const procesandoPago = ref(false);
const errorPago = ref("");
const qrPago = ref(null);
const mostrarModalQr = ref(false);

const creditoId = computed(() => props.credito?.id ?? null);
const cuotasUrl = computed(() => {
    return creditoId.value
        ? `/alumno/mis-creditos/${creditoId.value}/cuotas`
        : null;
});

const pagoUrl = (cuotaId) => {
    return `/alumno/mis-creditos/cuotas/${cuotaId}/pagar`;
};

const generarQrUrl = (cuotaId) => {
    return `/alumno/mis-creditos/cuotas/${cuotaId}/generar-qr`;
};

const obtenerTokenCsrf = () => {
    return (
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") ?? ""
    );
};

const cargarCuotas = async () => {
    if (!cuotasUrl.value) {
        cuotas.value = [];
        return;
    }

    cargando.value = true;
    error.value = "";

    try {
        const response = await fetch(cuotasUrl.value, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            throw new Error("No se pudo cargar la información.");
        }

        const data = await response.json();

        cuotas.value = data.cuotas ?? [];
    } catch (err) {
        error.value =
            err instanceof Error
                ? err.message
                : "Error al cargar las cuotas del crédito.";
    } finally {
        cargando.value = false;
    }
};

const cerrarModal = () => {
    emit("cerrar");
    resetPagoModal();
};

const abrirPagoModal = (cuota) => {
    cuotaSeleccionada.value = cuota;
    pagoModalAbierto.value = true;
    errorPago.value = "";
    metodoPago.value = cuota.metodo_pago === "QR" ? "QR" : "Efectivo";
    fechaPago.value = cuota.fecha_pago ?? new Date().toISOString().slice(0, 10);
    correoSolicitante.value = "";
    observacion.value = "";
    codigoTransaccion.value = cuota.codigo_transaccion ?? "";
};

const cerrarPagoModal = () => {
    pagoModalAbierto.value = false;
    resetPagoModal();
};

const resetPagoModal = () => {
    cuotaSeleccionada.value = null;
    pagoModalAbierto.value = false;
    procesandoPago.value = false;
    errorPago.value = "";
    qrPago.value = null;
    mostrarModalQr.value = false;
};

const actualizarMetodoPago = (evento) => {
    metodoPago.value = evento.target.value;
};

const submitPagoCuota = async () => {
    if (!cuotaSeleccionada.value) {
        return;
    }

    procesandoPago.value = true;
    errorPago.value = "";

    try {
        const url =
            metodoPago.value === "QR"
                ? generarQrUrl(cuotaSeleccionada.value.id)
                : pagoUrl(cuotaSeleccionada.value.id);

        const data = new FormData();
        data.append("metodo_pago", metodoPago.value);
        data.append("fecha_pago", fechaPago.value ?? "");
        data.append("correo_solicitante", correoSolicitante.value ?? "");
        data.append("observacion", observacion.value ?? "");
        data.append("codigo_transaccion", codigoTransaccion.value ?? "");
        data.append("_token", obtenerTokenCsrf());

        const response = await fetch(url, {
            method: "POST",
            body: data,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        });

        const result = await response.json();

        if (!response.ok || !result.ok) {
            throw new Error(result.message || "Error al procesar el pago.");
        }

        if (metodoPago.value === "QR") {
            qrPago.value = {
                qr_url: result.cuota.qr_url,
                payment_number: result.cuota.payment_number,
                estado_url: result.cuota.estado_url,
                consultar_url: result.cuota.consultar_url,
                show_url: result.cuota.show_url,
                estado: result.cuota.estado,
            };

            mostrarModalQr.value = true;
            pagoModalAbierto.value = false;
        } else {
            await cargarCuotas();
            cerrarPagoModal();
        }
    } catch (err) {
        errorPago.value =
            err instanceof Error
                ? err.message
                : "No se pudo procesar el pago de la cuota.";
    } finally {
        procesandoPago.value = false;
    }
};

const cerrarModalQr = () => {
    mostrarModalQr.value = false;
    qrPago.value = null;
    cargarCuotas();
};

watch(
    () => props.mostrar,
    (nuevo) => {
        if (nuevo) {
            cargarCuotas();
            document.body.classList.add("overflow-hidden");
        } else {
            cuotas.value = [];
            error.value = "";
            document.body.classList.remove("overflow-hidden");
        }
    },
);

watch(
    () => props.credito,
    (nuevo) => {
        if (props.mostrar && nuevo) {
            cargarCuotas();
        }
    },
);

const formatearMonto = (valor) => {
    const numero = Number(valor ?? 0);

    return `Bs ${numero.toLocaleString("es-BO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};

const formatearFecha = (fecha) => {
    if (!fecha) {
        return "-";
    }

    const fechaObjeto = new Date(fecha);

    if (Number.isNaN(fechaObjeto.getTime())) {
        return "-";
    }

    return new Intl.DateTimeFormat("es-BO", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    }).format(fechaObjeto);
};
</script>

<template>
    <div
        v-if="mostrar"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm"
        @click.self="cerrarModal"
    >
        <div
            class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl"
        >
            <div
                class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-5"
            >
                <div>
                    <h2 class="text-xl font-black text-slate-900">
                        Cuotas del crédito
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Revisa cada cuota y usa el icono de billete para pagar
                        cuando todavía esté pendiente.
                    </p>
                </div>

                <button
                    type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-red-50 hover:text-red-600"
                    @click="cerrarModal"
                >
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">
                            Concepto
                        </p>
                        <p class="mt-2 font-black text-slate-900">
                            {{
                                credito?.concepto_pago?.nombre ?? "Sin concepto"
                            }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">
                            Saldo pendiente
                        </p>
                        <p class="mt-2 font-black text-amber-700">
                            {{ formatearMonto(credito?.saldo_pendiente) }}
                        </p>
                    </div>
                </div>

                <div
                    class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Nro.
                                    </th>
                                    <th
                                        class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Monto
                                    </th>
                                    <th
                                        class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Vencimiento
                                    </th>
                                    <th
                                        class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Pago
                                    </th>
                                    <th
                                        class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Estado
                                    </th>
                                    <th
                                        class="px-5 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-500"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 bg-white">
                                <tr v-if="cargando">
                                    <td
                                        colspan="6"
                                        class="px-6 py-12 text-center text-sm text-slate-500"
                                    >
                                        Cargando cuotas...
                                    </td>
                                </tr>

                                <tr v-else-if="error">
                                    <td
                                        colspan="6"
                                        class="px-6 py-12 text-center text-sm text-red-600"
                                    >
                                        {{ error }}
                                    </td>
                                </tr>

                                <tr v-else-if="!cuotas.length">
                                    <td
                                        colspan="6"
                                        class="px-6 py-12 text-center text-sm text-slate-500"
                                    >
                                        No se encontraron cuotas para este
                                        crédito.
                                    </td>
                                </tr>

                                <tr
                                    v-for="cuota in cuotas"
                                    :key="cuota.id"
                                    class="transition hover:bg-slate-50"
                                >
                                    <td
                                        class="px-5 py-4 text-sm font-black text-slate-800"
                                    >
                                        #{{ cuota.numero_cuota }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700 ring-1 ring-blue-100"
                                        >
                                            {{ formatearMonto(cuota.monto) }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-5 py-4 text-sm text-slate-600"
                                    >
                                        {{
                                            formatearFecha(
                                                cuota.fecha_vencimiento,
                                            )
                                        }}
                                    </td>
                                    <td
                                        class="px-5 py-4 text-sm text-slate-600"
                                    >
                                        {{ formatearFecha(cuota.fecha_pago) }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-black ring-1"
                                            :class="
                                                cuota.estado_cuota === 'pagado'
                                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                                                    : 'bg-amber-50 text-amber-700 ring-amber-100'
                                            "
                                        >
                                            {{
                                                cuota.estado_cuota === "pagado"
                                                    ? "PAGADO"
                                                    : cuota.estado_cuota
                                            }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                :href="`/alumno/mis-creditos/cuotas/${cuota.id}`"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200"
                                                title="Ver detalle de cuota"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                                    />
                                                    <path
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"
                                                    />
                                                </svg>
                                            </Link>

                                            <button
                                                v-if="
                                                    cuota.estado_cuota !==
                                                    'pagado'
                                                "
                                                type="button"
                                                @click="abrirPagoModal(cuota)"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition hover:-translate-y-0.5 hover:bg-emerald-100"
                                                title="Pagar cuota"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        d="M21 12v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"
                                                    />
                                                    <path d="M7 8h10" />
                                                    <path d="M7 12h10" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="pagoModalAbierto"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm"
                @click.self="cerrarPagoModal"
            >
                <div
                    class="w-full max-w-3xl rounded-[2rem] bg-white shadow-2xl"
                >
                    <div class="border-b border-slate-200 px-6 py-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-black text-slate-900">
                                    Pagar cuota #{{
                                        cuotaSeleccionada?.numero_cuota
                                    }}
                                </h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    Completa los datos del pago y elige QR si
                                    deseas generar un código de PagoFácil.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-red-50 hover:text-red-600"
                                @click="cerrarPagoModal"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M18 6 6 18M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Método de pago
                                </label>
                                <select
                                    v-model="metodoPago"
                                    @change="actualizarMetodoPago"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
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
                                    Fecha de pago
                                </label>
                                <input
                                    type="date"
                                    v-model="fechaPago"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Correo solicitante
                                </label>
                                <input
                                    type="email"
                                    v-model="correoSolicitante"
                                    placeholder="cliente@correo.com"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Código de transacción
                                </label>
                                <input
                                    type="text"
                                    v-model="codigoTransaccion"
                                    placeholder="Opcional"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Observación
                                </label>
                                <textarea
                                    v-model="observacion"
                                    rows="4"
                                    placeholder="Observación del pago"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                                ></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <div
                                    class="rounded-3xl border border-slate-200 bg-slate-50 p-4"
                                >
                                    <p
                                        class="text-xs font-bold uppercase text-slate-400"
                                    >
                                        Monto de la cuota
                                    </p>
                                    <p
                                        class="mt-2 text-lg font-black text-blue-700"
                                    >
                                        {{
                                            formatearMonto(
                                                cuotaSeleccionada?.monto,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="errorPago"
                            class="mt-4 rounded-2xl bg-red-50 p-4 text-sm text-red-700"
                        >
                            {{ errorPago }}
                        </div>

                        <div
                            class="mt-6 flex flex-wrap gap-3 border-t border-slate-200 pt-5"
                        >
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white transition hover:-translate-y-0.5 hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="procesandoPago"
                                @click="submitPagoCuota"
                            >
                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M21 12v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"
                                    />
                                    <path d="M7 8h10" />
                                    <path d="M7 12h10" />
                                </svg>
                                <span v-if="procesandoPago">Procesando...</span>
                                <span v-else>{{
                                    metodoPago === "QR"
                                        ? "Generar QR"
                                        : "Registrar pago"
                                }}</span>
                            </button>

                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-200"
                                @click="cerrarPagoModal"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <ModalQrPagoFacil
            :mostrar="mostrarModalQr"
            :pago="qrPago"
            @cerrar="cerrarModalQr"
            @confirmado="cerrarModalQr"
        />
    </div>
</template>
