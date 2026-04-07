<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

defineProps({
    account: {
        type: Object,
        default: () => ({}),
    },
    orders: {
        type: Array,
        default: () => [],
    },
    ordersPagination: {
        type: Object,
        default: () => null,
    },
    history: {
        type: Object,
        default: () => ({
            orderCount: 0,
            lifetimeSpend: '-',
            averageOrderValue: '-',
            itemsOrdered: 0,
            shipmentSummary: {
                pending: 0,
                shipped: 0,
                delivered: 0,
                cancelled: 0,
            },
        }),
    },
});

const expandedOrders = ref([]);

const isExpanded = (orderId) => expandedOrders.value.includes(orderId);

const toggleExpanded = (orderId) => {
    if (isExpanded(orderId)) {
        expandedOrders.value = expandedOrders.value.filter((id) => id !== orderId);
        return;
    }

    expandedOrders.value = [...expandedOrders.value, orderId];
};
</script>

<template>
    <Head title="My Account" />

    <section class="mx-auto w-full max-w-[960px] space-y-6 py-6">
        <div class="glass-panel p-7">
            <p class="divider-label">Account</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">My account</h1>
            <p class="mt-2 text-sm text-slate-600">View your profile and continue shopping.</p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="stat-card">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Name</p>
                    <p class="mt-2 text-base font-semibold text-slate-950">{{ account.name || '-' }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Email</p>
                    <p class="mt-2 text-base font-semibold text-slate-950">{{ account.email || '-' }}</p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <Link href="/shop" class="primary-button">Continue shopping</Link>
                <Link href="/logout" method="post" as="button" class="secondary-button">Log out</Link>
            </div>
        </div>

        <div class="glass-panel p-7">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-2xl font-black tracking-tight text-slate-950">Order history</h2>
                <span class="text-sm text-slate-500">{{ history.orderCount }} order<span v-if="history.orderCount !== 1">s</span></span>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="stat-card">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Total spent</p>
                    <p class="mt-2 text-lg font-bold text-slate-950">{{ history.lifetimeSpend }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Average order</p>
                    <p class="mt-2 text-lg font-bold text-slate-950">{{ history.averageOrderValue }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Items ordered</p>
                    <p class="mt-2 text-lg font-bold text-slate-950">{{ history.itemsOrdered }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Shipment status</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ history.shipmentSummary?.pending || 0 }} pending,
                        {{ history.shipmentSummary?.shipped || 0 }} shipped,
                        {{ history.shipmentSummary?.delivered || 0 }} delivered
                    </p>
                </div>
            </div>

            <div v-if="orders.length" class="mt-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                            <th class="py-3 pr-4">Reference</th>
                            <th class="py-3 pr-4">Status</th>
                            <th class="py-3 pr-4">Shipment</th>
                            <th class="py-3 pr-4">Placed</th>
                            <th class="py-3 pr-4">Items</th>
                            <th class="py-3 text-right">Total</th>
                            <th class="py-3 pl-4 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <template v-for="order in orders" :key="order.id">
                            <tr>
                                <td class="py-3 pr-4 font-semibold text-slate-900">{{ order.reference }}</td>
                                <td class="py-3 pr-4">{{ order.status }}</td>
                                <td class="py-3 pr-4">{{ order.shipmentStatus }}</td>
                                <td class="py-3 pr-4">{{ order.placedAt || '-' }}</td>
                                <td class="py-3 pr-4">{{ order.items }}</td>
                                <td class="py-3 text-right font-semibold text-slate-900">{{ order.total }}</td>
                                <td class="py-3 pl-4 text-right">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-full border border-slate-300 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.12em] text-slate-700 transition hover:bg-slate-50"
                                        @click="toggleExpanded(order.id)"
                                    >
                                        {{ isExpanded(order.id) ? 'Hide' : 'Expand' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="isExpanded(order.id)" class="bg-slate-50/70">
                                <td colspan="7" class="px-3 py-4">
                                    <div class="space-y-4 rounded-2xl border border-slate-200 bg-white p-4">
                                        <div class="grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-5">
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Subtotal</p>
                                                <p class="mt-1 font-semibold text-slate-900">{{ order.subTotal }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Shipping</p>
                                                <p class="mt-1 font-semibold text-slate-900">{{ order.shippingTotal }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Tax</p>
                                                <p class="mt-1 font-semibold text-slate-900">{{ order.taxTotal }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Discount</p>
                                                <p class="mt-1 font-semibold text-slate-900">{{ order.discountTotal }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Total</p>
                                                <p class="mt-1 font-semibold text-slate-900">{{ order.total }}</p>
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            <div
                                                v-for="line in order.lines"
                                                :key="line.id"
                                                class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3"
                                            >
                                                <img
                                                    v-if="line.image"
                                                    :src="line.image"
                                                    :alt="line.name"
                                                    class="h-14 w-14 rounded-lg border border-slate-200 bg-white object-contain p-1"
                                                >
                                                <div
                                                    v-else
                                                    class="h-14 w-14 rounded-lg border border-slate-200 bg-white"
                                                />
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate font-semibold text-slate-900">{{ line.name }}</p>
                                                    <p class="text-xs text-slate-500">{{ line.option || 'Default option' }}</p>
                                                </div>
                                                <div class="text-right text-xs text-slate-600">
                                                    <p>Qty {{ line.quantity }}</p>
                                                    <p>{{ line.unitPrice }} each</p>
                                                    <p class="mt-1 font-semibold text-slate-900">{{ line.lineTotal }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div v-if="ordersPagination && ordersPagination.lastPage > 1" class="mt-6 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-slate-500">
                    Showing {{ ordersPagination.from }}-{{ ordersPagination.to }} of {{ ordersPagination.total }} orders
                </p>
                <div class="flex items-center gap-2">
                    <Link
                        v-if="ordersPagination.previousPageUrl"
                        :href="ordersPagination.previousPageUrl"
                        class="secondary-button !px-4 !py-2"
                    >
                        Previous
                    </Link>
                    <span class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">
                        Page {{ ordersPagination.currentPage }} / {{ ordersPagination.lastPage }}
                    </span>
                    <Link
                        v-if="ordersPagination.nextPageUrl"
                        :href="ordersPagination.nextPageUrl"
                        class="secondary-button !px-4 !py-2"
                    >
                        Next
                    </Link>
                </div>
            </div>
            <p v-else class="mt-4 text-sm text-slate-600">No past orders yet. Once you place an order, it will appear here.</p>
        </div>
    </section>
</template>
