<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">{{ title }}</div>
      <div class="popup-body-wrap">
        <p class="popup-text-row big-text" v-if="caption.length" v-html="caption"></p>

        <div class="popup-simple-list-wrap">
          <ul class="scrollbar" v-if="items.length">
            <li v-for="item in items">
              <span class="item-text">{{ item }}</span>
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