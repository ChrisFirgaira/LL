<script setup>
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
});
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
    </section>
</template>
