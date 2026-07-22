<template>
    <Link
        :href="href"
        class="rounded-3xl border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:shadow-lg"
    >
        <div class="flex items-start gap-4">
            <div
                class="flex h-11 w-11 items-center justify-center rounded-2xl border"
                :class="accent"
            >
                <i class="fa-solid" :class="icon"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900">{{ title }}</h3>
                <p class="mt-2 text-sm text-slate-500">{{ description }}</p>
            </div>
        </div>
    </Link>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    title: { type: String, required: true },
    description: { type: String, required: true },
    href: { type: String, required: true },
    icon: { type: String, required: true },
    accent: { type: String, default: "" },
});
</script>
