<template>
    <Head title="Registrar Oferta Académica" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-black text-slate-900">
                Nueva oferta académica
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Completa los datos para registrar una oferta académica.
            </p>
        </div>

        <FormOfertaAcademica
            :oferta="oferta"
            :action="action"
            method="post"
            :cancelUrl="cancelUrl"
            :carreras="carreras"
            :periodos="periodos"
            :docentes="docentes"
        />
    </div>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import FormOfertaAcademica from "./FormOfertaAcademica.vue";

const page = usePage();
const oferta = computed(() => page.props.oferta || {});
const action = computed(
    () => page.props.action || route("admin.ofertas-academicas.store"),
);
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.ofertas-academicas.index"),
);
const carreras = computed(() => page.props.carreras || []);
const periodos = computed(() => page.props.periodos || []);
const docentes = computed(() => page.props.docentes || []);
</script>
