<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <h3 class="text-lg font-black text-slate-900">
                    Datos del horario
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Selecciona carrera-materia, periodo, aula, docente, día y
                    horas.
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Carrera y materia</label
                >
                <select
                    v-model="form.carrera_materia_id"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    required
                >
                    <option value="">Seleccione una asignación</option>
                    <option
                        v-for="item in carreraMaterias"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.carrera.nombre }} - {{ item.materia.nombre }}
                    </option>
                </select>
                <p
                    v-if="form.errors.carrera_materia_id"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.carrera_materia_id }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Periodo académico</label
                >
                <select
                    v-model="form.periodo_academico_id"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    required
                >
                    <option value="">Seleccione un periodo</option>
                    <option
                        v-for="periodo in periodos"
                        :key="periodo.id"
                        :value="periodo.id"
                    >
                        {{ periodo.nombre }}
                    </option>
                </select>
                <p
                    v-if="form.errors.periodo_academico_id"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.periodo_academico_id }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Aula</label
                >
                <select
                    v-model="form.aula_id"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    required
                >
                    <option value="">Seleccione un aula</option>
                    <option
                        v-for="aula in aulas"
                        :key="aula.id"
                        :value="aula.id"
                    >
                        {{ aula.nombre }} - {{ aula.codigo }} / Capacidad:
                        {{ aula.capacidad }}
                    </option>
                </select>
                <p
                    v-if="form.errors.aula_id"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.aula_id }}
                </p>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Docente</label
                >
                <select
                    v-model="form.docente_detalle_id"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    required
                >
                    <option value="">Seleccione un docente</option>
                    <option
                        v-for="docente in docentes"
                        :key="docente.id"
                        :value="docente.id"
                    >
                        {{ docente.nombre }} -
                        {{ docente.especialidad || "Sin especialidad" }}
                    </option>
                </select>
                <p
                    v-if="form.errors.docente_detalle_id"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.docente_detalle_id }}
                </p>
            </div>

            <div class="border-t border-slate-200 pt-6 md:col-span-2">
                <h3 class="text-lg font-black text-slate-900">Día y horario</h3>
                <p class="mt-1 text-sm text-slate-500">
                    El sistema validará que no exista cruce de aula ni docente.
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Día</label
                >
                <select
                    v-model="form.dia"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    required
                >
                    <option value="">Seleccione un día</option>
                    <option v-for="dia in dias" :key="dia" :value="dia">
                        {{ dia }}
                    </option>
                </select>
                <p
                    v-if="form.errors.dia"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.dia }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Turno</label
                >
                <select
                    v-model="form.turno"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    required
                >
                    <option value="">Seleccione un turno</option>
                    <option v-for="turno in turnos" :key="turno" :value="turno">
                        {{ turno }}
                    </option>
                </select>
                <p
                    v-if="form.errors.turno"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.turno }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Hora inicio</label
                >
                <input
                    v-model="form.hora_inicio"
                    type="time"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    required
                />
                <p
                    v-if="form.errors.hora_inicio"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.hora_inicio }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Hora fin</label
                >
                <input
                    v-model="form.hora_fin"
                    type="time"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    required
                />
                <p
                    v-if="form.errors.hora_fin"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.hora_fin }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700"
                    >Estado</label
                >
                <select
                    v-model="form.estado"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    required
                >
                    <option :value="1">Activo</option>
                    <option :value="0">Inactivo</option>
                </select>
                <p
                    v-if="form.errors.estado"
                    class="mt-2 text-sm font-medium text-red-600"
                >
                    {{ form.errors.estado }}
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
                    Guardar horario
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
    horario: { type: Object, default: () => ({}) },
    action: { type: String, required: true },
    method: { type: String, default: "post" },
    cancelUrl: { type: String, required: true },
    carreraMaterias: { type: Array, default: () => [] },
    periodos: { type: Array, default: () => [] },
    aulas: { type: Array, default: () => [] },
    docentes: { type: Array, default: () => [] },
});

const dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
const turnos = ["Mañana", "Tarde", "Noche"];

const form = useForm({
    carrera_materia_id: props.horario.carrera_materia_id ?? "",
    periodo_academico_id: props.horario.periodo_academico_id ?? "",
    aula_id: props.horario.aula_id ?? "",
    docente_detalle_id: props.horario.docente_detalle_id ?? "",
    dia: props.horario.dia ?? "",
    hora_inicio: props.horario.hora_inicio ?? "",
    hora_fin: props.horario.hora_fin ?? "",
    turno: props.horario.turno ?? "",
    estado: props.horario.estado === false ? 0 : (props.horario.estado ?? 1),
});

watch(
    () => props.horario,
    (horario) => {
        form.carrera_materia_id = horario.carrera_materia_id ?? "";
        form.periodo_academico_id = horario.periodo_academico_id ?? "";
        form.aula_id = horario.aula_id ?? "";
        form.docente_detalle_id = horario.docente_detalle_id ?? "";
        form.dia = horario.dia ?? "";
        form.hora_inicio = horario.hora_inicio ?? "";
        form.hora_fin = horario.hora_fin ?? "";
        form.turno = horario.turno ?? "";
        form.estado = horario.estado === false ? 0 : (horario.estado ?? 1);
    },
    { deep: true },
);

function submit() {
    const payload = {
        ...form.data(),
        estado: Number(form.estado),
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
