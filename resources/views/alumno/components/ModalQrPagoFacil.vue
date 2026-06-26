<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";

const props = defineProps({
    mostrar: {
        type: Boolean,
        default: false,
    },

    pago: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["cerrar", "confirmado"]);

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") ?? "";

const estadoPago = ref("Pendiente");
const mensajeEstado = ref(
    "El sistema está verificando el pago con el callback real de PagoFácil. Si no se confirma solo, puedes usar la verificación manual.",
);
const tipoEstado = ref("info");
const verificando = ref(false);
const mostrandoExito = ref(false);
const intervaloVerificacion = ref(null);
const confirmacionEmitida = ref(false);

const qrUrl = computed(() => props.pago?.qr_url ?? "");
const paymentNumber = computed(() => props.pago?.payment_number ?? "");
const estadoUrl = computed(() => props.pago?.estado_url ?? "");
const consultarUrl = computed(() => props.pago?.consultar_url ?? "");

const leerJsonSeguro = async (response) => {
    const texto = await response.text();

    try {
        return texto ? JSON.parse(texto) : null;
    } catch {
        throw new Error(
            `La respuesta del servidor no fue JSON válido: ${texto.slice(0, 180)}`,
        );
    }
};

const limpiarIntervalo = () => {
    if (intervaloVerificacion.value) {
        clearInterval(intervaloVerificacion.value);
        intervaloVerificacion.value = null;
    }
};

const reiniciarEstado = () => {
    estadoPago.value = props.pago?.estado ?? "Pendiente";
    mensajeEstado.value =
        "El sistema está verificando el pago con el callback real de PagoFácil. Si no se confirma solo, puedes usar la verificación manual.";
    tipoEstado.value = "info";
    verificando.value = false;
    mostrandoExito.value = false;
    confirmacionEmitida.value = false;
    limpiarIntervalo();
};

const iniciarAutoVerificacion = () => {
    limpiarIntervalo();

    intervaloVerificacion.value = setInterval(() => {
        verificarEstado(false);
    }, 5000);
};

const cerrarModal = () => {
    limpiarIntervalo();
    emit("cerrar");
};

const esEstadoConfirmado = (estado) => {
    const valor = String(estado ?? "")
        .toLowerCase()
        .trim();

    return valor === "confirmado" || valor === "pagado" || valor === "2";
};

const mostrarExito = () => {
    if (confirmacionEmitida.value) {
        return;
    }

    confirmarEstado();
};

const confirmarEstado = () => {
    if (confirmacionEmitida.value) {
        return;
    }

    confirmacionEmitida.value = true;
    limpiarIntervalo();
    mostrandoExito.value = true;
    estadoPago.value = "Confirmado";
    tipoEstado.value = "success";
    mensajeEstado.value =
        "Pago confirmado correctamente por el callback real de PagoFácil. Regresando a la vista de cuotas...";

    setTimeout(() => {
        emit("confirmado", props.pago);
        emit("cerrar");
    }, 1400);
};

const verificarEstado = async (mostrarCarga = true) => {
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

        const data = await leerJsonSeguro(response);

        if (!data.ok) {
            return;
        }

        const estado =
            data.pago?.estado ?? data.cuota?.estado ?? estadoPago.value;

        estadoPago.value = estado;

        if (esEstadoConfirmado(estado)) {
            mostrarExito();
            return;
        }

        mensajeEstado.value =
            "Aún no aparece la confirmación automática del callback. El sistema sigue verificando en segundo plano.";
        tipoEstado.value = "info";
    } catch (error) {
        mensajeEstado.value =
            error?.message ?? "No se pudo verificar el estado automáticamente.";
        tipoEstado.value = "error";
    } finally {
        verificando.value = false;
    }
};

