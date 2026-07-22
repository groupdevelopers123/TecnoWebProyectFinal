<template>
    <div
        class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
    >
        <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-slate-500">{{ label }}</p>
            <div
                class="flex h-11 w-11 items-center justify-center rounded-2xl"
                :class="toneClasses"
            >
                <i class="fa-solid" :class="icon"></i>
            </div>
        </div>
        <p class="mt-4 text-4xl font-black text-slate-900">
            {{ formattedValue }}
        </p>
        <p class="mt-2 text-sm text-slate-500">{{ description }}</p>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    label: { type: String, required: true },
    value: { type: [Number, String], default: 0 },
    description: { type: String, default: "" },
    icon: { type: String, required: true },
    tone: { type: String, default: "blue" },
});

const formattedValue = computed(() =>
    new Intl.NumberFormat("es-BO").format(Number(props.value ?? 0)),
);
const toneClasses = computed(
    () =>
        ({
            blue: "bg-blue-50 text-blue-600",
            emerald: "bg-emerald-50 text-emerald-600",
            violet: "bg-violet-50 text-violet-600",
            amber: "bg-amber-50 text-amber-600",
        })[props.tone] || "bg-slate-50 text-slate-600",
);
</script>
