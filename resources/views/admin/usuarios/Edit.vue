<template>
    <Head title="Editar Usuario" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Editar usuario
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Modifica solo los datos necesarios.
                </p>
            </div>

            <a
                :href="showUrl"
                class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
            >
                Ver detalle
            </a>
        </div>

        <FormUsuario
            :usuario="usuario"
            :roles="roles"
            :action="action"
            method="put"
            :cancelUrl="cancelUrl"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import FormUsuario from "./FormUsuario.vue";
import { computed } from "vue";

const page = usePage();
const usuario = page.props.usuario || {};
const roles = page.props.roles || [];
const action = computed(() => page.props.action || "#");
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.usuarios.index"),
);
const showUrl = computed(() =>
    usuario && usuario.id
        ? route("admin.usuarios.show", usuario.id)
        : route("admin.usuarios.index"),
);
</script>
