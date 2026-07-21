<template>
    <Head title="Detalle de Usuario" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-2xl font-black text-blue-700"
                >
                    {{ (usuario.nombres || "").charAt(0).toUpperCase() }}
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900">
                        {{
                            usuario.nombre_completo ||
                            usuario.nombres + " " + usuario.apellidos
                        }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ usuario.email }}
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
                <p class="text-xs font-bold uppercase text-slate-400">CI</p>
                <p class="mt-1 font-bold text-slate-800">{{ usuario.ci }}</p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Rol</p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ usuario.role?.nombre }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Teléfono
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ usuario.telefono ?? "No registrado" }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Dirección
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ usuario.direccion ?? "No registrada" }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">
                    Fecha de nacimiento
                </p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ usuario.fecha_nacimiento ?? "No registrada" }}
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase text-slate-400">Estado</p>
                <p
                    class="mt-1 font-bold"
                    :class="usuario.estado ? 'text-green-700' : 'text-red-700'"
                >
                    {{ usuario.estado ? "Activo" : "Inactivo" }}
                </p>
            </div>
        </div>

        <div class="mt-8 border-t border-slate-200 pt-8">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-black text-slate-900">
                        Datos del rol
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Información específica asociada al rol del usuario.
                    </p>
                </div>

                <div
                    class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700"
                >
                    {{ roleLabel }}
                </div>
            </div>

            <div
                v-if="!rolDetalle"
                class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-500"
            >
                Este usuario no tiene un detalle específico cargado para el rol.
            </div>

            <div v-else class="grid gap-5 md:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Código
                    </p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{ rolDetalle.codigo ?? "No registrado" }}
                    </p>
                </div>

                <div
                    v-if="roleName === 'propietario'"
                    class="rounded-2xl bg-slate-50 p-4 md:col-span-2"
                >
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Cargo
                    </p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{ rolDetalle.cargo ?? "No registrado" }}
                    </p>
                </div>

                <template v-else-if="roleName === 'secretaria'">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase text-slate-400">
                            Turno de trabajo
                        </p>
                        <p class="mt-1 font-bold text-slate-800">
                            {{ rolDetalle.turno_trabajo ?? "No registrado" }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase text-slate-400">
                            Sueldo
                        </p>
                        <p class="mt-1 font-bold text-slate-800">
                            {{ rolDetalle.sueldo ?? "No registrado" }}
                        </p>
                    </div>
                </template>

                <template v-else-if="roleName === 'docente'">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase text-slate-400">
                            Especialidad
                        </p>
                        <p class="mt-1 font-bold text-slate-800">
                            {{ rolDetalle.especialidad ?? "No registrada" }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase text-slate-400">
                            Título
                        </p>
                        <p class="mt-1 font-bold text-slate-800">
                            {{ rolDetalle.titulo ?? "No registrado" }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                        <p class="text-xs font-bold uppercase text-slate-400">
                            Registro profesional
                        </p>
                        <p class="mt-1 font-bold text-slate-800">
                            {{
                                rolDetalle.registro_profesional ??
                                "No registrado"
                            }}
                        </p>
                    </div>
                </template>

                <template v-else-if="roleName === 'alumno'">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase text-slate-400">
                            Colegio de origen
                        </p>
                        <p class="mt-1 font-bold text-slate-800">
                            {{ rolDetalle.colegio_origen ?? "No registrado" }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase text-slate-400">
                            Año de bachillerato
                        </p>
                        <p class="mt-1 font-bold text-slate-800">
                            {{
                                rolDetalle.anio_bachillerato ?? "No registrado"
                            }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                        <p class="text-xs font-bold uppercase text-slate-400">
                            Estado académico
                        </p>
                        <p class="mt-1 font-bold text-slate-800">
                            {{ rolDetalle.estado_academico ?? "No registrado" }}
                        </p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const usuario = page.props.usuario || {};
const roleName = computed(() =>
    String(usuario.role?.nombre ?? "").toLowerCase(),
);
const roleLabel = computed(() => usuario.role?.nombre ?? "Sin rol");
const rolDetalle = computed(() => {
    if (roleName.value === "propietario") {
        return (
            usuario.propietario_detalle || usuario.propietarioDetalle || null
        );
    }

    if (roleName.value === "secretaria") {
        return usuario.secretaria_detalle || usuario.secretariaDetalle || null;
    }

    if (roleName.value === "docente") {
        return usuario.docente_detalle || usuario.docenteDetalle || null;
    }

    if (roleName.value === "alumno") {
        return usuario.alumno_detalle || usuario.alumnoDetalle || null;
    }

    return null;
});
const editUrl = computed(() =>
    usuario && usuario.id
        ? route("admin.usuarios.edit", usuario.id)
        : route("admin.usuarios.index"),
);
const indexUrl = computed(() => route("admin.usuarios.index"));
</script>
