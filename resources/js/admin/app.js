import {createApp, h} from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

const clickOutside = {
  beforeMount: (el, binding) => {
    el.clickOutsideEvent = event => {
      // here I check that click was outside the el and his children
      if (!(el === event.target || el.contains(event.target))) {
        // and if it did, call method provided in attribute value
        binding.value(event);
      }
    };
    document.addEventListener("click", el.clickOutsideEvent);
  },
  unmounted: el => {
    document.removeEventListener("click", el.clickOutsideEvent);
  }
};

createInertiaApp({
  progress: false,
  resolve: name => {
    const pages = import.meta.glob('./views/**/*.vue', {eager: true})
    return pages[`./views/${name}.vue`]
  },
  setup({el, App, props, plugin}) {
    createApp({render: () => h(App, props)})
      .directive("click-outside", clickOutside)
      .use(plugin)
      .mount(el)
  }
})