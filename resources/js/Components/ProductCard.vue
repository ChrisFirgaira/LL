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
    <article class="glass-panel group overflow-hidden transition duration-200 hover:-translate-y-1 hover:border-brand-400/40">
        <Link :href="product.url" class="block">
            <div class="relative aspect-[4/5] overflow-hidden bg-slate-900">
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
                <div v-if="product.isOnSale" class="absolute left-4 top-4 rounded-full bg-rose-500 px-3 py-1 text-[0.7rem] font-semibold uppercase tracking-[0.24em] text-white shadow-lg shadow-rose-950/30">
                    On sale
                </div>
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950 to-transparent p-5">
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="collectionName in (product.collectionNames ?? []).slice(0, 2)"
                            :key="collectionName"
                            class="pill bg-slate-950/80"
                        >
                            {{ collectionName }}
                        </span>
                    </div>
                </div>
            </div>
        </Link>

        <div class="space-y-4 p-5">
            <div class="space-y-2">
                <p class="divider-label">Featured product</p>
                <Link :href="product.url" class="text-xl font-semibold tracking-tight text-white transition group-hover:text-brand-300">
                    {{ product.name }}
                </Link>
                <p class="line-clamp-2 text-sm leading-6 text-slate-400">
                    {{ product.description || 'Crafted for a premium storefront experience with Lunar.' }}
                </p>
            </div>

            <div class="flex items-center justify-between gap-4 border-t border-white/10 pt-4">
                <div>
                    <PriceTag :price="product.price" />
                    <p v-if="product.comparePrice" class="mt-1 text-sm text-slate-500 line-through">
                        {{ product.comparePrice }}
                    </p>
                    <p v-if="(product.variantCount ?? 0) > 1" class="mt-2 text-xs font-medium uppercase tracking-[0.2em] text-slate-500">
                        {{ product.variantCount }} purchasable variants
                    </p>
                </div>
                <Link :href="product.url" class="secondary-button">
                    View details
                </Link>
            </div>
        </div>
    </article>
</template>
