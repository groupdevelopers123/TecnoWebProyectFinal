<template>
    <label class="block text-sm font-bold text-slate-700"
        >{{ label
        }}<input
            :name="name"
            :type="type"
            :value="modelValue"
            :required="required"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-normal outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
            @input="$emit('update:modelValue', $event.target.value)"
        /><span
            v-if="error"
            class="mt-1 block text-xs font-medium text-red-600"
            >{{ error }}</span
        ></label
    >
</template>
<script setup>
defineProps({
    label: String,
    name: String,
    type: { type: String, default: "text" },
    modelValue: [String, Number],
    error: String,
    required: Boolean,
});
defineEmits(["update:modelValue"]);
</script>
