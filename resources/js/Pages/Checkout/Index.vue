<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
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
const flash = computed(() => page.props.flash ?? {});
const auth = computed(() => page.props.auth ?? {});
const canCreateAccount = computed(() => !auth.value?.user);

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
    fingerprint: props.checkout.fingerprint ?? null,
    create_account: false,
    create_account_password: '',
    create_account_password_confirmation: '',
});
const cartBusyLineId = ref(null);
const checkoutSubmitting = ref(false);

const placeOrder = () => {
    if (checkoutSubmitting.value) {
        return;
    }

    router.post('/checkout/place', {
        accept_terms: placeOrderForm.accept_terms,
        fingerprint: props.checkout.fingerprint,
        create_account: placeOrderForm.create_account,
        create_account_password: placeOrderForm.create_account_password,
        create_account_password_confirmation: placeOrderForm.create_account_password_confirmation,
        same_as_billing: addressForm.same_as_billing,
        billing: addressForm.billing,
        shipping: addressForm.shipping,
        shipping_option: shippingForm.shipping_option,
    }, {
        preserveScroll: true,
        onStart: () => {
            checkoutSubmitting.value = true;
        },
        onFinish: () => {
            checkoutSubmitting.value = false;
        },
    });
};

const updateCheckoutLineQuantity = (line, quantity) => {
    if (!line?.id || cartBusyLineId.value) {
        return;
    }

    const nextQuantity = Math.max(1, Math.min(20, Number(quantity || 1)));
    if (nextQuantity === line.quantity) {
        return;
    }

    cartBusyLineId.value = line.id;
    router.patch(`/cart/lines/${line.id}`, { quantity: nextQuantity }, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            cartBusyLineId.value = null;
        },
    });
};

const removeCheckoutLine = (lineId) => {
    if (!lineId || cartBusyLineId.value) {
        return;
    }

    cartBusyLineId.value = lineId;
    router.delete(`/cart/lines/${lineId}`, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            cartBusyLineId.value = null;
        },
    });
};
</script>

