<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ProductCard from '../../Components/ProductCard.vue';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

defineProps({
    collection: {
        type: Object,
        required: true,
    },
    products: {
        type: Array,
        default: () => [],
    },
    pagination: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="collection.name" />

    <section class="glass-panel overflow-hidden">
        <div class="grid gap-0 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="aspect-[4/3] bg-slate-900">
                <img
                    v-if="collection.image"
                    :src="collection.image"
                    :alt="collection.name"
                    class="h-full w-full object-cover"
                >
                <div
                    v-else
                    class="flex h-full w-full items-end bg-[radial-gradient(circle_at_top,rgba(34,211,238,0.16),transparent_35%)] p-8"
                >
                    <span class="pill">Collection hero</span>
                </div>
            </div>

            <div class="space-y-5 p-8 lg:p-10">
                <span class="pill">Collection</span>
                <h1 class="text-4xl font-semibold tracking-tight text-white md:text-5xl">
                    {{ collection.name }}
                </h1>
                <p class="max-w-2xl text-base leading-8 text-slate-300">
                    {{ collection.description || 'Use collection pages for seasonal campaigns, merchandising edits, and category-led discovery.' }}
                </p>
            </div>
        </div>
    </section>

    <section v-if="products.length" class="grid gap-6 py-10 md:grid-cols-2 xl:grid-cols-3">
        <ProductCard
            v-for="product in products"
            :key="product.id"
            :product="product"
        />
    </section>

    <section v-else class="glass-panel mt-8 p-10 text-center">
        <h2 class="text-2xl font-semibold text-white">No products published here yet</h2>
        <p class="mt-3 text-slate-300">
            Attach products to this collection in Lunar and they will populate automatically.
        </p>
    </section>

    <section class="mt-10 flex items-center justify-between gap-4 border-t border-white/10 pt-6 text-sm text-slate-400">
        <p>
            Showing
            <span class="font-medium text-slate-200">{{ pagination.from ?? 0 }}</span>
            to
            <span class="font-medium text-slate-200">{{ pagination.to ?? 0 }}</span>
            of
            <span class="font-medium text-slate-200">{{ pagination.total }}</span>
            products
        </p>

        <div class="flex items-center gap-3">
            <Link
                v-if="pagination.previousPageUrl"
                :href="pagination.previousPageUrl"
                class="secondary-button"
            >
                Previous
            </Link>
            <span>Page {{ pagination.currentPage }} of {{ pagination.lastPage }}</span>
            <Link
                v-if="pagination.nextPageUrl"
                :href="pagination.nextPageUrl"
                class="secondary-button"
            >
                Next
            </Link>
        </div>
    </section>
</template>
