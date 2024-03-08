import {createApp, h} from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import vSelect from 'vue-select';

const clickOutside = {
  beforeMount: (el, binding) => {
    el.clickOutsideEvent = event => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event);
      }
    };
    document.addEventListener("click", el.clickOutsideEvent);
  },
  unmounted: el => {
    document.removeEventListener("click", el.clickOutsideEvent);
  }
};

// Save the original setItem function
const originalSetItem = localStorage.setItem;
// Override the setItem function
localStorage.setItem = function(key, value) {
  // Optionally, you can dispatch a custom event
  const event = new CustomEvent('localStorageSetItem', { detail: { key, value } });
  window.dispatchEvent(event);

  // Call the original setItem function
  originalSetItem.apply(this, arguments);
};

createInertiaApp({
  progress: false,
  resolve: name => {
    const pages = import.meta.glob('./views/**/*.vue', {eager: true})
    return pages[`./views/${name}.vue`]
  },
  setup({el, App, props, plugin}) {
    createApp({render: () => h(App, props)})
      .component('v-select', vSelect)
      .directive("click-outside", clickOutside)
      .use(plugin)
      .mount(el)
  }
})

Object.defineProperty(String.prototype, "ucfirst", {
  value: function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
  },
  enumerable: !1
});
