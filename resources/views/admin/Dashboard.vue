<template>
    <div class="space-y-6">
        <div
            class="overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 p-8 text-white shadow-xl"
        >
            <div
                class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between"
            >
                <div class="max-w-2xl">
                    <p
                        class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-200"
                    >
                        Panel principal
                    </p>
                    <h2 class="mt-3 text-3xl font-black">
                        Bienvenido al centro de control del instituto
                    </h2>
                    <p class="mt-3 text-sm text-slate-300 sm:text-base">
                        Consulta en un solo lugar el estado operativo del
                        sistema y accede rápidamente a los módulos clave del
                        proceso académico.
                    </p>
                </div>
                <div
                    class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur"
                >
                    <p class="text-sm font-semibold text-blue-100">
                        Usuarios activos
                    </p>
                    <p class="mt-2 text-3xl font-black">
                        {{ formatNumber(usuariosActivos) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard
                label="Usuarios registrados"
                :value="totalUsuarios"
                description="Total en el sistema"
                icon="fa-users"
                tone="blue"
            />
            <StatCard
                label="Aulas disponibles"
                :value="aulasDisponibles"
                :description="`de ${formatNumber(totalAulas)} registradas`"
                icon="fa-school"
                tone="emerald"
            />
            <StatCard
                label="Inscripciones"
                :value="totalInscripciones"
                description="Procesadas en el sistema"
                icon="fa-clipboard-list"
                tone="violet"
            />
            <StatCard
                label="Pagos pendientes"
                :value="pagosPendientes"
                description="Cuotas y pagos por revisar"
                icon="fa-file-invoice-dollar"
                tone="amber"
            />
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
            <section
                class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                <div class="mb-6">
                    <h2 class="text-xl font-black text-slate-900">
                        Accesos rápidos
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Módulos principales para la gestión diaria del
                        instituto.
                    </p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <QuickLink
                        v-for="module in modules"
                        :key="module.title"
                        v-bind="module"
                    />
                </div>
            </section>

            <section
                class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                <div class="mb-5">
                    <h2 class="text-xl font-black text-slate-900">
                        Estado del sistema
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Resumen operativo del día.
                    </p>
                </div>
                <div class="space-y-4">
                    <StatusRow
                        label="Aulas disponibles"
                        :value="`${formatNumber(aulasDisponibles)}/${formatNumber(totalAulas)}`"
                        icon="fa-door-open"
                    />
                    <StatusRow
                        label="Inscripciones cargadas"
                        :value="formatNumber(totalInscripciones)"
                        icon="fa-clipboard-check"
                    />
                    <StatusRow
                        label="Pendientes de pago"
                        :value="formatNumber(pagosPendientes)"
                        icon="fa-bell"
                    />
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import QuickLink from "./dashboard/QuickLink.vue";
import StatCard from "./dashboard/StatCard.vue";
import StatusRow from "./dashboard/StatusRow.vue";

const props = defineProps({
    totalUsuarios: { type: Number, default: 0 },
    usuariosActivos: { type: Number, default: 0 },
    totalAulas: { type: Number, default: 0 },
    aulasDisponibles: { type: Number, default: 0 },
    totalInscripciones: { type: Number, default: 0 },
    pagosPendientes: { type: Number, default: 0 },
});

const formatNumber = (value) =>
    new Intl.NumberFormat("es-BO").format(Number(value ?? 0));

const modules = computed(() => [
    {
        title: "Usuarios",
        description: "Gestiona propietarios, secretarias, docentes y alumnos.",
        href: route("admin.usuarios.index"),
        icon: "fa-users",
        accent: "border-blue-100 bg-blue-50 text-blue-600",
    },
    {
        title: "Aulas",
        description:
            "Revisa disponibilidad, capacidad y ubicación de cada aula.",
        href: route("admin.aulas.index"),
        icon: "fa-school",
        accent: "border-emerald-100 bg-emerald-50 text-emerald-600",
    },
    {
        title: "Horarios",
        description: "Administra periodos, carreras, materias y horarios.",
        href: route("admin.periodos-academicos.index"),
        icon: "fa-calendar-week",
        accent: "border-violet-100 bg-violet-50 text-violet-600",
    },
    {
        title: "Inscripciones",
        description: "Revisa y gestiona las inscripciones del periodo actual.",
        href: route("admin.inscripciones.index"),
        icon: "fa-clipboard-list",
        accent: "border-slate-200 bg-slate-50 text-slate-600",
    },
    {
        title: "Seguimiento académico",
        description: "Monitorea el avance y rendimiento de los estudiantes.",
        href: route("admin.seguimientos-academicos.index"),
        icon: "fa-chart-line",
        accent: "border-amber-100 bg-amber-50 text-amber-600",
    },
    {
        title: "Pagos",
        description: "Controla pagos al contado, cuotas y créditos.",
        href: route("admin.pago-contados.index"),
        icon: "fa-file-invoice-dollar",
        accent: "border-rose-100 bg-rose-50 text-rose-600",
    },
]);
</script>