<template>
    <Head title="Checkout" />

    <section class="flex w-full flex-col gap-3 px-4 pb-6 pt-3 md:px-6 lg:px-8">
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

    <section class="grid w-full gap-6 px-4 pb-8 md:px-6 lg:px-8 xl:grid-cols-[1.15fr_0.9fr_0.85fr]">
        <div class="space-y-5">
            <div class="glass-panel p-5">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-950">Billing details</h2>
                        <p class="text-sm text-slate-500">Used for order confirmation and invoice details.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
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

                    <div class="grid gap-4 md:grid-cols-2">
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

                    <div class="grid gap-4">
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

                    <div class="grid gap-4 md:grid-cols-2">
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

                    <div class="grid gap-4 md:grid-cols-2">
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

                    <div v-if="!addressForm.same_as_billing" class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <h3 class="text-lg font-semibold text-slate-950">Shipping address</h3>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">First name</span>
                                <input v-model="addressForm.shipping.first_name" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                                <p v-if="errors['shipping.first_name']" class="text-sm text-rose-600">{{ errors['shipping.first_name'] }}</p>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">Last name</span>
                                <input v-model="addressForm.shipping.last_name" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            </label>
                        </div>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-700">Address line 1</span>
                            <input v-model="addressForm.shipping.line_one" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            <p v-if="errors['shipping.line_one']" class="text-sm text-rose-600">{{ errors['shipping.line_one'] }}</p>
                        </label>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">City</span>
                                <input v-model="addressForm.shipping.city" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                                <p v-if="errors['shipping.city']" class="text-sm text-rose-600">{{ errors['shipping.city'] }}</p>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">Postcode</span>
                                <input v-model="addressForm.shipping.postcode" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                                <p v-if="errors['shipping.postcode']" class="text-sm text-rose-600">{{ errors['shipping.postcode'] }}</p>
                            </label>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">Country</span>
                                <select v-model="addressForm.shipping.country_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                                    <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                                </select>
                                <p v-if="errors['shipping.country_id']" class="text-sm text-rose-600">{{ errors['shipping.country_id'] }}</p>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">Delivery notes</span>
                                <input v-model="addressForm.shipping.delivery_instructions" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400">
                            </label>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="glass-panel p-5">
                <h2 class="text-2xl font-semibold text-slate-950">Order summary</h2>
                <div class="mt-4 space-y-3">
                    <article
                        v-for="line in checkout.cart.lines"
                        :key="line.id"
                        class="flex items-start justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3"
                    >
                        <div class="flex min-w-0 flex-1 items-start gap-3">
                            <div class="h-16 w-16 overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <img
                                    v-if="line.image"
                                    :src="line.image"
                                    :alt="line.name"
                                    class="h-full w-full object-contain"
                                >
                                <div v-else class="flex h-full w-full items-center justify-center text-[10px] text-slate-400">
                                    No image
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-slate-900">{{ line.name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ line.option || 'Default option' }}</p>
                                <div class="mt-2 inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2 py-1">
                                    <button
                                        type="button"
                                        class="h-7 w-7 rounded-md border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                                        :disabled="cartBusyLineId === line.id"
                                        @click="updateCheckoutLineQuantity(line, line.quantity - 1)"
                                    >
                                        -
                                    </button>
                                    <span class="min-w-8 text-center text-sm font-semibold text-slate-900">{{ line.quantity }}</span>
                                    <button
                                        type="button"
                                        class="h-7 w-7 rounded-md border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                                        :disabled="cartBusyLineId === line.id"
                                        @click="updateCheckoutLineQuantity(line, line.quantity + 1)"
                                    >
                                        +
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    class="mt-2 block text-xs font-semibold text-rose-600 hover:text-rose-700 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="cartBusyLineId === line.id"
                                    @click="removeCheckoutLine(line.id)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                        <p class="shrink-0 text-sm font-medium text-slate-900">{{ line.total || line.unitPrice }}</p>
                    </article>
                </div>

                <div class="mt-5 space-y-2.5 border-t border-slate-200 pt-3 text-sm text-slate-600">
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
                        <span>{{ checkout.cart.shippingTotal || checkout.shippingOptions?.find((option) => option.identifier === shippingForm.shipping_option)?.price || 'Pending selection' }}</span>
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
        </div>

        <aside class="space-y-5">
            <div class="glass-panel p-5">
                <div class="mb-4">
                    <h2 class="text-2xl font-semibold text-slate-950">Shipping method</h2>
                    <p class="text-sm text-slate-500">Choose how this order should be delivered or collected.</p>
                </div>

                <div v-if="checkout.shippingOptions?.length" class="space-y-3">
                    <label
                        v-for="option in checkout.shippingOptions"
                        :key="option.identifier"
                        class="checkout-shipping-option flex cursor-pointer items-start gap-3 rounded-2xl border px-4 py-3 transition"
                        :class="shippingForm.shipping_option === option.identifier ? 'checkout-shipping-option--selected border-emerald-400 bg-emerald-50' : 'border-slate-200 bg-white'"
                    >
                        <input
                            v-model="shippingForm.shipping_option"
                            type="radio"
                            :value="option.identifier"
                            class="mt-1 h-4 w-4"
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
                    No delivery methods are currently available for this order.
                </div>
            </div>

            <div class="glass-panel p-5">
                <h2 class="text-2xl font-semibold text-slate-950">Place order</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Submit your order when everything looks correct.
                </p>

                <form class="mt-4 space-y-3.5" @submit.prevent="placeOrder">
                    <div v-if="canCreateAccount" class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <label class="flex items-start gap-3 text-sm text-slate-700">
                            <input v-model="placeOrderForm.create_account" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 bg-white text-brand-500">
                            <span>Create a new account with these checkout details</span>
                        </label>

                        <div v-if="placeOrderForm.create_account" class="grid gap-3 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">Password</span>
                                <input v-model="placeOrderForm.create_account_password" type="password" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-900 outline-none transition focus:border-brand-400">
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700">Confirm password</span>
                                <input v-model="placeOrderForm.create_account_password_confirmation" type="password" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-900 outline-none transition focus:border-brand-400">
                            </label>
                        </div>

                        <p v-if="errors.create_account_password" class="text-sm text-rose-600">{{ errors.create_account_password }}</p>
                    </div>

                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-700">
                        <input v-model="placeOrderForm.accept_terms" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 bg-white text-brand-500">
                        <span>I confirm that my details are correct and I’m ready to submit this order.</span>
                    </label>

                    <p v-if="errors.accept_terms" class="text-sm text-rose-600">{{ errors.accept_terms }}</p>
                    <p v-if="errors.cart" class="text-sm text-rose-600">{{ errors.cart }}</p>
                    <p v-if="errors.fingerprint" class="text-sm text-rose-600">{{ errors.fingerprint }}</p>
                    <p v-if="errors.shipping_option" class="text-sm text-rose-600">{{ errors.shipping_option }}</p>
                    <p v-if="flash.error" class="text-sm text-rose-600">{{ flash.error }}</p>

                    <button
                        type="button"
                        class="primary-button w-full"
                        :disabled="checkoutSubmitting"
                        @click="placeOrder"
                    >
                        {{ checkoutSubmitting ? 'Submitting order...' : 'Submit order' }}
                    </button>
                </form>
            </div>
        </aside>
    </section>
</template>
