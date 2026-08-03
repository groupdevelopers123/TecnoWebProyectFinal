<template>
    <div
        :id="`docente-${props.docente.id}`"
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

        <div class="relative p-6 text-center">
            <div
                class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 text-3xl text-blue-700"
            >
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>

            <h3
                class="mt-5 text-lg font-black text-slate-900 transition-colors duration-300 group-hover:text-blue-700"
            >
                {{ nombreCompleto }}
            </h3>

            <p class="mt-1 text-sm font-bold text-blue-700">
                {{ especialidad }}
            </p>

            <div class="mt-4 space-y-2 text-sm text-slate-500">
                <div
                    v-if="hasEstado"
                    class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-left"
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
                        {{ estadoActivo ? "Activo" : "Inactivo" }}
                    </span>
                </div>
            </div>

            <p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-500">
                {{ biografia }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";

const props = defineProps({
    docente: {
        type: Object,
        required: true,
    },
});

const nombreCompleto = computed(() => {
    const nombres = props.docente.user?.nombres ?? "";
    const apellidos = props.docente.user?.apellidos ?? "";
    const nombre = `${nombres} ${apellidos}`.trim();

    return nombre || "Docente sin usuario";
});

const especialidad = computed(
    () => props.docente.especialidad || "Especialidad no registrada",
);

const biografia = computed(
    () => props.docente.biografia || "Sin biografía registrada.",
);

const hasEstado = computed(() =>
    Object.prototype.hasOwnProperty.call(props.docente, "estado"),
);

const estadoActivo = computed(() => props.docente.estado === true);

const highlighted = ref(false);

onMounted(() => {
    try {
        const hash = (window.location.hash || "").replace("#", "");
        if (hash === `docente-${props.docente.id}`) {
            const el = document.getElementById(hash);
            if (el) el.scrollIntoView({ behavior: "smooth", block: "center" });
            highlighted.value = true;
            setTimeout(() => (highlighted.value = false), 3000);
        }
    } catch (e) {}
});
</script>
