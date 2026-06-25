<script setup>
import { Head } from "@inertiajs/vue3";
import HeaderDocente from "@/views/partials/headerDocente.vue";
import PageVisitCounter from "@/views/partials/PageVisitCounter.vue";
</script>

<template>
    <Head title="Panel del Docente" />

    <div class="min-h-screen bg-slate-100">
        <HeaderDocente />

        <main class="px-5 pb-10 pt-24 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div
                    class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h1 class="text-2xl font-black text-slate-900">
                                Panel del Docente
                            </h1>
                            <p class="mt-1 text-sm text-slate-500">
                                Bienvenido al panel del docente. Aquí
                                encontrarás tu información y accesos rápidos.
                            </p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-3xl bg-blue-50 p-5">
                                <p class="text-sm font-black text-blue-600">
                                    Clases asignadas
                                </p>
                                <p
                                    class="mt-3 text-4xl font-black text-slate-900"
                                >
                                    0
                                </p>
                            </div>
                            <div class="rounded-3xl bg-emerald-50 p-5">
                                <p class="text-sm font-black text-emerald-600">
                                    Alumnos
                                </p>
                                <p
                                    class="mt-3 text-4xl font-black text-slate-900"
                                >
                                    0
                                </p>
                            </div>
                            <div class="rounded-3xl bg-amber-50 p-5">
                                <p class="text-sm font-black text-amber-600">
                                    Notificaciones
                                </p>
                                <p
                                    class="mt-3 text-4xl font-black text-slate-900"
                                >
                                    0
                                </p>
                            </div>
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
