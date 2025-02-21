import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { router } from '@inertiajs/vue3';

let stale = false;
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

window.addEventListener('popstate', () => stale = true);

router.on('navigate', (event) => {
  if (stale) {
    router.get(event.detail.page.url, {}, { replace: true, preserveState: false });
  }
  stale = false;
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
    // resolve: name => {
    //     console.log('Page name to resolve:', name); // 追加
    //     const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
    //     const page = pages[`./Pages/${name}.vue`];
    //     if (!page) {
    //         console.error(`Page not found: ./Pages/${name}.vue`); // 追加
    //         console.log('Available pages:', Object.keys(pages)); // 追加
    //     }
    //     return page;
    // },
    // setup({ el, App, props, plugin }) {
    //     console.log('Inertia setup with props:', props); // 追加
    //     createApp({ render: () => h(App, props) })
    //         .use(plugin)
    //         .mount(el);
    // },
});
