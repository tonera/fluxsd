import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18nVue,loadLanguageAsync } from 'laravel-vue-i18n';

const appName = import.meta.env.VITE_APP_NAME || 'FluxSD';

 
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        
        console.log(props.initialPage.props.locale);
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18nVue, {
                // locale:props.initialPage.props.locale,
                lang:props.initialPage.props.locale,
                // resolve:(lang) => import(`../../lang/${lang}.json`)
                resolve: async lang => {
                    const langs = import.meta.glob('../../lang/*.json');
                    return await langs[`../../lang/${lang}.json`]();
                }
            })
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
