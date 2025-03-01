import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { createApp, DefineComponent, h } from 'vue';
import { createI18n } from 'vue-i18n';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import '../css/app.css';
import './bootstrap';

const messages = Object.fromEntries(
    Object.entries(
        import.meta.glob('../../libs/i18n/locales/**/*.json', { eager: true }),
    ).map(([key, value]) => {
        const locale = key.match(/\/([^/]+)\.json$/)?.[1];
        return [locale, (value as { default: any }).default];
    }),
);

const i18n = createI18n({
    legacy: false,
    locale: localStorage.getItem('locale') || 'es',
    fallbackLocale: 'en',
    messages,
});

const appName = import.meta.env.VITE_APP_NAME || 'Peluanita';
const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
