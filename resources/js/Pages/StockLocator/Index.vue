<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StockLocatorTable from '../../Components/StockLocatorTable.vue';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

defineProps({
    rows: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    locations: {
        type: Array,
        default: () => [],
    },
    statuses: {
        type: Array,
        default: () => [],
    },
    summary: {
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
    <Head title="Stock Locator" />

    <section class="space-y-6 py-2">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
            <div class="max-w-4xl space-y-4">
                <span class="inline-flex rounded-full bg-brand-500 px-4 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-white">
                    Stock Locator
                </span>
                <h1 class="text-4xl font-black tracking-tight text-slate-950 md:text-5xl">
                    Realtime stock locator.
                </h1>
                <p class="max-w-3xl text-lg leading-8 text-slate-600">
                    This is a new internal Pop Attack-style stock locator with demo inventory, filterable rows, and a utility-first table layout inspired by the live legacy tool.
                </p>
                <div class="flex flex-wrap gap-4">
                    <Link href="/locations" class="primary-button">View locations</Link>
                    <a
                        v-if="legacyStockLocatorUrl"
                        :href="legacyStockLocatorUrl"
                        target="_blank"
                        rel="noreferrer"
                        class="secondary-button !border-slate-300 !bg-white !text-slate-800"
                    >
                        Open legacy locator
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-6 py-4">
        <StockLocatorTable
            :rows="rows"
            :categories="categories"
            :locations="locations"
            :statuses="statuses"
            :summary="summary"
        />
    </section>

    <section class="grid gap-6 py-4 lg:grid-cols-3">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Demo dataset</p>
            <h2 class="mt-3 text-2xl font-black tracking-tight text-slate-950">{{ summary.rows ?? rows.length }} inventory rows</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                The page is seeded with sample inventory so you can iterate on table design, search, and category handling before wiring live stock feeds.
            </p>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Utility-first UI</p>
            <h2 class="mt-3 text-2xl font-black tracking-tight text-slate-950">Built to feel operational</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                The layout intentionally favors quick search, filter controls, status chips, and compact row scanning over editorial merchandising.
            </p>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Next step</p>
            <h2 class="mt-3 text-2xl font-black tracking-tight text-slate-950">Swap demo rows for live feeds</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                Once the backend stock source is ready, this page can evolve from config-driven sample data into a real searchable availability dashboard.
            </p>
        </div>
    </section>
</template>
