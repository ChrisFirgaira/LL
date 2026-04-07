<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Breadcrumbs from '../../Components/Breadcrumbs.vue';
import ProductCard from '../../Components/ProductCard.vue';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

const props = defineProps({
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

const breadcrumbItems = computed(() => [
    { label: 'Home', href: '/' },
    { label: 'Catalog', href: '/shop' },
    { label: props.collection.name },
]);
</script>

<template>
    <Head :title="collection.name" />

    <section class="mb-4 mt-2">
        <Breadcrumbs :items="breadcrumbItems" />
    </section>

    <section class="glass-panel overflow-hidden">
        <div class="grid gap-0 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="aspect-[4/3] bg-slate-100">
                <img
                    v-if="collection.image"
                    :src="collection.image"
                    :alt="collection.name"
                    class="h-full w-full object-cover"
                >
                <div
                    v-else
                    class="flex h-full w-full items-end bg-slate-100 p-8"
                >
                    <span class="pill">Collection hero</span>
                </div>
            </div>

            <div class="space-y-5 p-8 lg:p-10">
                <span class="pill">Collection</span>
                <h1 class="text-4xl font-semibold tracking-tight text-slate-950 md:text-5xl">
                    {{ collection.name }}
                </h1>
                <p v-if="collection.description" class="max-w-2xl text-base leading-8 text-slate-600">
                    {{ collection.description }}
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
        <h2 class="text-2xl font-semibold text-slate-950">No products published here yet</h2>
        <p class="mt-3 text-slate-600">Products for this collection will appear here once available.</p>
    </section>

    <section class="mt-10 flex items-center justify-between gap-4 border-t border-slate-200 pt-6 text-sm text-slate-500">
        <p>
            Showing
            <span class="font-medium text-slate-900">{{ pagination.from ?? 0 }}</span>
            to
            <span class="font-medium text-slate-900">{{ pagination.to ?? 0 }}</span>
            of
            <span class="font-medium text-slate-900">{{ pagination.total }}</span>
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
