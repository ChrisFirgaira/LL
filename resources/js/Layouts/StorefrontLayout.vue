<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

const appName = computed(() => page.props.appName ?? 'Lunar Store');
const announcement = computed(() => page.props.announcement ?? null);
const navigation = computed(() => page.props.navigation ?? {});
const cartSummary = computed(() => page.props.cartSummary ?? { itemCount: 0, total: null });
const flash = computed(() => page.props.flash ?? {});
const contact = computed(() => page.props.contact ?? {});
</script>

<template>
    <div class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(circle_at_top,rgba(34,211,238,0.14),transparent_38%)]" />

        <div v-if="announcement || contact.phone || contact.email" class="border-b border-white/10 bg-black/30 text-sm text-slate-300">
            <div class="store-shell flex flex-col gap-2 py-2 md:flex-row md:items-center md:justify-between">
                <p>{{ announcement }}</p>
                <div class="flex flex-wrap items-center gap-4 text-slate-400">
                    <span v-if="contact.phone">{{ contact.phone }}</span>
                    <span v-if="contact.email">{{ contact.email }}</span>
                </div>
            </div>
        </div>

        <header class="sticky top-0 z-30 border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
            <div class="store-shell flex items-center justify-between gap-6 py-4">
                <div class="flex items-center gap-8">
                    <Link href="/" class="text-lg font-semibold tracking-tight text-white">
                        {{ appName }}
                    </Link>

                    <nav class="hidden items-center gap-5 text-sm text-slate-300 md:flex">
                        <Link href="/" class="transition hover:text-white">Home</Link>
                        <Link :href="navigation.shopUrl" class="transition hover:text-white">Shop</Link>
                        <Link :href="navigation.contactUrl" class="transition hover:text-white">Contact</Link>
                        <Link
                            v-for="collection in navigation.collections ?? []"
                            :key="collection.id"
                            :href="collection.url"
                            class="transition hover:text-white"
                        >
                            {{ collection.name }}
                        </Link>
                    </nav>
                </div>

                <div class="flex items-center gap-3">
                    <Link :href="navigation.checkoutUrl" class="secondary-button hidden md:inline-flex">
                        Checkout
                    </Link>
                    <Link :href="navigation.cartUrl" class="primary-button">
                        Cart
                        <span class="ml-2 rounded-full bg-white/15 px-2 py-0.5 text-xs">
                            {{ cartSummary.itemCount ?? 0 }}
                        </span>
                    </Link>
                </div>
            </div>
        </header>

        <div class="store-shell relative z-10 py-6">
            <div
                v-if="flash.success || flash.error"
                class="mb-6 rounded-2xl border px-4 py-3 text-sm backdrop-blur-md"
                :class="flash.success ? 'border-emerald-400/30 bg-emerald-500/10 text-emerald-100' : 'border-rose-400/30 bg-rose-500/10 text-rose-100'"
            >
                {{ flash.success || flash.error }}
            </div>

            <slot />
        </div>

        <footer class="mt-20 border-t border-white/10">
            <div class="store-shell flex flex-col gap-6 py-10 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="font-medium text-slate-200">{{ appName }}</p>
                    <p>Modern Lunar storefront boilerplate with Inertia and Vue.</p>
                    <p v-if="contact.email" class="mt-2">{{ contact.email }}</p>
                </div>

                <div class="flex items-center gap-4">
                    <Link href="/" class="transition hover:text-white">Home</Link>
                    <Link :href="navigation.shopUrl" class="transition hover:text-white">Shop</Link>
                    <Link :href="navigation.cartUrl" class="transition hover:text-white">Cart</Link>
                    <Link :href="navigation.contactUrl" class="transition hover:text-white">Contact</Link>
                </div>
            </div>
        </footer>
    </div>
</template>
