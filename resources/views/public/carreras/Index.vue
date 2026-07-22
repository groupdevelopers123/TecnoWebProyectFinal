<template>
    <PublicLayout>
        <Head title="Carreras" />
        <main class="min-h-screen bg-slate-50 px-5 py-12">
            <div class="mx-auto max-w-7xl">
                <Link :href="route('welcome')" class="font-bold text-blue-600"
                    >← Inicio</Link
                >
                <h1 class="mt-8 text-4xl font-black">Carreras</h1>
                <p class="mt-2 text-slate-500">
                    Conoce los programas disponibles del instituto.
                </p>
                <div class="mt-8 grid gap-5 md:grid-cols-3">
                    <template v-if="carreras.length">
                        <CarreraCard
                            v-for="carrera in carreras"
                            :key="carrera.id"
                            :carrera="carrera"
                        />
                    </template>
                    <p v-else class="text-slate-500">
                        No hay carreras disponibles.
                    </p>
                </div>
            </div>
        </main>
    </PublicLayout>
</template>
<script setup>
import { Head, Link } from "@inertiajs/vue3";
import PublicLayout from "../../layouts/PublicLayout.vue";
import CarreraCard from "./CarreraCard.vue";
defineProps({ carreras: { type: Array, default: () => [] } });
</script>
