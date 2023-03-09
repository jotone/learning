import {createApp, h} from "vue";
import {createInertiaApp} from "@inertiajs/vue3";
import {InertiaProgress} from "@inertiajs/progress"
import axios from 'axios';

window.$.axios = axios

createInertiaApp({
  resolve: name => require(`./Pages/${name}.vue`),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
})

InertiaProgress.init({
  color: '#005AFF'
})

