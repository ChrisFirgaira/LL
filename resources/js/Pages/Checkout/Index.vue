<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

const props = defineProps({
    countries: {
        type: Array,
        default: () => [],
    },
    checkout: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const errors = computed(() => page.props.errors ?? {});

const buildAddress = (address = {}) => ({
    first_name: address?.first_name ?? '',
    last_name: address?.last_name ?? '',
    company_name: address?.company_name ?? '',
    line_one: address?.line_one ?? '',
    line_two: address?.line_two ?? '',
    city: address?.city ?? '',
    state: address?.state ?? '',
    postcode: address?.postcode ?? '',
    country_id: address?.country_id ?? props.countries[0]?.id ?? '',
    contact_email: address?.contact_email ?? '',
    contact_phone: address?.contact_phone ?? '',
    delivery_instructions: address?.delivery_instructions ?? '',
});

const addressForm = useForm({
    same_as_billing: props.checkout.sameAsBilling ?? true,
    billing: buildAddress(props.checkout.billingAddress),
    shipping: buildAddress(props.checkout.shippingAddress),
});

const shippingForm = useForm({
    shipping_option: props.checkout.selectedShippingOption ?? props.checkout.shippingOptions?.[0]?.identifier ?? '',
});

const placeOrderForm = useForm({
    accept_terms: false,
});

const saveAddresses = () => {
    addressForm.post('/checkout/addresses', {
        preserveScroll: true,
    });
};

const saveShipping = () => {
    if (!shippingForm.shipping_option) {
        return;
    }

    shippingForm.post('/checkout/shipping', {
        preserveScroll: true,
    });
};

const placeOrder = () => {
    placeOrderForm.post('/checkout/place', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Checkout" />

    <section class="mx-auto flex w-full max-w-[1240px] flex-col gap-4 pb-8 pt-4">
        <span class="pill">Checkout</span>
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-4xl font-semibold tracking-tight text-slate-950 md:text-5xl">Complete your order</h1>
                <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">
                    Confirm your details, choose shipping, and submit your order.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <Link href="/cart" class="secondary-button">Back to cart</Link>
                <Link href="/shop" class="secondary-button">Continue shopping</Link>
            </div>
        </div>
    </section>

    <section class="mx-auto grid w-full max-w-[1240px] gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <div class="glass-panel p-6">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-950">Billing details</h2>
                        <p class="text-sm text-slate-500">Used for order confirmation and invoice details.</p>
                    </div>
                </div>

                <form class="space-y-5" @submit.prevent="saveAddresses">
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">First name</span>
                            <input v-model="addressForm.billing.first_name" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            <p v-if="errors['billing.first_name']" class="text-sm text-rose-600">{{ errors['billing.first_name'] }}</p>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">Last name</span>
                            <input v-model="addressForm.billing.last_name" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                        </label>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">Email</span>
                            <input v-model="addressForm.billing.contact_email" type="email" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            <p v-if="errors['billing.contact_email']" class="text-sm text-rose-600">{{ errors['billing.contact_email'] }}</p>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">Phone</span>
                            <input v-model="addressForm.billing.contact_phone" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                        </label>
                    </div>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-700">Company</span>
                        <input v-model="addressForm.billing.company_name" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                    </label>

                    <div class="grid gap-5">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">Address line 1</span>
                            <input v-model="addressForm.billing.line_one" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            <p v-if="errors['billing.line_one']" class="text-sm text-rose-600">{{ errors['billing.line_one'] }}</p>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">Address line 2</span>
                            <input v-model="addressForm.billing.line_two" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                        </label>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">City</span>
                            <input v-model="addressForm.billing.city" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            <p v-if="errors['billing.city']" class="text-sm text-rose-600">{{ errors['billing.city'] }}</p>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">State / Region</span>
                            <input v-model="addressForm.billing.state" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                        </label>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">Postcode</span>
                            <input v-model="addressForm.billing.postcode" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            <p v-if="errors['billing.postcode']" class="text-sm text-rose-600">{{ errors['billing.postcode'] }}</p>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">Country</span>
                            <select v-model="addressForm.billing.country_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                                <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                            </select>
                            <p v-if="errors['billing.country_id']" class="text-sm text-rose-600">{{ errors['billing.country_id'] }}</p>
                        </label>
                    </div>

                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        <input v-model="addressForm.same_as_billing" type="checkbox" class="h-4 w-4 rounded border-slate-300 bg-white text-brand-500">
                        Shipping address is the same as billing
                    </label>

                    <div v-if="!addressForm.same_as_billing" class="space-y-5 rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        <h3 class="text-lg font-semibold text-slate-950">Shipping address</h3>

                        <div class="grid gap-5 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">First name</span>
                                <input v-model="addressForm.shipping.first_name" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">Last name</span>
                                <input v-model="addressForm.shipping.last_name" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            </label>
                        </div>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">Address line 1</span>
                            <input v-model="addressForm.shipping.line_one" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                        </label>

                        <div class="grid gap-5 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">City</span>
                                <input v-model="addressForm.shipping.city" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">Postcode</span>
                                <input v-model="addressForm.shipping.postcode" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            </label>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">Country</span>
                                <select v-model="addressForm.shipping.country_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                                    <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                                </select>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">Delivery notes</span>
                                <input v-model="addressForm.shipping.delivery_instructions" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="primary-button" :disabled="addressForm.processing">
                            {{ addressForm.processing ? 'Saving...' : 'Save addresses' }}
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <aside class="space-y-6">
            <div class="glass-panel p-6">
                <div class="mb-5">
                    <h2 class="text-2xl font-semibold text-slate-950">Shipping method</h2>
                    <p class="text-sm text-slate-500">Choose how this order should be delivered or collected.</p>
                </div>

                <div v-if="checkout.shippingOptions?.length" class="space-y-4">
                    <label
                        v-for="option in checkout.shippingOptions"
                        :key="option.identifier"
                        class="flex cursor-pointer items-start gap-4 rounded-2xl border px-4 py-4 transition"
                        :class="shippingForm.shipping_option === option.identifier ? 'border-brand-400 bg-brand-50' : 'border-slate-200 bg-white'"
                    >
                        <input
                            v-model="shippingForm.shipping_option"
                            type="radio"
                            :value="option.identifier"
                            class="mt-1 h-4 w-4"
                            @change="saveShipping"
                        >
                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-4">
                                <p class="font-medium text-slate-900">{{ option.name }}</p>
                                <p class="text-sm font-medium text-slate-900">{{ option.price }}</p>
                            </div>
                            <p class="mt-1 text-sm text-slate-500">{{ option.description }}</p>
                        </div>
                    </label>

                    <p v-if="errors.shipping_option" class="text-sm text-rose-600">{{ errors.shipping_option }}</p>
                </div>

                <div v-else class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">
                    Save your addresses first to load available delivery methods.
                </div>
            </div>

            <div class="glass-panel p-6">
                <h2 class="text-2xl font-semibold text-slate-950">Order summary</h2>
                <div class="mt-5 space-y-4">
                    <article
                        v-for="line in checkout.cart.lines"
                        :key="line.id"
                        class="flex items-start justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4"
                    >
                        <div>
                            <p class="font-medium text-slate-900">{{ line.name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ line.option || 'Default option' }}</p>
                            <p class="mt-1 text-xs text-slate-500">Qty {{ line.quantity }}</p>
                        </div>
                        <p class="text-sm font-medium text-slate-900">{{ line.total || line.unitPrice }}</p>
                    </article>
                </div>

                <div class="mt-6 space-y-3 border-t border-slate-200 pt-4 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>Subtotal</span>
                        <span>{{ checkout.cart.subTotal || '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Discounts</span>
                        <span>{{ checkout.cart.discountTotal || '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Shipping</span>
                        <span>{{ checkout.cart.shippingTotal || 'Pending selection' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Tax</span>
                        <span>{{ checkout.cart.taxTotal || '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-200 pt-3 text-lg font-semibold text-slate-950">
                        <span>Total</span>
                        <span>{{ checkout.cart.total || checkout.cart.subTotal || '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-6">
                <h2 class="text-2xl font-semibold text-slate-950">Place order</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Submit your order when everything looks correct.
                </p>

                <form class="mt-5 space-y-4" @submit.prevent="placeOrder">
                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-700">
                        <input v-model="placeOrderForm.accept_terms" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 bg-white text-brand-500">
                        <span>I confirm that my details are correct and I’m ready to submit this order.</span>
                    </label>

                    <p v-if="errors.accept_terms" class="text-sm text-rose-600">{{ errors.accept_terms }}</p>
                    <p v-if="errors.cart" class="text-sm text-rose-600">{{ errors.cart }}</p>

                    <button
                        type="submit"
                        class="primary-button w-full"
                        :disabled="placeOrderForm.processing || !checkout.canPlaceOrder"
                    >
                        {{ placeOrderForm.processing ? 'Submitting order...' : 'Submit order' }}
                    </button>
                </form>
            </div>
        </aside>
    </section>
</template>
