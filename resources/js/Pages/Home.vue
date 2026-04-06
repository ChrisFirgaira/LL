<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ProductCard from '../Components/ProductCard.vue';
import StorefrontLayout from '../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    newArrivals: {
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
    stats: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Home" />

    <section class="grid gap-8 pb-8 pt-6 lg:grid-cols-[320px_1fr]">
        <aside class="glass-panel h-fit p-6">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold text-white">Shop categories</h2>
                <Link href="/shop" class="text-sm text-brand-300 transition hover:text-brand-200">View all</Link>
            </div>

            <div class="mt-5 space-y-3">
                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="category.url"
                    class="flex items-center justify-between rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-sm text-slate-300 transition hover:border-brand-400/50 hover:text-white"
                >
                    <span>{{ category.name }}</span>
                    <span class="text-slate-500">Shop</span>
                </Link>
            </div>

            <div class="mt-6 grid gap-4">
                <div v-for="stat in stats" :key="stat.label" class="rounded-2xl border border-white/10 bg-black/20 p-4">
                    <p class="text-sm text-slate-400">{{ stat.label }}</p>
                    <p class="mt-2 text-xl font-semibold text-white">{{ stat.value }}</p>
                </div>
            </div>
        </aside>

        <div class="space-y-8">
            <div class="glass-panel overflow-hidden">
                <div class="grid gap-0 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="space-y-6 p-8 md:p-10">
                        <span class="pill">Your one-stop shop for collectibles</span>
                        <div class="space-y-5">
                            <h1 class="max-w-4xl text-5xl font-semibold tracking-tight text-white md:text-7xl">
                                Build a merchandised ecommerce storefront that feels like a real hobby store.
                            </h1>
                            <p class="max-w-2xl text-lg leading-8 text-slate-300">
                                Showcase new arrivals, category-led shopping, featured collections, and a complete Lunar-backed checkout experience in one polished storefront.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <Link href="/shop" class="primary-button">Shop all products</Link>
                            <Link href="/checkout" class="secondary-button">Go to checkout</Link>
                        </div>
                    </div>

                    <div class="grid gap-px bg-white/10 md:grid-cols-2 lg:grid-cols-1">
                        <Link
                            v-for="collection in featuredCollections.slice(0, 3)"
                            :key="collection.id"
                            :href="collection.url"
                            class="relative min-h-48 overflow-hidden bg-slate-900 p-6 transition hover:bg-slate-800"
                        >
                            <img
                                v-if="collection.image"
                                :src="collection.image"
                                :alt="collection.name"
                                class="absolute inset-0 h-full w-full object-cover opacity-40"
                            >
                            <div class="relative z-10">
                                <span class="pill">Collection</span>
                                <h2 class="mt-4 text-2xl font-semibold text-white">{{ collection.name }}</h2>
                                <p class="mt-2 max-w-sm text-sm leading-6 text-slate-300">
                                    {{ collection.description || 'Curated storefront discovery with collection-led merchandising.' }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <section class="space-y-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="pill">New arrivals</p>
                        <h2 class="mt-3 text-3xl font-semibold text-white">Latest products</h2>
                    </div>
                    <Link href="/shop" class="secondary-button">Browse catalog</Link>
                </div>

                <div v-if="newArrivals.length" class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <ProductCard
                        v-for="product in newArrivals"
                        :key="product.id"
                        :product="product"
                    />
                </div>
            </section>
        </div>
    </section>

    <section class="space-y-6 py-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="pill">Featured picks</p>
                <h2 class="mt-3 text-3xl font-semibold text-white">Promoted storefront highlights</h2>
            </div>
            <Link href="/shop" class="secondary-button">View all products</Link>
        </div>

        <div v-if="featuredProducts.length" class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <ProductCard
                v-for="product in featuredProducts"
                :key="product.id"
                :product="product"
            />
        </div>

        <div v-else class="glass-panel p-8 text-center text-slate-300">
            Add products in the Lunar admin and they will automatically appear in this storefront.
        </div>
    </section>
</template>
