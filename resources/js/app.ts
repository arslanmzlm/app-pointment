import '../css/app.css';
import '../css/satoshi.css';
import 'primeicons/primeicons.css';
import './bootstrap';
import {primeVueOptions} from '@/primeVue';
import {createInertiaApp, Link} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {createPinia} from 'pinia';
import {ToastService, Tooltip} from 'primevue';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import {createApp, DefineComponent, h} from 'vue';
import {ZiggyVue} from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue)
            .use(createPinia())
            .use(PrimeVue, primeVueOptions)
            .use(ToastService)
            .use(ConfirmationService)
            .directive('tooltip', Tooltip)
            .component('Link', Link)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
