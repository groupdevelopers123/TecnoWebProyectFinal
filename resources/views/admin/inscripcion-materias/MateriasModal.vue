<template>
    <div class="space-y-6">
        <div
            class="flex flex-col gap-4 rounded-3xl bg-slate-50 p-5 md:flex-row md:items-center md:justify-between"
        >
            <div>
                <p
                    class="text-xs font-bold uppercase tracking-wider text-slate-400"
                >
                    Alumno
                </p>
                <p class="mt-1 font-black text-slate-900">{{ nombreAlumno }}</p>
                <p class="mt-1 text-sm text-slate-500">
                    {{ inscripcion.oferta_academica?.carrera?.nombre }}
                </p>
            </div>
            <button
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700"
                @click="startCreate"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                Agregar materia
            </button>
        </div>

        <form
            v-if="formOpen"
            class="rounded-3xl border border-slate-200 bg-white p-5"
            @submit.prevent="submit"
        >
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900">
                        {{
                            editingId
                                ? "Editar materia inscrita"
                                : "Agregar materia"
                        }}
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        {{
                            editingId
                                ? "Actualiza el estado académico."
                                : "Selecciona una materia disponible para esta carrera."
                        }}
                    </p>
                </div>
                <button
                    type="button"
                    class="text-sm font-bold text-slate-500 hover:text-slate-900"
                    @click="cancelForm"
                >
                    Cancelar
                </button>
            </div>

            <label
                v-if="!editingId"
                class="block text-sm font-bold text-slate-700"
            >
                Materia
                <select
                    v-model="form.carrera_materia_id"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    required
                >
                    <option value="">Selecciona una materia</option>
                    <option
                        v-for="item in inscripcion.materias_disponibles || []"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.materia?.codigo }} -
                        {{ item.materia?.nombre }} (Periodo
                        {{ item.periodo_numero ?? "-" }})
                    </option>
                </select>
                <span
                    v-if="form.errors.carrera_materia_id"
                    class="mt-1 block text-xs text-red-600"
                    >{{ form.errors.carrera_materia_id }}</span
                >
            </label>

            <div
                v-else
                class="mb-4 rounded-2xl bg-slate-50 p-4 text-sm font-bold text-slate-800"
            >
                {{ editingMateria?.materia?.codigo }} -
                {{ editingMateria?.materia?.nombre }}
            </div>

            <label class="mt-4 block text-sm font-bold text-slate-700">
                Estado
                <select
                    v-model="form.estado"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    required
                >
                    <option value="Cursando">Cursando</option>
                    <option value="Aprobada">Aprobada</option>
                    <option value="Reprobada">Reprobada</option>
                    <option value="Retirada">Retirada</option>
                </select>
                <span
                    v-if="form.errors.estado"
                    class="mt-1 block text-xs text-red-600"
                    >{{ form.errors.estado }}</span
                >
            </label>

            <button
                type="submit"
                :disabled="form.processing"
                class="mt-5 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800 disabled:opacity-60"
            >
                {{
                    form.processing
                        ? "Guardando..."
                        : editingId
                          ? "Actualizar estado"
                          : "Guardar materia"
                }}
            </button>
        </form>

        <div
            class="overflow-hidden rounded-3xl border border-slate-200 bg-white"
        >
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                            >
                                Materia
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                            >
                                Periodo
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                            >
                                Estado
                            </th>
                            <th
                                class="px-5 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-500"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="detail in inscripcion.inscripcion_materias ||
                            []"
                            :key="detail.id"
                            class="hover:bg-slate-50"
                        >
                            <td
                                class="px-5 py-4 text-sm font-bold text-slate-900"
                            >
                                {{ detail.materia?.codigo }} -
                                {{ detail.materia?.nombre }}
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ detail.periodo_numero ?? "-" }}
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-bold"
                                    :class="statusClass(detail.estado)"
                                    >{{ detail.estado }}</span
                                >
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button
                                    type="button"
                                    title="Editar materia inscrita"
                                    aria-label="Editar materia inscrita"
                                    class="mr-2 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:bg-blue-100"
                                    @click="startEdit(detail)"
                                >
                                    <i
                                        class="fa-solid fa-pen-to-square text-sm"
                                    ></i>
                                </button>
                                <button
                                    type="button"
                                    title="Retirar materia inscrita"
                                    aria-label="Retirar materia inscrita"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:bg-red-100"
                                    @click="remove(detail)"
                                >
                                    <i
                                        class="fa-solid fa-trash-can text-sm"
                                    ></i>
                                </button>
                            </td>
                        </tr>
                        <tr
                            v-if="
                                !(inscripcion.inscripcion_materias || []).length
                            "
                        >
                            <td
                                colspan="4"
                                class="px-6 py-12 text-center text-sm text-slate-500"
                            >
                                Esta inscripción todavía no tiene materias
                                registradas.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";

const props = defineProps({ inscripcion: { type: Object, required: true } });
const formOpen = ref(false);
const editingId = ref(null);
const editingMateria = ref(null);
const form = useForm({ carrera_materia_id: "", estado: "Cursando" });

const nombreAlumno = computed(() => {
    const alumno =
        props.inscripcion.alumno || props.inscripcion.alumno_detalle?.user;
    return (
        `${alumno?.nombres ?? ""} ${alumno?.apellidos ?? ""}`.trim() ||
        "Alumno sin usuario"
    );
});

function startCreate() {
    editingId.value = null;
    editingMateria.value = null;
    form.reset();
    form.clearErrors();
    form.estado = "Cursando";
    formOpen.value = true;
}

function startEdit(detail) {
    editingId.value = detail.id;
    editingMateria.value = detail;
    form.carrera_materia_id = detail.carrera_materia_id;
    form.estado = detail.estado;
    form.clearErrors();
    formOpen.value = true;
}

function cancelForm() {
    formOpen.value = false;
    editingId.value = null;
    editingMateria.value = null;
    form.reset();
    form.clearErrors();
}

function submit() {
    if (editingId.value) {
        form.put(
            route("admin.inscripciones.materias.update", [
                props.inscripcion.id,
                editingId.value,
            ]),
            {
                preserveScroll: true,
                onSuccess: cancelForm,
            },
        );
        return;
    }

    form.post(
        route("admin.inscripciones.materias.store", props.inscripcion.id),
        {
            preserveScroll: true,
            onSuccess: cancelForm,
        },
    );
}

function remove(detail) {
    if (
        !window.confirm(
            "¿Está seguro de retirar esta materia de la inscripción?",
        )
    ) {
        return;
    }

    router.delete(
        route("admin.inscripciones.materias.destroy", [
            props.inscripcion.id,
            detail.id,
        ]),
        {
            preserveScroll: true,
        },
    );
}

function statusClass(status) {
    return (
        {
            Cursando: "bg-blue-50 text-blue-700",
            Aprobada: "bg-green-50 text-green-700",
            Reprobada: "bg-red-50 text-red-700",
            Retirada: "bg-yellow-50 text-yellow-700",
        }[status] || "bg-slate-50 text-slate-700"
    );
}
</script>
