import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import axios from "axios";
import jquery from "jquery";
import "select2/dist/js/select2.min"

window.$ = jquery
window.axios = axios

createInertiaApp({
  resolve: name => require(`./Pages/${name}.vue`),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
})

Object.defineProperty(String.prototype, 'ucfirst', {
  value: function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
  },
  enumerable: false
});
