<script setup>
import { Head } from "@inertiajs/vue3";
import HeaderDocente from "@/views/partials/headerDocente.vue";
</script>

<template>
    <Head title="Mis Carreras - Docente" />

    <div class="min-h-screen bg-slate-100">
        <HeaderDocente />

        <main class="px-5 pb-10 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div
                    class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div>
                        <h1 class="text-2xl font-black text-slate-900">
                            Mis Carreras
                        </h1>
                        <p class="mt-1 text-sm text-slate-500">
                            Carreras asignadas para este período académico.
                        </p>
                    </div>

                    <div class="mt-8">
                        <div
                            class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 py-12 text-center"
                        >
                            <svg
                                class="mx-auto h-12 w-12 text-slate-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.996 10-10.747S17.5 6.253 12 6.253z"
                                />
                            </svg>
                            <h3 class="mt-4 text-lg font-bold text-slate-900">
                                Sin carreras asignadas
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Todavía no tienes carreras asignadas para este
                                período.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
