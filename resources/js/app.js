import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, createSSRApp, h } from 'vue';

const appName = import.meta.env.SSR
    ? 'Pop Attack'
    : document.title || 'Pop Attack';

createInertiaApp({
    title: (title) => (title ? `${title} | ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    progress: {
        color: '#8b5cf6',
        showSpinner: false,
    },
    setup({ el, App, props, plugin }) {
        const vueAppFactory = import.meta.env.SSR ? createSSRApp : createApp;
        const app = vueAppFactory({ render: () => h(App, props) }).use(plugin);

        if (import.meta.env.SSR) {
            return app;
        }

        app.mount(el);
    },
});