<template>
    <Head title="Detalle de Aula" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-2xl text-blue-700"
                >
                    <i class="fa-solid fa-school"></i>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900">
                        {{ aula.nombre }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Código: {{ aula.codigo }}
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
                    >Volver</a
                >
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Código</p>
                <p class="mt-1 font-bold text-slate-800">{{ aula.codigo }}</p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Nombre</p>
                <p class="mt-1 font-bold text-slate-800">{{ aula.nombre }}</p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Ubicación
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ aula.ubicacion ?? "No registrada" }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Piso</p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ aula.piso ?? "No registrado" }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Capacidad
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ aula.capacidad }} estudiantes
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Estado</p>
                <p
                    class="mt-1 font-bold"
                    :class="aula.disponible ? 'text-green-700' : 'text-red-700'"
                >
                    {{ aula.estadoTexto }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Largo</p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ aula.largo ? aula.largo + " m" : "No registrado" }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Ancho</p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ aula.ancho ? aula.ancho + " m" : "No registrado" }}
                </p>
            </div>

            <div class="rounded-2xl bg-blue-50 p-4 md:col-span-2">
                <p class="text-xs font-bold uppercase text-blue-500">
                    Área aproximada
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{
                        aula.area
                            ? Number(aula.area).toFixed(2) + " m²"
                            : "No calculada"
                    }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Registrado por
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{
                        aula.usuarioRegistro?.nombreCompleto ?? "No disponible"
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
const aula = computed(() => page.props.aula || {});

const editUrl = computed(() =>
    aula.value && aula.value.id
        ? route("admin.aulas.edit", aula.value.id)
        : route("admin.aulas.index"),
);
const indexUrl = computed(() => route("admin.aulas.index"));
</script>
