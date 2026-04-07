<script setup>
defineProps({
    price: {
        type: String,
        default: null,
    },
    label: {
        type: String,
        default: 'From',
    },
});
</script>

<template>
    <div class="flex items-baseline gap-2">
        <span class="text-xs font-medium uppercase tracking-[0.24em] text-slate-500">{{ label }}</span>
        <span class="text-lg font-semibold text-slate-900">{{ price || 'Coming soon' }}</span>
    </div>
</template>
