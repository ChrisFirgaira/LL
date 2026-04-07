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
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register');
};
</script>

<template>
    <Head title="Register" />

    <section class="mx-auto w-full max-w-[620px] py-6">
        <div class="glass-panel p-7">
            <p class="divider-label">Account</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Create account</h1>
            <p class="mt-2 text-sm text-slate-600">Create a profile to track orders and speed up checkout.</p>

            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <label class="block space-y-2">
                    <span class="text-sm font-medium text-slate-700">Full name</span>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400"
                    >
                    <p v-if="errors.name" class="text-sm text-rose-600">{{ errors.name }}</p>
                </label>

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

                <div class="grid gap-4 md:grid-cols-2">
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

                    <label class="block space-y-2">
                        <span class="text-sm font-medium text-slate-700">Confirm password</span>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-400"
                        >
                    </label>
                </div>

                <button type="submit" class="primary-button w-full" :disabled="form.processing">
                    {{ form.processing ? 'Creating account...' : 'Create account' }}
                </button>
            </form>

            <p class="mt-5 text-sm text-slate-600">
                Already registered?
                <Link href="/login" class="text-link">Log in</Link>
            </p>
        </div>
    </section>
</template>
