import { createApp, h } from 'vue'
import { createInertiaApp, Form, Link } from '@inertiajs/vue3'

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue')
        return pages[`./Pages/${name}.vue`]()
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({render: () => h(App, props)})
        app.use(plugin).component("Link", Link).component("Form", Form).mount(el)
    },
})