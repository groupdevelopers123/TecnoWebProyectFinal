<template>
    <form @submit.prevent="submit" class="grid gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <h3 class="text-lg font-black text-slate-900">Datos del aula</h3>
            <p class="mt-1 text-sm text-slate-500">
                Registra la información necesaria para identificar y administrar
                el aula.
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Código</label
            >
            <input
                type="text"
                v-model="form.codigo"
                placeholder="Ej: AULA-101"
                required
                :class="inputClass('codigo')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("codigo")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Nombre</label
            >
            <input
                type="text"
                v-model="form.nombre"
                placeholder="Ej: Aula de Sistemas"
                required
                :class="inputClass('nombre')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("nombre")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Ubicación</label
            >
            <input
                type="text"
                v-model="form.ubicacion"
                placeholder="Ej: Bloque A"
                :class="inputClass('ubicacion')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("ubicacion")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Piso</label
            >
            <select v-model="form.piso" :class="inputClass('piso')">
                <option value="">Seleccione un piso</option>
                <option value="Planta baja">Planta baja</option>
                <option value="Primer piso">Primer piso</option>
                <option value="Segundo piso">Segundo piso</option>
                <option value="Tercer piso">Tercer piso</option>
                <option value="Cuarto piso">Cuarto piso</option>
                <option value="Quinto piso">Quinto piso</option>
            </select>
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("piso")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Capacidad</label
            >
            <input
                type="number"
                v-model.number="form.capacidad"
                min="1"
                placeholder="Ej: 30"
                required
                :class="inputClass('capacidad')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("capacidad")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Disponibilidad</label
            >
            <select
                v-model.number="form.disponible"
                required
                :class="inputClass('disponible')"
            >
                <option :value="1">Disponible</option>
                <option :value="0">No disponible</option>
            </select>
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("disponible")
            }}</span>
        </div>

        <div class="border-t border-slate-200 pt-6 md:col-span-2">
            <h3 class="text-lg font-black text-slate-900">Dimensiones</h3>
            <p class="mt-1 text-sm text-slate-500">
                Estos datos ayudarán a describir mejor el espacio físico del
                aula.
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Largo en metros</label
            >
            <input
                type="number"
                step="0.01"
                v-model="form.largo"
                placeholder="Ej: 8.50"
                :class="inputClass('largo')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("largo")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Ancho en metros</label
            >
            <input
                type="number"
                step="0.01"
                v-model="form.ancho"
                placeholder="Ej: 6.20"
                :class="inputClass('ancho')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("ancho")
            }}</span>
        </div>

        <div
            class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2"
        >
            <button
                :disabled="hasError || form.processing"
                type="submit"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:opacity-60 disabled:pointer-events-none"
            >
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                Guardar aula
            </button>

            <a
                :href="cancelUrlComputed"
                class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >Cancelar</a
            >
        </div>
    </form>
</template>

<script setup>
import { computed, reactive, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
    aula: { type: Object, default: () => ({}) },
    action: { type: String, required: true },
    method: { type: String, default: "post" },
    cancelUrl: { type: String, default: null },
});

const rules = {
    codigo: {
        regex: /^[A-Za-z0-9\-_ ]+$/,
        msg: "Sólo letras, números, espacios, guiones y guiones bajos.",
    },
    nombre: {
        regex: /^[A-Za-zÀ-ÿ0-9\s\.,'\-]+$/,
        msg: "Nombre inválido — sin caracteres especiales.",
    },
    ubicacion: {
        regex: /^[A-Za-zÀ-ÿ0-9\s\.,'\-]+$/,
        msg: "Ubicación inválida — sin caracteres especiales.",
    },
};

const form = useForm({
    codigo: props.aula.codigo ?? "",
    nombre: props.aula.nombre ?? "",
    ubicacion: props.aula.ubicacion ?? "",
    piso: props.aula.piso ?? "",
    capacidad: props.aula.capacidad ?? "",
    disponible: props.aula.disponible ?? 1,
    largo: props.aula.largo ?? "",
    ancho: props.aula.ancho ?? "",
});

const localErrors = reactive({
    codigo: "",
    nombre: "",
    ubicacion: "",
    piso: "",
    capacidad: "",
    disponible: "",
    largo: "",
    ancho: "",
});

function validateField(id) {
    const val = (form[id] ?? "").toString().trim();
    let ok = true;
    let msg = "";

    if (id === "piso" || id === "disponible") {
        if (val === "") {
            ok = false;
            msg = "Seleccione una opción.";
        }
    } else if (id === "capacidad") {
        if (val === "" || !/^[0-9]+$/.test(val) || parseInt(val, 10) < 1) {
            ok = false;
            msg = "Ingrese un número entero mayor a 0.";
        }
    } else if (id === "largo" || id === "ancho") {
        if (val !== "" && !/^[0-9]+(\.[0-9]{1,2})?$/.test(val)) {
            ok = false;
            msg = "Ingrese un número válido (ej: 8.50).";
        }
    } else {
        if (val === "") {
            ok = false;
            msg = "Este campo es obligatorio.";
        } else if (rules[id] && !rules[id].regex.test(val)) {
            ok = false;
            msg = rules[id].msg;
        }
    }

    localErrors[id] = ok ? "" : msg;
}

[
    "codigo",
    "nombre",
    "ubicacion",
    "piso",
    "capacidad",
    "disponible",
    "largo",
    "ancho",
].forEach((f) => {
    watch(
        () => form[f],
        () => validateField(f),
        { immediate: true },
    );
});

function inputClass(id) {
    const base =
        "w-full rounded-2xl border px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100";
    return localErrors[id]
        ? base + " border-red-500"
        : base + " border-slate-200";
}

const hasError = computed(
    () =>
        Object.values(localErrors).some(
            (v) => v && v.toString().trim() !== "",
        ) || Object.keys(form.errors).length > 0,
);

function errorMessage(id) {
    return localErrors[id] || form.errors[id] || "";
}

const cancelUrlComputed = computed(() => {
    if (props.cancelUrl) return props.cancelUrl;
    try {
        return typeof route === "function" ? route("admin.aulas.index") : "#";
    } catch (e) {
        return "#";
    }
});

function submit() {
    if (hasError.value) return;
    const method = (props.method || "post").toLowerCase();
    if (method === "put" || method === "patch") {
        form.put(props.action);
    } else {
        form.post(props.action);
    }
}
</script>
