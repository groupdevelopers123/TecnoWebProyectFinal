<template>
    <form @submit.prevent="submit" class="space-y-6">
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
                    Datos del seguimiento académico
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Registra nota final, asistencia, docente responsable y
                    estado académico.
                </p>
            </div>

            <div
                v-if="inscripcionMaterias.length === 0"
                class="md:col-span-2 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 text-sm text-yellow-800"
            >
                <p class="font-bold">No hay materias inscritas disponibles</p>
                <p class="mt-1">
                    Primero registra una inscripción de materia que todavía no
                    tenga seguimiento académico.
                </p>
            </div>

            <div
                v-if="docentes.length === 0"
                class="md:col-span-2 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 text-sm text-yellow-800"
            >
                <p class="font-bold">No hay docentes disponibles</p>
                <p class="mt-1">
                    Primero registra un usuario con rol docente y su detalle
                    correspondiente.
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Inscripción de materia</label
                >
                <select
                    v-model="form.inscripcion_materia_id"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Seleccione una materia inscrita</option>
                    <option
                        v-for="item in inscripcionMaterias"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{
                            nombreCompleto(
                                item.inscripcion?.alumnoDetalle?.user,
                            )
                        }}
                        /
                        {{ item.inscripcion?.ofertaAcademica?.carrera?.nombre }}
                        / {{ item.carreraMateria?.materia?.codigo }} -
                        {{ item.carreraMateria?.materia?.nombre }}
                    </option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Docente</label
                >
                <select
                    v-model="form.docente_detalle_id"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Seleccione un docente</option>
                    <option
                        v-for="docente in docentes"
                        :key="docente.id"
                        :value="docente.id"
                    >
                        {{ docente.codigo || "SIN-COD" }} -
                        {{ nombreCompleto(docente.user) }} -
                        {{ docente.especialidad || "Sin especialidad" }}
                    </option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Nota final</label
                >
                <input
                    v-model="form.nota_final"
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    placeholder="Ejemplo: 85.50"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Porcentaje de asistencia</label
                >
                <input
                    v-model="form.porcentaje_asistencia"
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    placeholder="Ejemplo: 90.00"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Estado académico</label
                >
                <select
                    v-model="form.estado_academico"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >
                    <option
                        v-for="estado in estadosAcademicos"
                        :key="estado"
                        :value="estado"
                    >
                        {{ estado }}
                    </option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Fecha de registro</label
                >
                <input
                    v-model="form.fecha_registro"
                    type="date"
                    required
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Observación</label
                >
                <textarea
                    v-model="form.observacion"
                    rows="4"
                    placeholder="Observaciones académicas del estudiante"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                ></textarea>
            </div>

            <div
                class="flex flex-wrap gap-3 border-t border-slate-200 pt-6 md:col-span-2"
            >
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                >
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Guardar seguimiento
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
    seguimiento: {
        type: Object,
        default: () => ({}),
    },
    inscripcionMaterias: {
        type: Array,
        default: () => [],
    },
    docentes: {
        type: Array,
        default: () => [],
    },
    action: {
        type: String,
        default: "",
    },
    method: {
        type: String,
        default: "post",
    },
    cancelUrl: {
        type: String,
        default: "",
    },
});

const page = usePage();
const estadosAcademicos = ["Cursando", "Aprobado", "Reprobado", "Retirado"];

const buildFormData = () => ({
    inscripcion_materia_id: props.seguimiento?.inscripcion_materia_id ?? "",
    docente_detalle_id: props.seguimiento?.docente_detalle_id ?? "",
    nota_final: props.seguimiento?.nota_final ?? "",
    porcentaje_asistencia: props.seguimiento?.porcentaje_asistencia ?? "",
    estado_academico: props.seguimiento?.estado_academico ?? "Cursando",
    fecha_registro:
        props.seguimiento?.fecha_registro ??
        new Date().toISOString().slice(0, 10),
    observacion: props.seguimiento?.observacion ?? "",
});

const form = useForm(buildFormData());

watch(
    () => props.seguimiento,
    () => {
        form.reset();
        Object.assign(form, buildFormData());
    },
    { deep: true },
);

const errores = computed(() => {
    const errors = page.props.errors ?? {};
    return Object.values(errors).flat().filter(Boolean);
});

function submit() {
    if (props.method === "put") {
        form.put(props.action);
        return;
    }

    form.post(props.action);
}

function nombreCompleto(datos) {
    if (!datos) {
        return "Sin nombre";
    }

    const completo = `${datos.nombres ?? ""} ${datos.apellidos ?? ""}`.trim();
    return completo || "Sin nombre";
}
</script>
