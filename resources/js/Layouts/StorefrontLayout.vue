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
const featuredCollections = computed(() => (navigation.value.collections ?? []).slice(0, 6));
const stockLocatorUrl = computed(() => navigation.value.stockLocatorUrl ?? null);
</script>

<template>
    <div class="relative min-h-screen overflow-hidden">
        <div v-if="announcement || contact.phone || contact.email" class="border-b border-white/10 bg-slate-950 text-sm text-slate-300">
            <div class="store-shell flex flex-col gap-2 py-2 md:flex-row md:items-center md:justify-between">
                <p class="text-sm text-slate-200">{{ announcement }}</p>
                <div class="flex flex-wrap items-center gap-4 text-sm text-slate-400">
                    <span v-if="contact.phone">{{ contact.phone }}</span>
                    <span v-if="contact.email">{{ contact.email }}</span>
                </div>
            </div>
        </div>

        <header class="sticky top-0 z-30 shadow-[0_4px_24px_rgba(15,23,42,0.18)]">
            <div class="border-b border-slate-200 bg-white">
                <div class="store-shell flex flex-col gap-4 py-5 lg:flex-row lg:items-center lg:justify-between">
                    <Link href="/" class="flex items-center gap-4">
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-500 text-base font-black uppercase tracking-[0.2em] text-white">
                            PA
                        </span>
                        <span>
                            <span class="block text-2xl font-black uppercase tracking-[0.16em] text-slate-950">{{ appName }}</span>
                            <span class="block text-sm text-slate-500">Trading cards and collectible retail</span>
                        </span>
                    </Link>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-right">
                            <p class="text-[0.7rem] font-semibold uppercase tracking-[0.22em] text-slate-500">Cart total</p>
                            <p class="text-sm font-semibold text-slate-950">{{ cartSummary.total || 'Ready to shop' }}</p>
                        </div>
                        <Link :href="navigation.cartUrl ?? '/cart'" class="primary-button">
                            Cart
                            <span class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                {{ cartSummary.itemCount ?? 0 }}
                            </span>
                        </Link>
                    </div>
                </div>
            </div>

            <nav class="bg-brand-500 text-white">
                <div class="store-shell flex flex-wrap items-center justify-center gap-x-8 gap-y-3 py-4 text-sm font-semibold uppercase tracking-[0.16em]">
                    <Link href="/" class="transition hover:opacity-80">Home</Link>
                    <Link :href="navigation.shopUrl ?? '/shop'" class="transition hover:opacity-80">Shop</Link>
                    <Link :href="navigation.locationsUrl ?? '/locations'" class="transition hover:opacity-80">Locations</Link>
                    <Link v-if="stockLocatorUrl" :href="stockLocatorUrl" class="transition hover:opacity-80">Stock Locator</Link>
                    <Link :href="navigation.contactUrl ?? '/contact'" class="transition hover:opacity-80">Contact</Link>
                    <Link :href="navigation.checkoutUrl ?? '/checkout'" class="transition hover:opacity-80">Checkout</Link>
                </div>
            </nav>
        </header>

        <main class="store-shell relative z-10 py-6 md:py-8">
            <div
                v-if="flash.success || flash.error"
                class="mb-6 rounded-2xl border px-4 py-3 text-sm backdrop-blur-md"
                :class="flash.success ? 'border-emerald-400/30 bg-emerald-500/10 text-emerald-100' : 'border-rose-400/30 bg-rose-500/10 text-rose-100'"
            >
                {{ flash.success || flash.error }}
            </div>

            <slot />
        </main>

        <section v-if="featuredCollections.length" class="border-t border-white/10 bg-slate-950/70">
            <div class="store-shell flex flex-wrap items-center gap-2 py-6">
                <span class="divider-label pr-2 text-slate-400">Popular collections</span>
                <Link
                    v-for="collection in featuredCollections"
                    :key="collection.id"
                    :href="collection.url"
                    class="pill transition hover:border-brand-400/40 hover:text-white"
                >
                    {{ collection.name }}
                </Link>
            </div>
        </section>

        <footer class="mt-20 bg-slate-950 text-white">
            <div class="store-shell grid gap-10 py-12 lg:grid-cols-[1.1fr_0.8fr_0.8fr_0.8fr]">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-500 text-sm font-black uppercase tracking-[0.2em] text-white">
                            PA
                        </span>
                        <div>
                            <p class="text-lg font-semibold text-white">{{ appName }}</p>
                            <p class="text-sm text-slate-400">Collector-first storefront experience</p>
                        </div>
                    </div>
                    <p class="max-w-md text-sm leading-7 text-slate-400">
                        Inspired by the original Pop Attack retail experience and rebuilt in Laravel, Inertia, and Lunar with stronger merchandising and service-led navigation.
                    </p>
                    <div class="flex flex-wrap gap-4 text-sm text-slate-400">
                        <span v-if="contact.email">{{ contact.email }}</span>
                        <span v-if="contact.phone">{{ contact.phone }}</span>
                    </div>
                </div>

                <div>
                    <p class="divider-label mb-4">Shop</p>
                    <div class="space-y-3 text-sm text-slate-400">
                        <Link href="/" class="block transition hover:text-white">Home</Link>
                        <Link :href="navigation.shopUrl ?? '/shop'" class="block transition hover:text-white">Catalog</Link>
                        <Link :href="navigation.locationsUrl ?? '/locations'" class="block transition hover:text-white">Locations</Link>
                        <Link :href="navigation.cartUrl ?? '/cart'" class="block transition hover:text-white">Cart</Link>
                        <Link v-if="stockLocatorUrl" :href="stockLocatorUrl" class="block transition hover:text-white">Stock locator</Link>
                    </div>
                </div>

                <div>
                    <p class="divider-label mb-4">Collections</p>
                    <div class="space-y-3 text-sm text-slate-400">
                        <Link
                            v-for="collection in featuredCollections"
                            :key="collection.id"
                            :href="collection.url"
                            class="block transition hover:text-white"
                        >
                            {{ collection.name }}
                        </Link>
                    </div>
                </div>

                <div>
                    <p class="divider-label mb-4">Support</p>
                    <div class="space-y-3 text-sm text-slate-400">
                        <Link :href="navigation.contactUrl ?? '/contact'" class="block transition hover:text-white">Contact</Link>
                        <Link :href="navigation.locationsUrl ?? '/locations'" class="block transition hover:text-white">Locations</Link>
                        <Link :href="navigation.checkoutUrl ?? '/checkout'" class="block transition hover:text-white">Shipping and checkout</Link>
                        <Link :href="navigation.shopUrl ?? '/shop'" class="block transition hover:text-white">Browse inventory</Link>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
