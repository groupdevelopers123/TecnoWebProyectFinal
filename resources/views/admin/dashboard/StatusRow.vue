<template>
    <div class="rounded-2xl bg-slate-50 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-500">{{ label }}</p>
                <p class="mt-1 text-2xl font-black text-slate-900">
                    {{ value }}
                </p>
            </div>
            <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-200 text-slate-600"
            >
                <i class="fa-solid" :class="icon"></i>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], default: "" },
    icon: { type: String, required: true },
});
</script>
