<template>
    <form class="space-y-6" @submit.prevent="submit">
        <div
            v-if="errorList.length"
            class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700"
        >
            <p class="font-bold">Revisa los siguientes errores:</p>
            <ul class="mt-2 list-inside list-disc">
                <li v-for="(error, index) in errorList" :key="index">
                    {{ error }}
                </li>
            </ul>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <h3 class="text-lg font-black text-slate-900">
                    Datos de la inscripción
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Selecciona alumno, oferta académica, periodo y fecha de
                    inscripción.
                </p>
            </div>

            <div
                v-if="alumnos.length === 0"
                class="md:col-span-2 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 text-sm text-yellow-800"
            >
                <p class="font-bold">No hay alumnos disponibles</p>
                <p class="mt-1">
                    Primero registra un usuario con rol alumno y completa sus
                    datos de alumno.
                </p>
            </div>

            <div
                v-if="ofertas.length === 0"
                class="md:col-span-2 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 text-sm text-yellow-800"
            >
                <p class="font-bold">No hay ofertas académicas disponibles</p>
                <p class="mt-1">
                    Primero registra una oferta académica activa con cupos
                    disponibles.
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Alumno</label
                >
                <select
                    v-model="form.alumno_detalle_id"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Seleccione un alumno</option>
                    <option
                        v-for="alumno in alumnos"
                        :key="alumno.id"
                        :value="alumno.id"
                    >
                        {{ alumno.codigo || "SIN-COD" }} -
                        {{ alumno.nombre_completo || "Alumno sin usuario"
                        }}<span v-if="alumno.ci"> / CI: {{ alumno.ci }}</span>
                    </option>
                </select>
                <p
                    v-if="form.errors.alumno_detalle_id"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.alumno_detalle_id }}
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Oferta académica</label
                >
                <select
                    v-model="form.oferta_academica_id"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Seleccione una oferta académica</option>
                    <option
                        v-for="oferta in ofertas"
                        :key="oferta.id"
                        :value="oferta.id"
                    >
                        {{ oferta.nombre }} - {{ oferta.carrera.codigo }}
                        {{ oferta.carrera.nombre }} /
                        {{ oferta.periodo_academico.nombre }}
                        {{ oferta.periodo_academico.gestion }} / Cupos:
                        {{ oferta.cupos_disponibles }}/{{
                            oferta.cantidad_cupos
                        }}
                    </option>
                </select>
                <p
                    v-if="form.errors.oferta_academica_id"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.oferta_academica_id }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Periodo número</label
                >
                <input
                    v-model="form.periodo_numero"
                    type="number"
                    min="1"
                    max="20"
                    required
                    placeholder="Ejemplo: 1"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.periodo_numero"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.periodo_numero }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Fecha de inscripción</label
                >
                <input
                    v-model="form.fecha_inscripcion"
                    type="date"
                    required
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <p
                    v-if="form.errors.fecha_inscripcion"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.fecha_inscripcion }}
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Observación</label
                >
                <textarea
                    v-model="form.observacion"
                    rows="4"
                    placeholder="Observaciones adicionales de la inscripción"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                ></textarea>
                <p
                    v-if="form.errors.observacion"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.observacion }}
                </p>
            </div>

            <div
                class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2"
            >
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                    :disabled="form.processing"
                >
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Guardar inscripción
                </button>

                <a
                    :href="cancelUrl"
                    class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { computed, watch } from "vue";

const props = defineProps({
    inscripcion: { type: Object, default: () => ({}) },
    action: { type: String, required: true },
    method: { type: String, default: "post" },
    cancelUrl: { type: String, required: true },
    alumnos: { type: Array, default: () => [] },
    ofertas: { type: Array, default: () => [] },
});

const form = useForm({
    alumno_detalle_id: props.inscripcion.alumno_detalle_id ?? "",
    oferta_academica_id: props.inscripcion.oferta_academica_id ?? "",
    periodo_numero: props.inscripcion.periodo_numero ?? "",
    fecha_inscripcion: props.inscripcion.fecha_inscripcion ?? todayString(),
    observacion: props.inscripcion.observacion ?? "",
});

const errorList = computed(() => Object.values(form.errors));

watch(
    () => props.inscripcion,
    (inscripcion) => {
        form.alumno_detalle_id = inscripcion.alumno_detalle_id ?? "";
        form.oferta_academica_id = inscripcion.oferta_academica_id ?? "";
        form.periodo_numero = inscripcion.periodo_numero ?? "";
        form.fecha_inscripcion = inscripcion.fecha_inscripcion ?? todayString();
        form.observacion = inscripcion.observacion ?? "";
    },
    { deep: true },
);

function todayString() {
    return new Intl.DateTimeFormat("en-CA").format(new Date());
}

function submit() {
    const payload = {
        ...form.data(),
        oferta_academica_id: Number(form.oferta_academica_id),
        alumno_detalle_id: Number(form.alumno_detalle_id),
        periodo_numero: Number(form.periodo_numero),
    };

    if (props.method.toLowerCase() === "put") {
        form.transform(() => payload).put(props.action, {
            preserveScroll: true,
        });
        return;
    }

    form.transform(() => payload).post(props.action, { preserveScroll: true });
}
</script>
