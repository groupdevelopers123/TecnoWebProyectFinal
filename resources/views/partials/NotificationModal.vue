<script setup>
import { computed } from "vue";

const props = defineProps({
    notificaciones: {
        type: Array,
        default: () => [],
    },
    panelAbierto: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["ver-seguimiento", "marcar-como-leida"]);

const cantidadNotificaciones = computed(() => props.notificaciones.length);
</script>

<template>
    <Transition
        enter-active-class="transition duration-150"
        enter-from-class="-translate-y-2 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-100"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="-translate-y-2 opacity-0"
    >
        <div
            v-if="panelAbierto === 'notificaciones'"
            class="absolute right-0 top-full mt-3 w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
        >
            <div class="border-b border-slate-100 px-4 py-3">
                <h3 class="text-sm font-black text-slate-900">
                    Notificaciones
                </h3>
                <p class="mt-0.5 text-xs text-slate-500">Avisos académicos</p>
            </div>

            <div v-if="cantidadNotificaciones > 0" class="space-y-2 p-3">
                <template
                    v-for="notificacion in notificaciones"
                    :key="notificacion.id"
                >
                    <div
                        class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-left"
                    >
                        <p class="text-sm font-black text-slate-900">
                            {{ notificacion.titulo ?? "Notificación" }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{
                                notificacion.mensaje ?? notificacion.descripcion
                            }}
                        </p>
                        <p class="mt-2 text-[11px] text-slate-400">
                            {{ notificacion.fecha || "" }}
                        </p>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <button
                                v-if="notificacion.inscripcion_materia_id"
                                type="button"
                                class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 transition hover:bg-blue-100"
                                @click="$emit('ver-seguimiento', notificacion)"
                            >
                                Ver seguimiento
                            </button>

                            <button
                                type="button"
                                class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-100"
                                @click="
                                    $emit('marcar-como-leida', notificacion)
                                "
                            >
                                Marcar como leída
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div
                v-else
                class="flex flex-col items-center px-5 py-8 text-center"
            >
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600"
                >
                    <svg
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"
                        />
                        <path d="M13.7 21a2 2 0 0 1-3.4 0" />
                    </svg>
                </div>

                <p class="mt-4 text-sm font-bold text-slate-700">
                    Sin notificaciones
                </p>
                <p class="mt-1 text-xs leading-5 text-slate-500">
                    La vista de notificaciones todavía no fue creada.
                </p>
            </div>
        </div>
    </Transition>
</template>
