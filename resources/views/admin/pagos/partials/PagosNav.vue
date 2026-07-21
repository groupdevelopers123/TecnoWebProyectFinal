<script setup>
import { Link } from "@inertiajs/vue3";

function isActive(routePrefix) {
    const current = route().current();
    return current === routePrefix || current?.startsWith(`${routePrefix}.`);
}
</script>

<template>
    <div
        class="mb-6 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm"
    >
        <div class="grid grid-cols-2 gap-2 md:grid-cols-4">
            <Link
                :href="route('admin.concepto-pagos.index')"
                class="flex items-center justify-center gap-1.5 rounded-xl px-2.5 py-2 text-xs font-bold transition"
                :class="
                    isActive('admin.concepto-pagos')
                        ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20'
                        : 'text-slate-600 hover:bg-slate-100'
                "
            >
                <i class="fa-solid fa-tags text-xs"></i>
                <span>Conceptos</span>
            </Link>

            <Link
                :href="route('admin.pago-contados.index')"
                class="flex items-center justify-center gap-1.5 rounded-xl px-2.5 py-2 text-xs font-bold transition"
                :class="
                    isActive('admin.pago-contados')
                        ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20'
                        : 'text-slate-600 hover:bg-slate-100'
                "
            >
                <i class="fa-solid fa-money-bill-wave text-xs"></i>
                <span>Contado</span>
            </Link>

            <Link
                :href="route('admin.creditos.index')"
                class="flex items-center justify-center gap-1.5 rounded-xl px-2.5 py-2 text-xs font-bold transition"
                :class="
                    isActive('admin.creditos')
                        ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20'
                        : 'text-slate-600 hover:bg-slate-100'
                "
            >
                <i class="fa-solid fa-hand-holding-dollar text-xs"></i>
                <span>Créditos</span>
            </Link>

            <Link
                :href="route('admin.pago-cuotas.index')"
                class="flex items-center justify-center gap-1.5 rounded-xl px-2.5 py-2 text-xs font-bold transition"
                :class="
                    isActive('admin.pago-cuotas')
                        ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20'
                        : 'text-slate-600 hover:bg-slate-100'
                "
            >
                <i class="fa-solid fa-calendar-check text-xs"></i>
                <span>Cuotas</span>
            </Link>
        </div>
    </div>
</template>
