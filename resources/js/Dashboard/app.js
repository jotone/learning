import {createApp, h} from "vue";
import {createInertiaApp} from "@inertiajs/vue3";
import axios from "axios";
import jquery from "jquery";
import "select2/dist/js/select2.min"

window.$ = jquery
window.axios = axios

axios.interceptors.request.use(config => {
  $('.preloader').show()
  return config;
});
axios.interceptors.response.use(response => {
  $('.preloader').hide()
  return response
}, error => {
  $('.preloader').hide()
  return Promise.reject(error);
})

createInertiaApp({
  resolve: name => require(`./Pages/${name}.vue`),
  setup({el, App, props, plugin}) {
    createApp({render: () => h(App, props)})
      .use(plugin)
      .mixin({
        methods: {
          /**
           * Make translation
           *
           * @param translation
           * @param args
           * @returns {*}
           */
          __: function(translation, ...args) {
            const path = translation.split('.')
            let value = this.$page.props.translations
            for (let i = 0, n = path.length; i < n; i++) {
              if (value.hasOwnProperty(path[i])) {
                value = value[path[i]]
              } else {
                value = path;
                break;
              }
            }

            if (Array.isArray(value)) {
              value = value.join('.')
            } else if (typeof value === 'object') {
              value = JSON.stringify(value)
            }
            return value.replace(/:[a-z]+/g, () => args.shift())
          }
        }
      })
      .mount(el)
  }
})

Object.defineProperty(String.prototype, 'ucfirst', {
  value: function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
  },
  enumerable: false
});
