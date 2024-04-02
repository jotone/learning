<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup">
      <div class="default-popup__close" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup__title">{{ title }}</div>
      <div class="popup__body">
        <p class="popup__text-row popup__text-row--big-text" v-if="caption.length" v-html="caption"></p>

        <div class="popup__list--simple">
          <ul class="scrollbar" v-if="items.length">
            <li v-for="item in items">
              <span class="item__text">{{ item }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// Mixin
import {DefaultPopupMixin} from "../../../mixins/default-popup-mixin.js";

export default {
  mixins: [DefaultPopupMixin],
  props: {
    caption: {
      type: String,
      default: ''
    },
    title: {
      type: String,
      required: true
    }
  },
  methods: {
    /**
     * Open modal window
     * @param {Array} list
     * @return {Promise<unknown>}
     */
    open(list) {
      this.items = list;
      this.active = true;

      return new Promise(resolve => {
        this.resolver = resolve
      })
    }
  }
}
</script>