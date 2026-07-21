<template>
    <Head title="Detalle de Inscripción" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-3xl bg-cyan-100 text-2xl text-cyan-700"
                >
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900">
                        {{ nombreAlumno }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Inscripción #{{ inscripcion.id }}
                    </p>
                </div>
            </div>

            <div class="flex gap-3">
                <a
                    :href="editUrl"
                    class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
                >
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                    Editar
                </a>

                <a
                    :href="indexUrl"
                    class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
                >
                    Volver
                </a>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                <p class="text-xs font-bold uppercase text-slate-400">Alumno</p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ inscripcion.alumno?.codigo || "SIN-COD" }} -
                    {{ nombreAlumno }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Oferta académica
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ inscripcion.oferta_academica?.nombre }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Carrera
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ inscripcion.oferta_academica?.carrera?.codigo }} -
                    {{ inscripcion.oferta_academica?.carrera?.nombre }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Periodo académico
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{
                        inscripcion.oferta_academica?.periodo_academico?.nombre
                    }}
                    -
                    {{
                        inscripcion.oferta_academica?.periodo_academico?.gestion
                    }}
                </p>
            </div>

            <div class="rounded-2xl bg-cyan-50 p-4">
                <p class="text-xs font-bold uppercase text-cyan-500">
                    Periodo número
                </p>
                <p class="mt-1 font-bold text-cyan-700">
                    {{ inscripcion.periodo_numero }}
                </p>
            </div>

            <div class="rounded-2xl bg-cyan-50 p-4">
                <p class="text-xs font-bold uppercase text-cyan-500">
                    Fecha de inscripción
                </p>
                <p class="mt-1 font-bold text-cyan-700">
                    {{ formatDate(inscripcion.fecha_inscripcion) }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Registrado por
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ nombreRegistro }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Observación
                </p>
                <p class="mt-1 text-sm font-medium text-slate-700">
                    {{
                        inscripcion.observacion || "Sin observación registrada."
                    }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const inscripcion = computed(() => page.props.inscripcion || {});
const nombreAlumno = computed(() => {
    const nombres = inscripcion.value.alumno?.nombres || "";
    const apellidos = inscripcion.value.alumno?.apellidos || "";
    const completo = `${nombres} ${apellidos}`.trim();

    return completo || "Alumno sin usuario";
});
const nombreRegistro = computed(() => {
    const nombres = inscripcion.value.usuario_registro?.nombres || "";
    const apellidos = inscripcion.value.usuario_registro?.apellidos || "";
    const completo = `${nombres} ${apellidos}`.trim();

    return completo || "No registrado";
});
const editUrl = computed(() =>
    inscripcion.value?.id
        ? route("admin.inscripciones.edit", inscripcion.value.id)
        : route("admin.inscripciones.index"),
);
const indexUrl = computed(() => route("admin.inscripciones.index"));

function formatDate(value) {
    if (!value) {
        return "-";
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat("es-BO").format(date);
}
</script>
