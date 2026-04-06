<script setup>
import { computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

defineProps({
    contactDetails: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const errors = computed(() => page.props.errors ?? {});

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const submit = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset('subject', 'message'),
    });
};
</script>

<template>
    <Head title="Contact" />

    <section class="grid gap-8 py-4 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="space-y-5">
            <span class="pill">Contact</span>
            <h1 class="text-4xl font-semibold tracking-tight text-white md:text-5xl">
                Let’s plan the next phase of your storefront.
            </h1>
            <p class="max-w-2xl text-base leading-8 text-slate-300">
                Use this form for general enquiries, project requests, support questions, or custom ecommerce feature work.
            </p>

            <div class="glass-panel space-y-4 p-6">
                <div>
                    <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Email</p>
                    <p class="mt-2 text-lg font-medium text-white">
                        {{ contactDetails.email || 'Set STOREFRONT_CONTACT_EMAIL in your environment' }}
                    </p>
                </div>

                <div v-if="contactDetails.phone">
                    <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Phone</p>
                    <p class="mt-2 text-lg font-medium text-white">{{ contactDetails.phone }}</p>
                </div>
            </div>
        </div>

        <div class="glass-panel p-6 md:p-8">
            <form class="space-y-5" @submit.prevent="submit">
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-300">Name</span>
                        <input
                            v-model="form.name"
                            type="text"
                            maxlength="100"
                            class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-brand-400"
                        >
                        <p v-if="errors.name" class="text-sm text-rose-300">{{ errors.name }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-300">Email</span>
                        <input
                            v-model="form.email"
                            type="email"
                            maxlength="255"
                            class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-brand-400"
                        >
                        <p v-if="errors.email" class="text-sm text-rose-300">{{ errors.email }}</p>
                    </label>
                </div>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-300">Subject</span>
                    <input
                        v-model="form.subject"
                        type="text"
                        maxlength="150"
                        class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-brand-400"
                    >
                    <p v-if="errors.subject" class="text-sm text-rose-300">{{ errors.subject }}</p>
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-300">Message</span>
                    <textarea
                        v-model="form.message"
                        rows="7"
                        maxlength="5000"
                        class="w-full rounded-3xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-brand-400"
                    />
                    <p v-if="errors.message" class="text-sm text-rose-300">{{ errors.message }}</p>
                </label>

                <div class="flex items-center justify-between gap-4">
                    <p class="text-sm text-slate-400">
                        Messages are sent to your configured contact inbox and logged if delivery fails.
                    </p>
                    <button type="submit" class="primary-button shrink-0" :disabled="form.processing">
                        {{ form.processing ? 'Sending...' : 'Send message' }}
                    </button>
                </div>
            </form>
        </div>
    </section>
</template>
