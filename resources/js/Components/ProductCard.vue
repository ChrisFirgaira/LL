<script setup>
import { Link } from '@inertiajs/vue3';
import PriceTag from './PriceTag.vue';

defineProps({
    product: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <article class="glass-panel group overflow-hidden">
        <Link :href="product.url" class="block">
            <div class="aspect-[4/5] overflow-hidden bg-slate-900">
                <img
                    v-if="product.image"
                    :src="product.image"
                    :alt="product.name"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                >
                <div
                    v-else
                    class="flex h-full w-full items-center justify-center bg-[radial-gradient(circle_at_top,rgba(139,92,246,0.25),transparent_40%)] text-sm text-slate-400"
                >
                    Product imagery ready
                </div>
            </div>
        </Link>

        <div class="space-y-4 p-5">
            <div class="flex flex-wrap gap-2">
                <span
                    v-for="collectionName in product.collectionNames ?? []"
                    :key="collectionName"
                    class="pill"
                >
                    {{ collectionName }}
                </span>
                <span v-if="(product.variantCount ?? 0) > 1" class="pill">
                    {{ product.variantCount }} variants
                </span>
            </div>

            <div class="space-y-2">
                <Link :href="product.url" class="text-xl font-semibold tracking-tight text-white transition group-hover:text-brand-300">
                    {{ product.name }}
                </Link>
                <p class="line-clamp-2 text-sm leading-6 text-slate-400">
                    {{ product.description || 'Crafted for a premium storefront experience with Lunar.' }}
                </p>
            </div>

            <div class="flex items-center justify-between gap-4">
                <PriceTag :price="product.price" />
                <Link :href="product.url" class="secondary-button">
                    View product
                </Link>
            </div>
        </div>
    </article>
</template>
