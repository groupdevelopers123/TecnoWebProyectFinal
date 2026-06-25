<script setup>
import { computed, onBeforeUnmount, onMounted } from "vue";

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

const emit = defineEmits(["cerrar", "inscribirse"]);

const nombreCarrera = computed(() => {
    return props.oferta?.carrera?.nombre ?? "Carrera no registrada";
});

const nombreOferta = computed(() => {
    return props.oferta?.nombre ?? nombreCarrera.value;
});

const periodoAcademico = computed(() => {
    const periodo = props.oferta?.periodo_academico;

    if (!periodo) {
        return "Periodo no registrado";
    }

    if (periodo.gestion) {
        return `${periodo.nombre ?? "Periodo"} - ${periodo.gestion}`;
    }

    return periodo.nombre ?? "Periodo no registrado";
});

const estaDisponible = computed(() => {
    return (
        Boolean(props.oferta?.estado) &&
        Number(props.oferta?.cupos_disponibles ?? 0) > 0
    );
});

const cerrarModal = () => {
    emit("cerrar");
};

const handleInscribirse = () => {
    emit("inscribirse", props.oferta);
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
                v-if="mostrar && oferta"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
                @click.self="cerrarModal"
            >
                <div
                    class="w-full max-w-3xl overflow-hidden rounded-[32px] bg-white shadow-2xl ring-1 ring-slate-200"
                >
                    <div
                        class="bg-gradient-to-r from-blue-700 to-indigo-700 px-6 py-5 text-white"
                    >
                        <div class="flex items-start justify-between gap-5">
                            <div>
                                <p
                                    class="text-xs font-black uppercase tracking-[0.2em] text-blue-200"
                                >
                                    Detalle de la oferta
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
                                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-white transition hover:bg-white/20"
                                @click="cerrarModal"
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
                    </div>

                    <div class="grid gap-6 bg-slate-50 p-6 sm:p-8">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div
                                class="rounded-3xl border border-slate-200 bg-white p-5"
                            >
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400"
                                >
                                    Periodo académico
                                </p>
                                <p
                                    class="mt-3 text-sm font-black text-slate-900"
                                >
                                    {{ periodoAcademico }}
                                </p>
                            </div>
                            <div
                                class="rounded-3xl border border-slate-200 bg-white p-5"
                            >
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400"
                                >
                                    Cupos restantes
                                </p>
                                <p
                                    class="mt-3 text-sm font-black text-slate-900"
                                >
                                    {{ props.oferta?.cupos_disponibles ?? 0 }} /
                                    {{ props.oferta?.cantidad_cupos ?? 0 }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div
                                class="rounded-3xl border border-slate-200 bg-white p-5"
                            >
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400"
                                >
                                    Régimen
                                </p>
                                <p
                                    class="mt-3 text-sm font-black text-slate-900"
                                >
                                    {{
                                        props.oferta?.carrera
                                            ?.regimen_academico ?? "No definido"
                                    }}
                                </p>
                            </div>
                            <div
                                class="rounded-3xl border border-slate-200 bg-white p-5"
                            >
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400"
                                >
                                    Duración
                                </p>
                                <p
                                    class="mt-3 text-sm font-black text-slate-900"
                                >
                                    {{
                                        props.oferta?.carrera?.duracion ??
                                        "No definida"
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="rounded-3xl border border-slate-200 bg-white p-6"
                        >
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400"
                            >
                                Costos
                            </p>
                            <div class="mt-4 space-y-3 text-sm text-slate-700">
                                <div class="flex items-center justify-between">
                                    <span>Matrícula</span>
                                    <span class="font-black text-slate-900"
                                        >Bs
                                        {{
                                            Number(
                                                props.oferta
                                                    ?.precio_matricula ?? 0,
                                            ).toLocaleString("es-BO", {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2,
                                            })
                                        }}</span
                                    >
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Mensualidad</span>
                                    <span class="font-black text-slate-900"
                                        >Bs
                                        {{
                                            Number(
                                                props.oferta
                                                    ?.precio_mensualidad ?? 0,
                                            ).toLocaleString("es-BO", {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2,
                                            })
                                        }}</span
                                    >
                                </div>
                                <div
                                    class="flex items-center justify-between border-t border-slate-200 pt-3"
                                >
                                    <span class="font-bold"
                                        >Carrera completa</span
                                    >
                                    <span class="font-black text-blue-700"
                                        >Bs
                                        {{
                                            Number(
                                                props.oferta
                                                    ?.precio_carrera_completa ??
                                                    0,
                                            ).toLocaleString("es-BO", {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2,
                                            })
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:justify-end"
                        >
                            <button
                                type="button"
                                class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100 sm:w-auto"
                                @click="cerrarModal"
                            >
                                Cerrar
                            </button>
                            <button
                                type="button"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-black text-white transition hover:bg-blue-700 sm:w-auto"
                                :disabled="!estaDisponible"
                                @click="handleInscribirse"
                            >
                                Inscribirme
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
