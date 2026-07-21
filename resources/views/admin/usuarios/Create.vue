<template>
    <Head title="Registrar Usuario" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">Nuevo usuario</h2>
            <p class="mt-1 text-sm text-slate-500">
                Completa los datos generales y específicos según el rol.
            </p>
        </div>

        <FormUsuario
            :roles="roles"
            :action="action"
            method="post"
            :cancelUrl="cancelUrl"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import FormUsuario from "./FormUsuario.vue";
import { computed } from "vue";

const page = usePage();
const roles = page.props.roles || [];
const action = computed(
    () => page.props.action || route("admin.usuarios.store"),
);
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.usuarios.index"),
);
</script>