const verificarManual = async () => {
    if (!consultarUrl.value || confirmacionEmitida.value) {
        return;
    }

    verificando.value = true;
    mensajeEstado.value = "Consultando PagoFácil manualmente...";
    tipoEstado.value = "info";

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

        const data = await leerJsonSeguro(response);

        if (!data.ok) {
            throw new Error(data.message || "No se pudo consultar el pago.");
        }

        const estado =
            data.pago?.estado ?? data.cuota?.estado ?? estadoPago.value;

        estadoPago.value = estado;

        if (esEstadoConfirmado(estado)) {
            mostrarExito();
            return;
        }

        mensajeEstado.value =
            "El pago todavía no está confirmado. El callback automático seguirá intentando detectar el cambio.";
        tipoEstado.value = "info";
    } catch (error) {
        mensajeEstado.value =
            error?.message ?? "No se pudo consultar manualmente el pago.";
        tipoEstado.value = "error";
    } finally {
        verificando.value = false;
    }
};

watch(
    () => [props.mostrar, props.pago?.id],
    ([mostrar]) => {
        if (mostrar) {
            reiniciarEstado();

            if (
                String(props.pago?.estado ?? "").toLowerCase() === "confirmado"
            ) {
                mostrarExito();
                return;
            }

            iniciarAutoVerificacion();
        } else {
            limpiarIntervalo();
        }
    },
    { immediate: true },
);

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
    limpiarIntervalo();
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="mostrar && pago"
                class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-950/70 px-4 py-6 backdrop-blur-sm"
                @click.self="cerrarModal"
            >
                <div
                    class="relative max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl"
                >
                    <button
                        type="button"
                        aria-label="Cerrar modal de QR"
                        class="absolute right-5 top-5 z-10 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-slate-900"
                        @click="cerrarModal"
                    >
                        ×
                    </button>

                    <div v-if="!mostrandoExito" class="p-5 sm:p-6">
                        <div class="mb-4 text-center">
                            <h2 class="mt-3 text-xl font-black text-slate-900">
                                Escanea para pagar
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Abre tu app de PagoFácil y escanea el QR.
                            </p>
                        </div>

                        <div
                            class="rounded-3xl border border-emerald-100 bg-emerald-50 p-4 text-center"
                        >
                            <img
                                :src="qrUrl"
                                alt="QR PagoFácil"
                                class="mx-auto h-60 w-60 rounded-3xl border border-white bg-white p-3 shadow-sm"
                            />

                            <p
                                class="mt-4 text-xs font-bold uppercase text-emerald-600"
                            >
                                Payment Number
                            </p>

                            <p
                                class="mt-1 break-all text-sm font-black text-slate-800"
                            >
                                {{ paymentNumber }}
                            </p>
                        </div>

                        <div
                            class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700"
                        >
                            <p class="font-semibold">Estado actual:</p>
                            <p class="mt-1">{{ estadoPago }}</p>
                            <p class="mt-2 text-xs text-slate-500">
                                {{ mensajeEstado }}
                            </p>
                        </div>

                        <div class="mt-4 flex flex-wrap justify-center gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="verificando"
                                @click="verificarEstado(true)"
                            >
                                <i class="fa-solid fa-bolt text-xs"></i>
                                <span v-if="verificando">Verificando...</span>
                                <span v-else>Verificar</span>
                            </button>

                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="verificando"
                                @click="verificarManual"
                            >
                                <i class="fa-solid fa-rotate text-xs"></i>
                                <span v-if="verificando">Consultando...</span>
                                <span v-else>Manual</span>
                            </button>
                        </div>
                    </div>

                    <div v-else class="px-6 py-12 text-center sm:px-8">
                        <div
                            class="mx-auto flex h-24 w-24 animate-bounce items-center justify-center rounded-full bg-green-100 text-5xl text-green-700 shadow-lg shadow-green-100"
                        >
                            <i class="fa-solid fa-check"></i>
                        </div>

                        <h2 class="mt-6 text-3xl font-black text-green-700">
                            Pago realizado exitosamente
                        </h2>

                        <p class="mt-3 text-sm text-slate-500">
                            El pago fue confirmado por PagoFácil y guardado en
                            el sistema.
                        </p>

                        <div
                            class="mt-6 rounded-3xl border border-green-100 bg-green-50 px-5 py-4 text-sm font-semibold text-green-700"
                        >
                            Cerrando los modales para volver a Mis Pagos...
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
