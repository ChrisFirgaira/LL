<script setup>
import { Link, router } from '@inertiajs/vue3';
import PriceTag from './PriceTag.vue';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    catalogMode: {
        type: Boolean,
        default: false,
    },
});

const quickAddToCart = () => {
    if (!props.product?.defaultVariantId) {
        return;
    }

    router.post('/cart/lines', {
        variant_id: props.product.defaultVariantId,
        quantity: 1,
        redirect_back: true,
    }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <article class="glass-panel group transition duration-200 hover:border-brand-400/40" :class="catalogMode ? 'overflow-hidden catalog-product-card' : ''">
        <Link :href="product.url" class="block">
            <div class="relative" :class="catalogMode ? 'catalog-product-media aspect-[4/4.3] bg-white p-4' : 'aspect-[4/5] bg-slate-100'">
                <img
                    v-if="product.image"
                    :src="product.image"
                    :alt="product.name"
                    class="h-full w-full"
                    :class="catalogMode ? 'object-contain' : 'object-cover'"
                >
                <div
                    v-else
                    class="flex h-full w-full items-center justify-center text-sm text-slate-400"
                >
                    Product imagery ready
                </div>
                <div v-if="product.isOnSale" class="absolute left-4 top-4 rounded-full bg-rose-500 px-3 py-1 text-[0.7rem] font-semibold uppercase tracking-[0.24em] text-white shadow-lg shadow-rose-950/30">
                    On sale
                </div>
                <div v-if="!catalogMode" class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/70 to-transparent p-5">
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="collectionName in (product.collectionNames ?? []).slice(0, 2)"
                            :key="collectionName"
                            class="pill !border-slate-200 !bg-white/95 !text-slate-700"
                        >
                            {{ collectionName }}
                        </span>
                    </div>
                </div>
            </div>
        </Link>

        <div class="space-y-4 p-5" :class="catalogMode ? 'catalog-product-card-body bg-sky-50/60' : ''">
            <div class="space-y-2">
                <p v-if="!catalogMode" class="divider-label">Featured product</p>
                <Link :href="product.url" class="catalog-product-title font-semibold tracking-tight text-slate-900 transition group-hover:text-brand-600" :class="catalogMode ? 'line-clamp-2 text-base leading-6' : 'text-xl'">
                    {{ product.name }}
                </Link>
                <p v-if="!catalogMode && product.description" class="line-clamp-2 text-sm leading-6 text-slate-600">
                    {{ product.description }}
                </p>
            </div>

            <div class="border-t border-slate-200 pt-4">
                <div class="mb-3" :class="catalogMode ? 'catalog-price-context' : ''">
                    <PriceTag :price="product.price" :compact="catalogMode" />
                    <p v-if="product.comparePrice" class="catalog-compare-price mt-1 text-xs text-slate-500 line-through">
                        {{ product.comparePrice }}
                    </p>
                    <p v-if="!catalogMode && (product.variantCount ?? 0) > 1" class="mt-2 text-xs font-medium uppercase tracking-[0.2em] text-slate-500">
                        {{ product.variantCount }} purchasable variants
                    </p>
                </div>
                <button
                    v-if="catalogMode"
                    type="button"
                    class="primary-button w-full justify-center !rounded-xl !py-2.5"
                    :disabled="!product.defaultVariantId"
                    @click="quickAddToCart"
                >
                    Add to cart
                </button>
                <Link v-else :href="product.url" class="secondary-button">View details</Link>
            </div>
        </div>
    </article>
</template>
