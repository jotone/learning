<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup">
      <div class="default-popup__close" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup__title">{{ title }}</div>

      <div class="popup__body">
        <p class="popup__text-row popup__text-row--big-text" v-if="caption.length" v-html="caption"></p>

        <template v-if="'top' in listMessages">
          <template v-if="typeof listMessages.top === 'string'">
            <p class="popup__text-row" v-html="listMessages.top"></p>
          </template>
          <template v-else>
            <p class="popup__text-row" v-for="message in listMessages.top" v-html="message"></p>
          </template>
        </template>

        <div class="popup__list--items">
          <ul class="scrollbar" v-if="items.length">
            <li v-for="item in items">
              <span class="item__text">{{ item.text }}</span>
              <span class="item__remove" @click="removeItem(item.id)"><i class="icon close-icon"></i></span>
            </li>
          </ul>
        </div>

        <template v-if="'bottom' in listMessages">
          <template v-if="typeof listMessages.bottom === 'string'">
            <p class="popup__text-row" v-html="listMessages.bottom"></p>
          </template>
          <template v-else>
            <p class="popup__text-row" v-for="message in listMessages.bottom" v-html="message"></p>
          </template>
        </template>

        <form class="popup__confirmation" @submit.prevent="handle">
          <div class="popup__confirmation__field">
            <input class="form-input danger" :placeholder="button.confirmation" v-model.trim="confirmationText">
            <span class="popup__confirmation__circle-warning">!</span>
            <span class="popup__confirmation__hint-message" v-if="!confirmationText.length">Can't be blank</span>
          </div>
          <div class="popup__confirmation__button">
            <button
              class="btn"
              type="submit"
              :class="button.class"
              :disabled="confirmationText !== button.confirmation"
            >
              {{ button.text }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
// Mixin
import {DefaultPopupMixin} from "../../../mixins/default-popup-mixin.js";

export default {
  mixins: [DefaultPopupMixin],
  data() {
    return {
      confirmationText: ''
    }
  },
  props: {
    button: {
      type: Object,
      default: {
        class: 'danger',
        confirmation: 'Delete',
        text: 'Delete'
      }
    },
    caption: {
      type: String,
      default: 'Kindly note that this action <b>can not be undone.</b>'
    },
    title: {
      type: String,
      required: true
    },
    listMessages: {
      type: Object,
      default: {}
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
      this.confirmationText = '';
      this.active = true;

      return new Promise(resolve => {
        this.resolver = resolve
      })
    },
    /**
     * Remove an element from the modal window list
     * @param {int} id
     */
    removeItem(id) {
      // Search for the item
      for(let i = 0, n = this.items.length; i < n; i++) {
        // Item found by ID
        if (this.items[i].id === id) {
          // Remove item
          this.items.splice(i, 1)
          // Close the modal window if the list is empty
          if (!this.items.length) {
            this.active = false
            this.resolver(false)
          }
          break;
        }
      }
    }
  }
}
</script>