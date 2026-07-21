<template>
    <form @submit.prevent="submit" class="grid gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <h3 class="text-lg font-black text-slate-900">
                Asignación carrera - materia
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Selecciona una carrera y una materia para habilitarla en
                horarios.
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Carrera</label
            >
            <select
                v-model="form.carrera_id"
                required
                :class="inputClass('carrera_id')"
            >
                <option value="">Seleccione una carrera</option>
                <option
                    v-for="carrera in carreras"
                    :key="carrera.id"
                    :value="carrera.id"
                    :data-regimen="carrera.regimen_academico"
                >
                    {{ carrera.nombre }}
                </option>
            </select>
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("carrera_id")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Materia</label
            >
            <select
                v-model="form.materia_id"
                required
                :class="inputClass('materia_id')"
            >
                <option value="">Seleccione una materia</option>
                <option
                    v-for="materia in materias"
                    :key="materia.id"
                    :value="materia.id"
                >
                    {{ materia.nombre }}
                </option>
            </select>
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("materia_id")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700">
                {{ periodoLabel }}
            </label>
            <select
                v-model="form.periodo_numero"
                :class="inputClass('periodo_numero')"
            >
                <option value="">Seleccione</option>
                <option
                    v-for="periodo in periodos"
                    :key="periodo"
                    :value="periodo"
                >
                    {{ periodo }}
                </option>
            </select>
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("periodo_numero")
            }}</span>
        </div>

        <div>
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Regla visual
                </p>
                <p class="mt-1 text-sm font-semibold text-slate-700">
                    El período cambia según el régimen de la carrera.
                </p>
            </div>
        </div>

        <div
            class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2"
        >
            <button
                :disabled="hasError || form.processing"
                type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:pointer-events-none disabled:opacity-60"
            >
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                Guardar asignación
            </button>

            <a
                :href="cancelUrlComputed"
                class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
            >
                Cancelar
            </a>
        </div>
    </form>
</template>

<script setup>
import { computed, reactive, watch } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    asignacion: { type: Object, default: () => ({}) },
    carreras: { type: Array, default: () => [] },
    materias: { type: Array, default: () => [] },
    action: { type: String, required: true },
    method: { type: String, default: "post" },
    cancelUrl: { type: String, default: null },
});

const form = useForm({
    carrera_id: props.asignacion.carrera_id ?? "",
    materia_id: props.asignacion.materia_id ?? "",
    periodo_numero: props.asignacion.periodo_numero ?? "",
});

const localErrors = reactive({
    carrera_id: "",
    materia_id: "",
    periodo_numero: "",
});

const carreraSeleccionada = computed(
    () =>
        props.carreras.find(
            (carrera) => String(carrera.id) === String(form.carrera_id),
        ) || null,
);

const periodoLabel = computed(() => {
    const regimen = String(
        carreraSeleccionada.value?.regimen_academico || "",
    ).toLowerCase();
    if (regimen === "anual") {
        return "Año";
    }
    if (regimen === "modular") {
        return "Módulo";
    }
    return "Semestre";
});

const periodos = computed(() => {
    const max = carreraSeleccionada.value?.duracion || 12;
    return Array.from({ length: max }, (_, index) => index + 1);
});

function validateField(id) {
    const val = (form[id] ?? "").toString().trim();
    let msg = "";

    if ((id === "carrera_id" || id === "materia_id") && val === "") {
        msg = "Este campo es obligatorio.";
    }

    if (
        id === "periodo_numero" &&
        val !== "" &&
        (!/^\d+$/.test(val) || Number(val) < 1 || Number(val) > 12)
    ) {
        msg = "El periodo debe estar entre 1 y 12.";
    }

    localErrors[id] = msg;
}

["carrera_id", "materia_id", "periodo_numero"].forEach((field) => {
    watch(
        () => form[field],
        () => validateField(field),
        { immediate: true },
    );
});

function inputClass(id) {
    const base =
        "w-full rounded-2xl border px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100";
    return localErrors[id]
        ? `${base} border-red-500`
        : `${base} border-slate-200`;
}

const hasError = computed(
    () =>
        Object.values(localErrors).some(
            (value) => value && value.toString().trim() !== "",
        ) || Object.keys(form.errors).length > 0,
);

function errorMessage(id) {
    return localErrors[id] || form.errors[id] || "";
}

const cancelUrlComputed = computed(
    () => props.cancelUrl || route("admin.carrera-materias.index"),
);

function submit() {
    if (hasError.value) {
        return;
    }

    const method = (props.method || "post").toLowerCase();
    if (method === "put" || method === "patch") {
        form.put(props.action);
    } else {
        form.post(props.action);
    }
}
</script>
