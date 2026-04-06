<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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
</script>

<template>
    <Head :title="product.name" />

    <section class="grid gap-8 py-4 lg:grid-cols-[1fr_0.9fr]">
        <div class="glass-panel overflow-hidden">
            <div class="aspect-square bg-slate-900">
                <img
                    v-if="product.image"
                    :src="product.image"
                    :alt="product.name"
                    class="h-full w-full object-cover"
                >
                <div
                    v-else
                    class="flex h-full w-full items-center justify-center bg-[radial-gradient(circle_at_top,rgba(139,92,246,0.25),transparent_35%)] text-slate-400"
                >
                    Product media slot
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
                    <h1 class="text-4xl font-semibold tracking-tight text-white md:text-5xl">
                        {{ product.name }}
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-8 text-slate-300">
                        {{ product.longDescription || product.description || 'Use Lunar product attributes and media to turn this into a rich product storytelling surface.' }}
                    </p>
                </div>

                <PriceTag :price="selectedVariant?.price || product.price" label="Price" />
            </div>

            <div class="glass-panel space-y-5 p-6">
                <div v-if="product.variants?.length" class="space-y-3">
                    <label class="text-sm font-medium text-slate-300">Choose variant</label>
                    <div class="grid gap-3">
                        <button
                            v-for="variant in product.variants"
                            :key="variant.id"
                            type="button"
                            class="rounded-2xl border px-4 py-4 text-left transition"
                            :class="variant.id === selectedVariantId ? 'border-brand-400 bg-brand-500/10 text-white' : 'border-white/10 bg-white/5 text-slate-300 hover:border-white/20'"
                            @click="selectedVariantId = variant.id"
                        >
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-medium">{{ variant.option || 'Default option' }}</p>
                                    <p class="mt-1 text-sm text-slate-400">SKU: {{ variant.sku || 'Not assigned yet' }}</p>
                                </div>
                                <p class="text-sm font-medium text-white">{{ variant.price || 'Price pending' }}</p>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-[120px_1fr] sm:items-end">
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-300">Quantity</span>
                        <input
                            v-model.number="form.quantity"
                            type="number"
                            min="1"
                            max="20"
                            class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-brand-400"
                        >
                    </label>

                    <button
                        type="button"
                        class="primary-button w-full"
                        :disabled="form.processing || !selectedVariantId"
                        @click="submit"
                    >
                        {{ form.processing ? 'Adding...' : 'Add to cart' }}
                    </button>
                </div>

                <p class="text-sm text-slate-400">
                    This starter uses Lunar cart actions directly, so stock and quantity validation come from Lunar rather than custom storefront logic.
                </p>
            </div>
        </div>
    </section>

    <section v-if="relatedProducts.length" class="space-y-6 border-t border-white/10 pt-10">
        <div>
            <p class="pill">More to explore</p>
            <h2 class="mt-3 text-3xl font-semibold text-white">Related catalogue picks</h2>
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
