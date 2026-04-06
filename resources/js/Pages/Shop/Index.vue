<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ProductCard from '../../Components/ProductCard.vue';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    collections: {
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
    <Head title="Shop" />

    <section class="space-y-6 pb-8 pt-4">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="pill">Storefront</span>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white">Shop all products</h1>
                <p class="mt-3 max-w-2xl text-base leading-7 text-slate-300">
                    A clean catalogue foundation with collection-based discovery and Lunar-backed product detail pages.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <Link
                    v-for="collection in collections"
                    :key="collection.id"
                    :href="collection.url"
                    class="secondary-button"
                >
                    {{ collection.name }}
                </Link>
            </div>
        </div>
    </section>

    <section v-if="products.length" class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <ProductCard
            v-for="product in products"
            :key="product.id"
            :product="product"
        />
    </section>

    <section v-else class="glass-panel p-10 text-center">
        <h2 class="text-2xl font-semibold text-white">Your catalogue is ready for content</h2>
        <p class="mt-3 text-slate-300">
            Publish products and collections in the Lunar admin to populate this page.
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
