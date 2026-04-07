<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
        <template v-for="(item, index) in items" :key="`${item.label}-${index}`">
            <Link
                v-if="item.href && index < items.length - 1"
                :href="item.href"
                class="transition hover:text-slate-900"
            >
                {{ item.label }}
            </Link>
            <span v-else class="text-slate-900">{{ item.label }}</span>
            <span v-if="index < items.length - 1">/</span>
        </template>
    </nav>
</template>
