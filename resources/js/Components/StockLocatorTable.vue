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
const selectedRow = ref(null);

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

const selectedStoreRows = computed(() => {
    if (!selectedRow.value) {
        return [];
    }

    return props.rows.filter((row) => row.location === selectedRow.value.location);
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

const openStoreOverlay = (row) => {
    selectedRow.value = row;
};

const closeStoreOverlay = () => {
    selectedRow.value = null;
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
                        class="cursor-pointer rounded-2xl bg-slate-50 text-sm text-slate-700 shadow-sm transition hover:bg-slate-100"
                        @click="openStoreOverlay(row)"
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

        <div
            v-if="selectedRow"
            class="fixed inset-0 z-[1100] flex items-center justify-center bg-slate-950/45 px-4 backdrop-blur-[2px]"
            @click="closeStoreOverlay"
        >
            <div class="w-full max-w-xl rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-[0_28px_100px_rgba(15,23,42,0.25)]" @click.stop>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Store details</p>
                        <h3 class="mt-2 text-2xl font-black tracking-tight text-slate-950">{{ selectedRow.location }}</h3>
                        <p class="mt-1 text-sm uppercase tracking-[0.12em] text-slate-500">{{ selectedRow.suburb }}</p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                        @click="closeStoreOverlay"
                    >
                        ×
                    </button>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Selected product</p>
                        <p class="mt-2 font-semibold text-slate-950">{{ selectedRow.product }}</p>
                        <p class="mt-1 text-xs uppercase tracking-[0.12em] text-slate-500">{{ selectedRow.sku }}</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ selectedRow.price }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Stock position</p>
                        <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]" :class="stockBadgeClass(selectedRow.stock_status)">
                            {{ selectedRow.stock_status }}
                        </span>
                        <p class="mt-2 text-sm text-slate-700">{{ selectedRow.stock_level }} units</p>
                        <p class="mt-1 text-xs uppercase tracking-[0.12em] text-slate-500">Updated {{ selectedRow.updated_at }}</p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Store snapshot</p>
                    <p class="mt-2 text-sm text-slate-700">
                        {{ selectedStoreRows.length }} product row<span v-if="selectedStoreRows.length !== 1">s</span> currently listed for this location.
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span
                            v-for="item in selectedStoreRows.slice(0, 6)"
                            :key="item.id"
                            class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-slate-700"
                        >
                            {{ item.category }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
