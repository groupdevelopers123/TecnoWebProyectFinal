<template>
    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-black text-slate-900">{{ titulo }}</h1>
            <p class="mt-2 text-sm text-slate-500">
                Desde {{ inicioFormateado }} hasta {{ finFormateado }}
            </p>
        </div>

        <div
            class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm"
        >
            <table class="min-w-full border-collapse text-left text-sm">
                <thead class="bg-slate-950 text-white">
                    <tr>
                        <th
                            v-for="columna in columnas"
                            :key="columna"
                            class="px-4 py-3 font-semibold"
                        >
                            {{ columna }}
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <template v-if="filas.length">
                        <tr
                            v-for="(fila, filaIndex) in filas"
                            :key="filaIndex"
                            class="border-b border-slate-200 even:bg-slate-50"
                        >
                            <td
                                v-for="(valor, columnaIndex) in fila"
                                :key="`${filaIndex}-${columnaIndex}`"
                                class="px-4 py-3 align-top text-slate-700"
                            >
                                {{ valor }}
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <td
                                :colspan="columnas.length"
                                class="px-4 py-6 text-center text-slate-500"
                            >
                                No existen registros para el periodo
                                seleccionado.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    titulo: { type: String, default: "Reporte" },
    columnas: { type: Array, default: () => [] },
    filas: { type: Array, default: () => [] },
    inicio: { type: [String, Date], default: "" },
    fin: { type: [String, Date], default: "" },
});

const formatDate = (value) => {
    if (!value) return "";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return String(value);
    }
    return new Intl.DateTimeFormat("es-BO", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    }).format(date);
};

const inicioFormateado = computed(() => formatDate(props.inicio));
const finFormateado = computed(() => formatDate(props.fin));
</script>
