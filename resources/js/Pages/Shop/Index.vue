<script setup>
import { reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ProductCard from '../../Components/ProductCard.vue';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

const props = defineProps({
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
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const filterForm = reactive({
    search: props.filters.search ?? '',
    collection: props.filters.collection ?? '',
    sale: props.filters.sale ?? false,
    sort: props.filters.sort ?? 'latest',
});

const applyFilters = () => {
    router.get('/shop', {
        search: filterForm.search || undefined,
        collection: filterForm.collection || undefined,
        sale: filterForm.sale ? 1 : undefined,
        sort: filterForm.sort !== 'latest' ? filterForm.sort : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const resetFilters = () => {
    filterForm.search = '';
    filterForm.collection = '';
    filterForm.sale = false;
    filterForm.sort = 'latest';
    applyFilters();
};
</script>

<template>
    <Head title="Shop" />

    <section class="space-y-6 pb-8 pt-4">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="pill">Catalog</span>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white">Browse the full collection</h1>
                <p class="mt-3 max-w-2xl text-base leading-7 text-slate-300">
                    A cleaner catalog view with collection shortcuts, stronger product cards, and a clearer path into product detail and checkout.
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

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
        <div class="grid gap-4 lg:grid-cols-[1.6fr_1fr_1fr_auto_auto]">
            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Search</span>
                <input
                    v-model="filterForm.search"
                    type="text"
                    placeholder="Product, slug, keyword..."
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-brand-500"
                    @keyup.enter="applyFilters"
                >
            </label>

            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Collection</span>
                <select
                    v-model="filterForm.collection"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-brand-500"
                >
                    <option value="">All collections</option>
                    <option v-for="collection in collections" :key="collection.id" :value="collection.url.split('/').pop()">
                        {{ collection.name }}
                    </option>
                </select>
            </label>

            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Sort</span>
                <select
                    v-model="filterForm.sort"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-brand-500"
                >
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                    <option value="name_asc">Name A-Z</option>
                    <option value="name_desc">Name Z-A</option>
                </select>
            </label>

            <label class="flex items-end">
                <span class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                    <input v-model="filterForm.sale" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand-500 focus:ring-brand-500">
                    On sale only
                </span>
            </label>

            <div class="flex items-end gap-3">
                <button type="button" class="primary-button" @click="applyFilters">Apply</button>
                <button type="button" class="secondary-button !border-slate-300 !bg-white !text-slate-800" @click="resetFilters">Reset</button>
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
        <h2 class="text-2xl font-semibold text-white">Your catalog is ready for inventory</h2>
        <p class="mt-3 text-slate-300">
            Publish products and collections in Lunar and this browse page will populate automatically.
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
