<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

const props = defineProps({
    cart: {
        type: Object,
        required: true,
    },
});

const updateQuantity = (lineId, quantity) => {
    const normalizedQuantity = Number.isNaN(quantity) || quantity < 1 ? 1 : quantity;

    router.patch(`/cart/lines/${lineId}`, { quantity: normalizedQuantity });
};

const removeLine = (lineId) => {
    router.delete(`/cart/lines/${lineId}`);
};

const clearCart = () => {
    router.delete('/cart');
};
</script>

<template>
    <Head title="Cart" />

    <section class="flex flex-col gap-4 pb-6 pt-4">
        <span class="pill">Cart</span>
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-4xl font-semibold tracking-tight text-white">Your shopping cart</h1>
                <p class="mt-3 text-base leading-7 text-slate-300">
                    Review quantities, confirm your mix of products, and move straight into checkout when you are ready.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="button" class="secondary-button" @click="clearCart">Clear cart</button>
                <Link href="/shop" class="secondary-button">Continue shopping</Link>
            </div>
        </div>
    </section>

    <section v-if="cart.lines.length" class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-4">
            <article
                v-for="line in cart.lines"
                :key="line.id"
                class="glass-panel flex flex-col gap-5 p-5 md:flex-row"
            >
                <div class="h-28 w-28 shrink-0 overflow-hidden rounded-2xl bg-slate-900">
                    <img
                        v-if="line.image"
                        :src="line.image"
                        :alt="line.name"
                        class="h-full w-full object-cover"
                    >
                    <div v-else class="flex h-full items-center justify-center text-xs text-slate-500">
                        No image
                    </div>
                </div>

                <div class="flex flex-1 flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-2">
                        <Link v-if="line.url" :href="line.url" class="text-xl font-semibold text-white">
                            {{ line.name }}
                        </Link>
                        <p v-else class="text-xl font-semibold text-white">{{ line.name }}</p>
                        <p class="text-sm text-slate-400">{{ line.option || 'Default option' }}</p>
                        <p class="text-sm text-slate-500">SKU: {{ line.sku || 'Pending' }}</p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-[110px_auto] sm:items-end">
                        <label class="space-y-2">
                            <span class="text-sm text-slate-400">Qty</span>
                            <input
                                :value="line.quantity"
                                type="number"
                                min="1"
                                max="20"
                                class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-brand-400"
                                @change="updateQuantity(line.id, Number($event.target.value))"
                            >
                        </label>

                        <div class="flex items-center justify-between gap-3 sm:block">
                            <p class="text-right text-lg font-semibold text-white">{{ line.total || line.unitPrice }}</p>
                            <button type="button" class="mt-2 text-sm text-rose-300 transition hover:text-rose-200" @click="removeLine(line.id)">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <aside class="glass-panel h-fit space-y-5 p-6">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold text-white">Order summary</h2>
                <p class="text-sm text-slate-400">Totals are calculated from the active Lunar cart session.</p>
                <p class="text-sm text-slate-500">{{ cart.itemCount }} item<span v-if="cart.itemCount !== 1">s</span> in cart</p>
            </div>

            <div class="space-y-3 text-sm text-slate-300">
                <div class="flex items-center justify-between">
                    <span>Subtotal</span>
                    <span>{{ cart.subTotal || '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Discounts</span>
                    <span>{{ cart.discountTotal || '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Shipping</span>
                    <span>{{ cart.shippingTotal || 'Calculated during checkout' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Tax</span>
                    <span>{{ cart.taxTotal || '-' }}</span>
                </div>
            </div>

            <div class="border-t border-white/10 pt-4">
                <div class="flex items-center justify-between text-lg font-semibold text-white">
                    <span>Total</span>
                    <span>{{ cart.total || cart.subTotal || '-' }}</span>
                </div>
            </div>

            <Link href="/checkout" class="primary-button w-full">Proceed to checkout</Link>
        </aside>
    </section>

    <section v-else class="glass-panel p-10 text-center">
        <h2 class="text-2xl font-semibold text-white">Your cart is empty</h2>
        <p class="mt-3 text-slate-300">
            As shoppers add products from the catalog, their Lunar cart session will populate here automatically.
        </p>
        <Link href="/shop" class="primary-button mt-6">Browse products</Link>
    </section>
</template>
