<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

const page = usePage();
const errors = computed(() => page.props.errors ?? {});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login');
};
</script>

<template>
    <Head title="Log in" />

    <section class="mx-auto w-full max-w-[560px] py-6">
        <div class="glass-panel p-7">
            <p class="divider-label">Account</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Log in</h1>
            <p class="mt-2 text-sm text-slate-600">Sign in to view orders, checkout faster, and manage your profile.</p>

            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <label class="block space-y-2">
                    <span class="text-sm font-medium text-slate-700">Email</span>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400"
                    >
                    <p v-if="errors.email" class="text-sm text-rose-600">{{ errors.email }}</p>
                </label>

                <label class="block space-y-2">
                    <span class="text-sm font-medium text-slate-700">Password</span>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400"
                    >
                    <p v-if="errors.password" class="text-sm text-rose-600">{{ errors.password }}</p>
                </label>

                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input v-model="form.remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand-500 focus:ring-brand-400">
                    Remember me
                </label>

                <button type="submit" class="primary-button w-full" :disabled="form.processing">
                    {{ form.processing ? 'Signing in...' : 'Log in' }}
                </button>
            </form>

            <p class="mt-5 text-sm text-slate-600">
                New here?
                <Link href="/register" class="text-link">Create an account</Link>
            </p>
        </div>
    </section>
</template>
