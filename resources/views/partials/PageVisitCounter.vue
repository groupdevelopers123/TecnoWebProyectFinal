<script setup>
import { computed, onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    apiEndpoint: {
        type: String,
        default: "/page-visits",
    },
    compact: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const visits = ref(0);
const loading = ref(true);
const error = ref(false);

const badgeClasses = computed(() =>
    props.compact
        ? "inline-flex items-center justify-center rounded-full bg-white/90 px-3 py-1 text-sm font-semibold text-slate-900 shadow-sm"
        : "inline-flex items-center justify-center rounded-full bg-white/90 px-3 py-1 text-base font-semibold text-slate-900 shadow-sm",
);

const displayCount = computed(() => {
    if (loading.value) {
        return "...";
    }

    if (error.value) {
        return "-";
    }

    return visits.value;
});

const loadVisitCount = async () => {
    loading.value = true;
    error.value = false;

    try {
        const response = await axios.post(props.apiEndpoint, {
            page: page.url,
        });

        visits.value = response.data.visits ?? 0;
    } catch (err) {
        error.value = true;
        console.error(err);
    } finally {
        loading.value = false;
    }
};

onMounted(loadVisitCount);
</script>

<template>
    <span :class="badgeClasses"> Visitas: {{ displayCount }} </span>
</template>
