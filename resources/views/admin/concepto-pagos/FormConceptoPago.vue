<template>
    <form @submit.prevent="submit" class="mt-8 space-y-6">
        <div
            v-if="errores.length"
            class="rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700"
        >
            <p class="font-bold">Revisa los siguientes errores:</p>
            <ul class="mt-2 list-inside list-disc">
                <li
                    v-for="(error, index) in errores"
                    :key="`${error}-${index}`"
                >
                    {{ error }}
                </li>
            </ul>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <h3 class="text-lg font-black text-slate-900">
                    Datos del concepto de pago
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Registra el nombre, descripción y estado del concepto.
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Nombre</label
                >
                <input
                    v-model="form.nombre"
                    type="text"
                    required
                    placeholder="Ejemplo: Matrícula, Mensualidad, Certificado, Examen"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p v-if="form.errors.nombre" class="mt-2 text-sm text-red-600">
                    {{ form.errors.nombre }}
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Descripción</label
                >
                <textarea
                    v-model="form.descripcion"
                    rows="4"
                    placeholder="Descripción del concepto de pago"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                ></textarea>
                <p
                    v-if="form.errors.descripcion"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.descripcion }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Estado</label
                >
                <select
                    v-model="form.estado"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
                <p v-if="form.errors.estado" class="mt-2 text-sm text-red-600">
                    {{ form.errors.estado }}
                </p>
            </div>

            <div
                class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2"
            >
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                >
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    {{ submitLabel }}
                </button>

                <Link
                    :href="cancelUrl"
                    class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    Cancelar
                </Link>
            </div>
        </div>
    </form>
</template>

<script setup>
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { computed, watch } from "vue";

const props = defineProps({
    concepto: { type: Object, default: () => ({}) },
    submitUrl: { type: String, default: "" },
    cancelUrl: { type: String, default: "" },
    submitLabel: { type: String, default: "Guardar concepto" },
    submitMethod: { type: String, default: "post" },
});

const page = usePage();
const form = useForm({
    nombre: props.concepto?.nombre ?? "",
    descripcion: props.concepto?.descripcion ?? "",
    estado: props.concepto?.estado ?? "Activo",
});

const errores = computed(() =>
    Object.values(page.props.errors ?? {})
        .flat()
        .filter(Boolean),
);

watch(
    () => props.concepto,
    (value) => {
        const next = value ?? {};
        form.nombre = next.nombre ?? "";
        form.descripcion = next.descripcion ?? "";
        form.estado = next.estado ?? "Activo";
    },
    { immediate: true, deep: true },
);

function submit() {
    if (props.submitMethod === "put") {
        form.put(props.submitUrl);
        return;
    }

    form.post(props.submitUrl);
}
</script>
