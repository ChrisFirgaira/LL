<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

defineProps({
    order: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Order received" />

    <section class="mx-auto grid w-full max-w-[1240px] gap-8 pb-10 pt-4 lg:grid-cols-[1fr_0.9fr]">
        <div class="space-y-5">
            <span class="pill">Order submitted</span>
            <h1 class="text-4xl font-semibold tracking-tight text-slate-950 md:text-5xl">
                Thanks, your order has been received.
            </h1>
            <p class="max-w-2xl text-base leading-8 text-slate-600">
                Your order is now being processed. Use the reference below in any correspondence.
            </p>

            <div class="glass-panel space-y-4 p-6">
                <div>
                    <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Order reference</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ order.reference }}</p>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-sm text-slate-500">Status</p>
                        <p class="mt-1 font-medium text-slate-900">{{ order.status }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Placed</p>
                        <p class="mt-1 font-medium text-slate-900">{{ order.placedAt }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Total</p>
                        <p class="mt-1 font-medium text-slate-900">{{ order.total }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-4">
                <Link href="/shop" class="primary-button">Continue shopping</Link>
                <Link href="/contact" class="secondary-button">Contact us</Link>
            </div>
        </div>

        <div class="space-y-6">
            <div class="glass-panel p-6">
                <h2 class="text-2xl font-semibold text-slate-950">Order details</h2>
                <div class="mt-5 space-y-3">
                    <div
                        v-for="line in order.lines"
                        :key="line.id"
                        class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4"
                    >
                        <div>
                            <p class="font-medium text-slate-900">{{ line.description }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ line.option || 'Default option' }}</p>
                            <p class="mt-1 text-xs text-slate-500">Qty {{ line.quantity }}</p>
                        </div>
                        <p class="text-sm font-medium text-slate-900">{{ line.total }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-6">
                <h2 class="text-2xl font-semibold text-slate-950">Billing address</h2>
                <div class="mt-4 text-sm leading-7 text-slate-600">
                    <p>{{ order.billingAddress?.first_name }} {{ order.billingAddress?.last_name }}</p>
                    <p>{{ order.billingAddress?.line_one }}</p>
                    <p v-if="order.billingAddress?.line_two">{{ order.billingAddress?.line_two }}</p>
                    <p>{{ order.billingAddress?.city }} {{ order.billingAddress?.postcode }}</p>
                    <p>{{ order.billingAddress?.country }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
