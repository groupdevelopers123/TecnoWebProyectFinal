<template>
    <form @submit.prevent="submit" class="grid gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <h3 class="text-lg font-black text-slate-900">
                Datos de la materia
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Registra la información principal de la materia académica.
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Código</label
            >
            <input
                id="codigo"
                type="text"
                v-model="form.codigo"
                placeholder="Ej: WEB-101"
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
                id="nombre"
                type="text"
                v-model="form.nombre"
                placeholder="Ej: Tecnología Web"
                required
                :class="inputClass('nombre')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("nombre")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Carga horaria</label
            >
            <input
                id="carga_horaria"
                type="number"
                min="1"
                step="1"
                v-model="form.carga_horaria"
                placeholder="Ej: 80"
                :class="inputClass('carga_horaria')"
            />
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("carga_horaria")
            }}</span>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700"
                >Docente (Opcional)</label
            >
            <select
                id="docente_detalle_id"
                v-model="form.docente_detalle_id"
                :class="inputClass('docente_detalle_id')"
            >
                <option value="">Seleccione un docente (opcional)</option>
                <option
                    v-for="docente in docentes"
                    :key="docente.id"
                    :value="docente.id"
                >
                    {{ docente.codigo }} - {{ docente.nombre }}
                </option>
            </select>
            <span class="mt-1 text-sm text-red-600" aria-live="polite">{{
                errorMessage("docente_detalle_id")
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
                <option :value="1">Activa</option>
                <option :value="0">Inactiva</option>
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
                Guardar materia
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
    materia: { type: Object, default: () => ({}) },
    carreras: { type: Array, default: () => [] },
    docentes: { type: Array, default: () => [] },
    carreraSeleccionada: { type: Object, default: null },
    action: { type: String, required: true },
    method: { type: String, default: "post" },
    cancelUrl: { type: String, default: null },
});

const form = useForm({
    codigo: props.materia.codigo ?? "",
    nombre: props.materia.nombre ?? "",
    carga_horaria: props.materia.carga_horaria ?? "",
    docente_detalle_id: props.materia.docente_detalle_id ?? "",
    estado: props.materia.estado === false ? 0 : 1,
});

const localErrors = reactive({
    codigo: "",
    nombre: "",
    carga_horaria: "",
    docente_detalle_id: "",
    estado: "",
});

function validateField(id) {
    const val = (form[id] ?? "").toString().trim();
    let msg = "";

    if ((id === "codigo" || id === "nombre") && val === "") {
        msg = "Este campo es obligatorio.";
    }

    if (id === "codigo" && val !== "" && !/^[A-Za-z0-9\-_ ]+$/.test(val)) {
        msg = "Sólo letras, números, espacios, guiones y guiones bajos.";
    }

    if (
        id === "nombre" &&
        val !== "" &&
        !/^[A-Za-zÀ-ÿ0-9\s\.,'\-]+$/u.test(val)
    ) {
        msg = "Nombre inválido.";
    }

    if (
        id === "carga_horaria" &&
        val !== "" &&
        (!/^\d+$/.test(val) || Number(val) < 1)
    ) {
        msg = "Ingrese un número entero mayor a 0.";
    }

    if (id === "estado" && val === "") {
        msg = "Este campo es obligatorio.";
    }

    if (id === "docente_detalle_id" && val !== "" && !/^\d+$/.test(val)) {
        msg = "Seleccione un docente válido.";
    }

    localErrors[id] = msg;
}

["codigo", "nombre", "carga_horaria", "docente_detalle_id", "estado"].forEach(
    (field) => {
        watch(
            () => form[field],
            () => validateField(field),
            { immediate: true },
        );
    },
);

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
    () => props.cancelUrl || route("admin.materias.index"),
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
