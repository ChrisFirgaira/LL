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
    compact: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="flex items-baseline gap-2">
        <span class="price-tag-label font-semibold uppercase tracking-[0.24em] text-slate-500" :class="compact ? 'text-[0.72rem]' : 'text-xs'">{{ label }}</span>
        <span class="price-tag-value font-semibold text-slate-900" :class="compact ? 'text-base' : 'text-lg'">{{ price || 'Coming soon' }}</span>
    </div>
</template>
