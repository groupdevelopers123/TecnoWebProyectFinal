<template>
    <div>
        <div
            class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center"
        >
            <div>
                <h2 class="text-2xl font-black">Materias inscritas</h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ fullName(inscripcion.alumno_detalle?.user) }} ·
                    {{ inscripcion.oferta_academica?.carrera?.nombre }}
                </p>
            </div>
            <Link
                :href="
                    route('admin.inscripciones.materias.create', inscripcion.id)
                "
                class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white"
                >Agregar materia</Link
            >
        </div>
        <div
            class="overflow-hidden rounded-3xl border border-slate-200 bg-white"
        >
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs uppercase text-slate-500"
                        >
                            Materia
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs uppercase text-slate-500"
                        >
                            Periodo
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs uppercase text-slate-500"
                        >
                            Estado
                        </th>
                        <th
                            class="px-6 py-4 text-right text-xs uppercase text-slate-500"
                        >
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr
                        v-for="detail in inscripcion.inscripcion_materias"
                        :key="detail.id"
                    >
                        <td class="px-6 py-4 font-bold">
                            {{ detail.carrera_materia?.materia?.nombre
                            }}<small class="block font-normal text-slate-500">{{
                                detail.carrera_materia?.materia?.codigo
                            }}</small>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold">Periodo</div>
                            <div class="mt-1 text-lg font-medium">
                                {{
                                    detail.carrera_materia?.periodo_numero ??
                                    "-"
                                }}
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ detail.estado }}</td>
                        <td class="px-6 py-4 text-right">
                            <Link
                                :href="
                                    route('admin.inscripciones.materias.edit', [
                                        inscripcion.id,
                                        detail.id,
                                    ])
                                "
                                title="Editar materia inscrita"
                                aria-label="Editar materia inscrita"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:bg-blue-100"
                                ><i
                                    class="fa-solid fa-pen-to-square text-sm"
                                ></i></Link
                            ><button
                                type="button"
                                title="Retirar materia inscrita"
                                aria-label="Retirar materia inscrita"
                                class="ml-2 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700 transition hover:bg-red-100"
                                @click="remove(detail)"
                            >
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!inscripcion.inscripcion_materias?.length">
                        <td
                            colspan="4"
                            class="px-6 py-12 text-center text-slate-500"
                        >
                            Esta inscripción todavía no tiene materias
                            registradas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script setup>
import { Link, router } from "@inertiajs/vue3";
const props = defineProps({ inscripcion: { type: Object, required: true } });
const fullName = (u) =>
    `${u?.nombres ?? ""} ${u?.apellidos ?? ""}`.trim() || "Alumno sin usuario";
const remove = (detail) => {
    if (confirm("¿Está seguro de retirar esta materia de la inscripción?"))
        router.delete(
            route("admin.inscripciones.materias.destroy", [
                props.inscripcion.id,
                detail.id,
            ]),
        );
};
</script>
