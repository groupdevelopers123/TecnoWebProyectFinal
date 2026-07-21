<template>
    <Head title="Editar Oferta Académica" />

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
            class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-xl font-black text-slate-900">
                    Editar oferta académica
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Actualiza la información de la oferta seleccionada.
                </p>
            </div>

            <a
                :href="showUrl"
                class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200"
            >
                <i class="fa-solid fa-eye text-xs"></i>
                Ver detalle
            </a>
        </div>

        <FormOfertaAcademica
            :oferta="oferta"
            :action="action"
            method="put"
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
const action = computed(() => page.props.action || "#");
const cancelUrl = computed(
    () => page.props.cancelUrl || route("admin.ofertas-academicas.index"),
);
const carreras = computed(() => page.props.carreras || []);
const periodos = computed(() => page.props.periodos || []);
const docentes = computed(() => page.props.docentes || []);
const showUrl = computed(() =>
    oferta.value?.id
        ? route("admin.ofertas-academicas.show", oferta.value.id)
        : route("admin.ofertas-academicas.index"),
);
</script>
