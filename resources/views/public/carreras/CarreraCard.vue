<template>
    <div
        :id="`carrera-${props.carrera.id}`"
        :class="[
            'group relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-100',
            highlighted ? 'ring-4 ring-emerald-200/40 bg-emerald-50/40' : '',
        ]"
    >
        <div
            class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
        ></div>
        <div
            class="absolute top-0 left-0 h-1 w-0 bg-gradient-to-r from-blue-600 to-blue-400 transition-all duration-300 group-hover:w-full"
        ></div>

        <div class="relative p-6">
            <div
                class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl text-blue-700"
            >
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <h3
                class="text-xl font-black text-slate-900 transition-colors duration-300 group-hover:text-blue-700"
            >
                {{ nombre }}
            </h3>

            <p class="mt-3 text-sm leading-6 text-slate-500">
                Carrera académica registrada en el Instituto Andrés Ibáñez.
            </p>

            <div class="mt-5 grid gap-3 text-sm">
                <div
                    class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3"
                >
                    <span class="font-bold text-slate-500">Código</span>
                    <span class="font-black text-slate-800">{{ codigo }}</span>
                </div>

                <div
                    class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3"
                >
                    <span class="font-bold text-slate-500">Duración</span>
                    <span class="font-black text-slate-800">{{
                        duracion
                    }}</span>
                </div>

                <div
                    class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3"
                >
                    <span class="font-bold text-slate-500">Régimen</span>
                    <span class="font-black text-slate-800">{{ regimen }}</span>
                </div>

                <div
                    class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3"
                >
                    <span class="font-bold text-slate-500">Estado</span>
                    <span
                        class="inline-flex items-center gap-2 font-black"
                        :class="
                            estadoActivo ? 'text-emerald-700' : 'text-red-700'
                        "
                    >
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-full"
                            :class="
                                estadoActivo
                                    ? 'bg-emerald-50 text-emerald-600'
                                    : 'bg-red-50 text-red-600'
                            "
                        >
                            <svg
                                v-if="estadoActivo"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="3"
                            >
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            <svg
                                v-else
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <circle cx="12" cy="12" r="2" />
                            </svg>
                        </span>
                        {{ estadoActivo ? "Activa" : "Inactiva" }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";

const props = defineProps({
    carrera: {
        type: Object,
        required: true,
    },
});

const nombre = computed(() => props.carrera.nombre || "Carrera sin nombre");
const codigo = computed(() => props.carrera.codigo ?? "-");
const duracion = computed(() => {
    return props.carrera.duracion ? `${props.carrera.duracion} semestres` : "-";
});
const regimen = computed(() => props.carrera.regimen_academico ?? "-");
const estadoActivo = computed(() => props.carrera.estado === true);
const highlighted = ref(false);

onMounted(() => {
    try {
        const hash = (window.location.hash || "").replace("#", "");
        if (hash === `carrera-${props.carrera.id}`) {
            const el = document.getElementById(hash);
            if (el) {
                el.scrollIntoView({ behavior: "smooth", block: "center" });
            }
            highlighted.value = true;
            setTimeout(() => (highlighted.value = false), 3000);
        }
    } catch (e) {
        // ignore
    }
});
</script>
