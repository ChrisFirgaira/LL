<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ProductCard from '../Components/ProductCard.vue';
import StockLocatorTable from '../Components/StockLocatorTable.vue';
import StorefrontLayout from '../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    trustStrip: {
        type: Array,
        default: () => [],
    },
    featuredProducts: {
        type: Array,
        default: () => [],
    },
    featuredCollections: {
        type: Array,
        default: () => [],
    },
    servicePanels: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Array,
        default: () => [],
    },
    stockRows: {
        type: Array,
        default: () => [],
    },
    stockCategories: {
        type: Array,
        default: () => [],
    },
    stockLocations: {
        type: Array,
        default: () => [],
    },
    stockStatuses: {
        type: Array,
        default: () => [],
    },
    stockSummary: {
        type: Object,
        default: () => ({}),
    },
    legacyStockLocatorUrl: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <Head title="Home" />

    <section class="space-y-6 py-2">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
            <div class="grid gap-8 xl:grid-cols-[1.15fr_0.85fr]">
                <div class="space-y-5">
                    <span class="inline-flex rounded-full bg-brand-500 px-4 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-white">
                        Stock Locator
                    </span>
                    <h1 class="max-w-4xl text-5xl font-black tracking-tight text-slate-950 md:text-6xl">
                        Check stock levels, find locations, and browse the catalog from one utility-first storefront.
                    </h1>
                    <p class="max-w-3xl text-lg leading-8 text-slate-600">
                        The homepage now borrows the feel of `popattack.com.au/stock-locator`: direct, searchable, operational, and fast to scan instead of hiding the most useful tools behind secondary pages.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <Link href="/stock-locator" class="primary-button">Open stock locator</Link>
                        <Link href="/locations" class="secondary-button !border-slate-300 !bg-white !text-slate-800">View locations</Link>
                        <a
                            v-if="legacyStockLocatorUrl"
                            :href="legacyStockLocatorUrl"
                            target="_blank"
                            rel="noreferrer"
                            class="secondary-button !border-slate-300 !bg-white !text-slate-800"
                        >
                            Legacy stock page
                        </a>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3 xl:grid-cols-1">
                    <div v-for="stat in stats" :key="stat.label" class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ stat.label }}</p>
                        <p class="mt-3 text-3xl font-black tracking-tight text-slate-950">{{ stat.value }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section v-if="trustStrip.length" class="grid gap-4 py-4 md:grid-cols-3">
        <article
            v-for="item in trustStrip"
            :key="item.title"
            class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_20px_80px_rgba(15,23,42,0.08)]"
        >
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Store promise</p>
            <h2 class="mt-3 text-2xl font-black tracking-tight text-slate-950">{{ item.title }}</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600">{{ item.copy }}</p>
        </article>
    </section>

    <section class="space-y-6 py-4">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-500">Stock Preview</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-white">A live-tool style table right on the homepage</h2>
            </div>
            <Link href="/stock-locator" class="secondary-button">Go to full locator</Link>
        </div>

        <StockLocatorTable
            :rows="stockRows"
            :categories="stockCategories"
            :locations="stockLocations"
            :statuses="stockStatuses"
            :summary="stockSummary"
            :limit="4"
            compact
        />
    </section>

    <section class="grid gap-6 py-4 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-500">Browse Collections</p>
            <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950">Move naturally from stock lookup into category-led shopping</h2>
            <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">
                The utility pages should not feel detached from the storefront. They should feed directly into your main catalog, collections, and product discovery experience.
            </p>

            <div class="mt-8 grid gap-4 md:grid-cols-2">
                <Link
                    v-for="category in categories.slice(0, 4)"
                    :key="category.id"
                    :href="category.url"
                    class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-0.5 hover:border-brand-400/40"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Collection</p>
                    <h3 class="mt-3 text-2xl font-black tracking-tight text-slate-950">{{ category.name }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ category.description || 'Use category-led navigation to connect stock checking with actual shopping paths.' }}
                    </p>
                </Link>
            </div>
        </div>

        <div class="grid gap-4">
            <article
                v-for="panel in servicePanels"
                :key="panel.title"
                class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_20px_80px_rgba(15,23,42,0.08)]"
            >
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-500">{{ panel.eyebrow }}</p>
                <h3 class="mt-3 text-2xl font-black tracking-tight text-slate-950">{{ panel.title }}</h3>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ panel.copy }}</p>
            </article>
        </div>
    </section>

    <section class="space-y-6 py-4">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-500">Featured Products</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-white">Keep inventory shoppable after the utility layer</h2>
            </div>
            <Link href="/shop" class="secondary-button">Browse full catalog</Link>
        </div>

        <div v-if="featuredProducts.length" class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <ProductCard
                v-for="product in featuredProducts"
                :key="product.id"
                :product="product"
            />
        </div>
    </section>

    <section class="space-y-6 py-4">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-500">Featured Collections</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-white">Support the operational tools with merchandising surfaces</h2>
            </div>
            <Link href="/shop" class="secondary-button">Shop all products</Link>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Link
                v-for="collection in featuredCollections.slice(0, 3)"
                :key="collection.id"
                :href="collection.url"
                class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900 p-6 transition hover:border-brand-400/40"
            >
                <img
                    v-if="collection.image"
                    :src="collection.image"
                    :alt="collection.name"
                    class="absolute inset-0 h-full w-full object-cover opacity-35"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-transparent" />
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-300">Featured collection</p>
                    <h3 class="mt-3 text-2xl font-black tracking-tight text-white">{{ collection.name }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-300">
                        {{ collection.description || 'Collection-led browse paths still matter once customers move out of the stock tools.' }}
                    </p>
                </div>
            </Link>
        </div>
    </section>
</template>
