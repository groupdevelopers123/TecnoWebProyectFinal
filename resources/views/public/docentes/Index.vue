<template>
    <PublicLayout>
        <Head title="Docentes" />
        <main class="min-h-screen bg-slate-50 px-5 py-12">
            <div class="mx-auto max-w-7xl">
                <Link :href="route('welcome')" class="font-bold text-blue-600"
                    >← Inicio</Link
                >
                <h1 class="mt-8 text-4xl font-black">Nuestros docentes</h1>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <template v-if="docentes.length">
                        <DocenteCard
                            v-for="docente in docentes"
                            :key="docente.id"
                            :docente="docente"
                        />
                    </template>
                    <p v-else class="text-slate-500">
                        No hay docentes registrados.
                    </p>
                </div>
            </div>
        </main>
    </PublicLayout>
</template>
<script setup>
import { Head, Link } from "@inertiajs/vue3";
import PublicLayout from "../../layouts/PublicLayout.vue";
import DocenteCard from "./DocenteCard.vue";
defineProps({ docentes: { type: Array, default: () => [] } });
</script>
