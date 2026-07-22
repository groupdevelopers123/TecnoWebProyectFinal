<template>
    <div class="p-6">
        <h1 class="mb-6 text-2xl font-black text-slate-900">
            Cuotas del crédito
        </h1>
        <ModalCuotas :credito="credito" :cuotas="cuotas" />
    </div>
</template>
<script setup>
import ModalCuotas from "./ModalCuotas.vue";
defineProps({
    credito: { type: Object, default: () => ({}) },
    cuotas: { type: Array, default: () => [] },
});
</script>
