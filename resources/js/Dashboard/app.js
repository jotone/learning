import {createApp, h} from "vue";
import {createInertiaApp} from "@inertiajs/vue3";
import axios from "axios";
import jquery from "jquery";
import "select2/dist/js/select2.min"

window.$ = jquery
window.axios = axios

axios.get('/api/language', {params: {files: ["common"]}}).then(response => {
  if (200 === response.status) {
    window.translations = response.data;

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
              lang: (translation, ...args) => {
                const path = translation.split('.')
                let value = window.translations
                for (let i = 0, n = path.length; i < n; i++) {
                  if (value.hasOwnProperty(path[i])) {
                    value = value[path[i]]
                  } else {
                    value = path;
                    break;
                  }
                }
                return value.replace(/:[a-z]+/g, () => args.shift())
              }
            }
          })
          .mount(el)
      }
    })
  }
})

Object.defineProperty(String.prototype, 'ucfirst', {
  value: function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
  },
  enumerable: false
});
