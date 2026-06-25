<script setup>
import { Head } from "@inertiajs/vue3";
import HeaderAlumno from "@/views/partials/headerAlumno.vue";
import PageVisitCounter from "@/views/partials/PageVisitCounter.vue";
</script>

<template>
    <Head title="Panel del Alumno" />

    <div class="min-h-screen bg-slate-100">
        <HeaderAlumno />

        <main class="px-5 pb-10 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div
                    class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-xl text-blue-700"
                        >
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>

                        <div>
                            <h1 class="text-2xl font-black text-slate-900">
                                Panel del Alumno
                            </h1>

                            <p class="mt-1 text-sm text-slate-500">
                                La vista Home y el encabezado del alumno están
                                funcionando correctamente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer
            class="fixed left-4 bottom-4 z-50 bg-transparent p-0 sm:left-6 sm:bottom-6"
        >
            <PageVisitCounter compact />
        </footer>
    </div>
</template>
