<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Breadcrumbs from '../../Components/Breadcrumbs.vue';
import ProductCard from '../../Components/ProductCard.vue';
import PriceTag from '../../Components/PriceTag.vue';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    relatedProducts: {
        type: Array,
        default: () => [],
    },
});

const selectedVariantId = ref(props.product.variants?.[0]?.id ?? null);

const selectedVariant = computed(() => {
    return props.product.variants?.find((variant) => variant.id === selectedVariantId.value) ?? null;
});

const form = useForm({
    variant_id: selectedVariantId.value,
    quantity: 1,
});

watch(selectedVariantId, (value) => {
    form.variant_id = value;
});

const submit = () => {
    form.post('/cart/lines');
};

const breadcrumbItems = computed(() => {
    const firstCollection = props.product.collections?.[0];

    return [
        { label: 'Home', href: '/' },
        { label: 'Catalog', href: '/shop' },
        ...(firstCollection ? [{ label: firstCollection.name, href: firstCollection.url }] : []),
        { label: props.product.name },
    ];
});
</script>

<template>
    <Head :title="product.name" />

    <section class="mx-auto grid w-full max-w-[1240px] gap-8 py-4 lg:grid-cols-[1fr_0.95fr]">
        <div class="space-y-4">
            <Breadcrumbs :items="breadcrumbItems" />
            <div class="glass-panel w-full max-w-[360px] overflow-hidden rounded-xl">
                <div class="aspect-square bg-slate-100">
                    <img
                        v-if="product.image"
                        :src="product.image"
                        :alt="product.name"
                        class="h-full w-full object-contain"
                    >
                    <div
                        v-else
                        class="flex h-full w-full items-center justify-center bg-slate-100 text-slate-500"
                    >
                        Product media slot
                    </div>
                </div>
            </div>
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="stat-card">
                    <p class="divider-label">Stock</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">Live inventory status</p>
                </div>
                <div class="stat-card">
                    <p class="divider-label">Shipping</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">Estimated at checkout</p>
                </div>
                <div class="stat-card">
                    <p class="divider-label">Checkout</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">Stripe-ready flow</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-4">
                <div class="flex flex-wrap gap-2">
                    <Link
                        v-for="collection in product.collections ?? []"
                        :key="collection.id"
                        :href="collection.url"
                        class="pill"
                    >
                        {{ collection.name }}
                    </Link>
                </div>

                <div>
                    <h1 class="text-4xl font-semibold tracking-tight text-slate-950 md:text-5xl">
                        {{ product.name }}
                    </h1>
                    <p v-if="product.longDescription || product.description" class="mt-4 max-w-2xl text-base leading-8 text-slate-600">
                        {{ product.longDescription || product.description }}
                    </p>
                </div>

                <PriceTag :price="selectedVariant?.price || product.price" label="Price" />
            </div>

            <div class="surface-panel space-y-5 p-6">
                <div v-if="product.variants?.length" class="space-y-3">
                    <label class="text-sm font-medium text-slate-700">Choose variant</label>
                    <div class="grid gap-3">
                        <button
                            v-for="variant in product.variants"
                            :key="variant.id"
                            type="button"
                            class="rounded-2xl border px-4 py-4 text-left transition"
                            :class="variant.id === selectedVariantId ? 'border-brand-400 bg-brand-50 text-slate-900' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300'"
                            @click="selectedVariantId = variant.id"
                        >
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-medium">{{ variant.option || 'Default option' }}</p>
                                    <p class="mt-1 text-sm text-slate-500">SKU: {{ variant.sku || 'Not assigned yet' }}</p>
                                </div>
                                <p class="text-sm font-medium text-slate-900">{{ variant.price || 'Price pending' }}</p>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-[120px_1fr] sm:items-end">
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-700">Quantity</span>
                        <input
                            v-model.number="form.quantity"
                            type="number"
                            min="1"
                            max="20"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400"
                        >
                    </label>

                    <button
                        type="button"
                        class="primary-button w-full !rounded-2xl"
                        :disabled="form.processing || !selectedVariantId"
                        @click="submit"
                    >
                        {{ form.processing ? 'Adding...' : 'Add to cart' }}
                    </button>
                </div>

                <p class="text-sm text-slate-500">Availability and pricing update automatically.</p>
            </div>
        </div>
    </section>

    <section v-if="relatedProducts.length" class="mx-auto w-full max-w-[1240px] space-y-6 border-t border-slate-200 pt-10">
        <div>
            <p class="pill">More to explore</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-950">More products shoppers may want next</h2>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <ProductCard
                v-for="relatedProduct in relatedProducts"
                :key="relatedProduct.id"
                :product="relatedProduct"
            />
        </div>
    </section>
</template>
