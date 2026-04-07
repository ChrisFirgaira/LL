<script setup>
import { computed, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Breadcrumbs from '../../Components/Breadcrumbs.vue';
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

const selectedCollectionName = computed(() => {
    if (!filterForm.collection) {
        return null;
    }

    return props.collections.find((collection) => collection.url.split('/').pop() === filterForm.collection)?.name ?? null;
});

const breadcrumbItems = computed(() => {
    const items = [
        { label: 'Home', href: '/' },
        { label: 'Catalog', href: '/shop' },
    ];

    if (selectedCollectionName.value) {
        items.push({ label: selectedCollectionName.value });
    }

    return items;
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

    <section class="space-y-3 pb-3 pt-2">
        <Breadcrumbs :items="breadcrumbItems" />
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-4xl font-semibold tracking-tight text-slate-950">Shop all products</h1>
            </div>
            <p class="text-sm font-medium text-slate-500">
                Showing {{ pagination.from ?? 0 }}-{{ pagination.to ?? 0 }} of {{ pagination.total }}
            </p>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-[320px_1fr]">
        <aside class="surface-panel h-fit space-y-5 p-5">
            <h2 class="text-lg font-semibold text-slate-900">Filters</h2>

            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Search</span>
                <input
                    v-model="filterForm.search"
                    type="text"
                    placeholder="Name, keyword..."
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

            <label class="mt-3 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                <input v-model="filterForm.sale" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand-500 focus:ring-brand-500">
                On sale only
            </label>

            <div class="flex gap-2">
                <button type="button" class="primary-button w-full !rounded-2xl" @click="applyFilters">Apply</button>
                <button type="button" class="secondary-button w-full !rounded-2xl" @click="resetFilters">Reset</button>
            </div>
        </aside>

        <div class="space-y-6">
            <div class="flex flex-wrap gap-2">
                <Link
                    v-for="collection in collections"
                    :key="collection.id"
                    :href="collection.url"
                    class="pill"
                >
                    {{ collection.name }}
                </Link>
            </div>

            <section v-if="products.length" class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
                <ProductCard
                    v-for="product in products"
                    :key="product.id"
                    :product="product"
                    :catalog-mode="true"
                />
            </section>
        </div>
    </section>

    <section v-if="!products.length" class="glass-panel p-10 text-center">
        <h2 class="text-2xl font-semibold text-slate-950">No products found</h2>
        <p class="mt-3 text-slate-600">Try adjusting your filters or search terms.</p>
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
