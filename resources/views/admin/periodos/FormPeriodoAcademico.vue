<template>
    <form @submit.prevent="submit" class="grid gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <h3 class="text-lg font-black text-slate-900">
                Datos del periodo académico
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Define la gestión, tipo de periodo y fechas académicas.
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Nombre</label
            >
            <input
                id="nombre"
                type="text"
                v-model="form.nombre"
                placeholder="Ej: Periodo 1-2026"
                required
                :class="inputClass('nombre')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("nombre")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Gestión</label
            >
            <input
                id="gestion"
                type="number"
                v-model="form.gestion"
                required
                :class="inputClass('gestion')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("gestion")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Tipo de periodo</label
            >
            <select
                id="tipo_periodo"
                v-model="form.tipo_periodo"
                :class="inputClass('tipo_periodo')"
            >
                <option value="">Seleccione</option>
                <option value="Semestral">Semestral</option>
                <option value="Anual">Anual</option>
                <option value="Modular">Modular</option>
            </select>
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("tipo_periodo")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Número de periodo</label
            >
            <select
                id="numero_periodo"
                v-model="form.numero_periodo"
                :class="inputClass('numero_periodo')"
            >
                <option value="">Seleccione</option>
                <option
                    v-for="numero in [1, 2, 3, 4]"
                    :key="numero"
                    :value="numero"
                >
                    {{ numero }}
                </option>
            </select>
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("numero_periodo")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Fecha inicio</label
            >
            <input
                id="fecha_inicio"
                type="date"
                v-model="form.fecha_inicio"
                :class="inputClass('fecha_inicio')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("fecha_inicio")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Fecha fin</label
            >
            <input
                id="fecha_fin"
                type="date"
                v-model="form.fecha_fin"
                :class="inputClass('fecha_fin')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("fecha_fin")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Estado</label
            >
            <select
                id="estado"
                v-model="form.estado"
                required
                :class="inputClass('estado')"
            >
                <option :value="1">Activo</option>
                <option :value="0">Inactivo</option>
            </select>
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("estado")
            }}</span>
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
                Guardar periodo
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
    periodo: { type: Object, default: () => ({}) },
    action: { type: String, required: true },
    method: { type: String, default: "post" },
    cancelUrl: { type: String, default: null },
});

function normalizeDate(value) {
    if (!value) {
        return "";
    }

    return String(value).slice(0, 10);
}

function currentYear() {
    return new Date().getFullYear();
}

const form = useForm({
    nombre: props.periodo.nombre ?? "",
    gestion: props.periodo.gestion ?? currentYear(),
    tipo_periodo: props.periodo.tipo_periodo ?? "",
    numero_periodo: props.periodo.numero_periodo ?? "",
    fecha_inicio: normalizeDate(props.periodo.fecha_inicio),
    fecha_fin: normalizeDate(props.periodo.fecha_fin),
    estado: props.periodo.estado === false ? 0 : 1,
});

const localErrors = reactive({
    nombre: "",
    gestion: "",
    tipo_periodo: "",
    numero_periodo: "",
    fecha_inicio: "",
    fecha_fin: "",
    estado: "",
});

function validateField(id) {
    const val = (form[id] ?? "").toString().trim();
    let msg = "";

    if (
        (id === "nombre" || id === "gestion" || id === "estado") &&
        val === ""
    ) {
        msg = "Este campo es obligatorio.";
    }

    if (id === "nombre" && val !== "" && val.length > 100) {
        msg = "El nombre no puede superar 100 caracteres.";
    }

    if (
        id === "gestion" &&
        val !== "" &&
        (!/^\d+$/.test(val) || Number(val) < 2020 || Number(val) > 2100)
    ) {
        msg = "La gestión no es válida.";
    }

    if (
        id === "tipo_periodo" &&
        val !== "" &&
        !["Semestral", "Anual", "Modular"].includes(val)
    ) {
        msg = "Seleccione un tipo de periodo válido.";
    }

    if (
        id === "numero_periodo" &&
        val !== "" &&
        (!/^\d+$/.test(val) || Number(val) < 1 || Number(val) > 4)
    ) {
        msg = "El número debe estar entre 1 y 4.";
    }

    if (
        id === "fecha_fin" &&
        form.fecha_inicio &&
        form.fecha_fin &&
        form.fecha_fin < form.fecha_inicio
    ) {
        msg = "La fecha fin debe ser posterior o igual a la fecha inicio.";
    }

    localErrors[id] = msg;
}

[
    "nombre",
    "gestion",
    "tipo_periodo",
    "numero_periodo",
    "fecha_inicio",
    "fecha_fin",
    "estado",
].forEach((field) => {
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
    () => props.cancelUrl || route("admin.periodos-academicos.index"),
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
