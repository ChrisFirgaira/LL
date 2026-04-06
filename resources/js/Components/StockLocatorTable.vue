<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
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
    limit: {
        type: Number,
        default: null,
    },
    compact: {
        type: Boolean,
        default: false,
    },
});

const search = ref('');
const selectedLocation = ref('all');
const selectedCategory = ref('all');
const selectedStatus = ref('all');

const filteredRows = computed(() => {
    const term = search.value.trim().toLowerCase();

    return props.rows.filter((row) => {
        const haystack = [
            row.location,
            row.suburb,
            row.category,
            row.product,
            row.sku,
            row.stock_status,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase();

        const matchesSearch = !term || haystack.includes(term);
        const matchesLocation = selectedLocation.value === 'all' || row.location === selectedLocation.value;
        const matchesCategory = selectedCategory.value === 'all' || row.category === selectedCategory.value;
        const matchesStatus = selectedStatus.value === 'all' || row.stock_status === selectedStatus.value;

        return matchesSearch && matchesLocation && matchesCategory && matchesStatus;
    });
});

const visibleRows = computed(() => {
    return props.limit ? filteredRows.value.slice(0, props.limit) : filteredRows.value;
});

const stockBadgeClass = (status) => {
    if (status === 'In Stock') {
        return 'bg-emerald-100 text-emerald-700';
    }

    if (status === 'Low Stock') {
        return 'bg-amber-100 text-amber-700';
    }

    return 'bg-rose-100 text-rose-700';
};
</script>

<template>
    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
        <div class="grid gap-4" :class="compact ? 'lg:grid-cols-[1.5fr_1fr_1fr_1fr]' : 'lg:grid-cols-[1.7fr_1fr_1fr_1fr]'">
            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Search Location</span>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Location, postcode, product..."
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-brand-500"
                >
            </label>

            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Location</span>
                <select
                    v-model="selectedLocation"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-brand-500"
                >
                    <option value="all">All Locations</option>
                    <option v-for="location in locations" :key="location" :value="location">{{ location }}</option>
                </select>
            </label>

            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Category</span>
                <select
                    v-model="selectedCategory"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-brand-500"
                >
                    <option value="all">All Categories</option>
                    <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                </select>
            </label>

            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Status</span>
                <select
                    v-model="selectedStatus"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-brand-500"
                >
                    <option value="all">All Statuses</option>
                    <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                </select>
            </label>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-slate-500">
            <span>Rows: {{ summary.rows ?? rows.length }}</span>
            <span>Locations: {{ summary.locations ?? locations.length }}</span>
            <span>Categories: {{ summary.categories ?? categories.length }}</span>
            <span>Shown: {{ visibleRows.length }}</span>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-y-3">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                        <th class="px-4 py-2">Location</th>
                        <th class="px-4 py-2">Category</th>
                        <th class="px-4 py-2">Product</th>
                        <th class="px-4 py-2">Price</th>
                        <th class="px-4 py-2">Stock</th>
                        <th class="px-4 py-2">Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in visibleRows"
                        :key="row.id"
                        class="rounded-2xl bg-slate-50 text-sm text-slate-700 shadow-sm"
                    >
                        <td class="rounded-l-2xl px-4 py-4 align-top">
                            <p class="font-semibold text-slate-950">{{ row.location }}</p>
                            <p class="mt-1 text-xs uppercase tracking-[0.12em] text-slate-500">{{ row.suburb }}</p>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-brand-600">
                                {{ row.category }}
                            </span>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <p class="font-medium text-slate-950">{{ row.product }}</p>
                            <p class="mt-1 text-xs uppercase tracking-[0.12em] text-slate-500">{{ row.sku }}</p>
                        </td>
                        <td class="px-4 py-4 align-top font-semibold text-slate-950">
                            {{ row.price }}
                        </td>
                        <td class="px-4 py-4 align-top">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]" :class="stockBadgeClass(row.stock_status)">
                                {{ row.stock_status }}
                            </span>
                            <p class="mt-2 text-xs uppercase tracking-[0.12em] text-slate-500">{{ row.stock_level }} units</p>
                        </td>
                        <td class="rounded-r-2xl px-4 py-4 align-top text-slate-500">
                            {{ row.updated_at }}
                        </td>
                    </tr>
                    <tr v-if="!visibleRows.length">
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                            No stock rows match the current filters.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
