<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">{{ title }}</div>

      <div class="popup-body-wrap">
        <p class="popup-text-row big-text" v-if="caption.length" v-html="caption"></p>

        <template v-if="'top' in listMessages">
          <template v-if="typeof listMessages.top === 'string'">
            <p class="popup-text-row" v-html="listMessages.top"></p>
          </template>
          <template v-else>
            <p class="popup-text-row" v-for="message in listMessages.top" v-html="message"></p>
          </template>
        </template>

        <div class="popup-items-list-wrap">
          <ul class="scrollbar" v-if="items.length">
            <li v-for="item in items">
              <span class="item-text">{{ item.text }}</span>
              <span class="item-remove" @click="removeItem(item.id)"><i class="icon close-icon"></i></span>
            </li>
          </ul>
        </div>

        <template v-if="'bottom' in listMessages">
          <template v-if="typeof listMessages.bottom === 'string'">
            <p class="popup-text-row" v-html="listMessages.bottom"></p>
          </template>
          <template v-else>
            <p class="popup-text-row" v-for="message in listMessages.bottom" v-html="message"></p>
          </template>
        </template>

        <form class="popup-confirmation-wrap" @submit.prevent="handle">
          <div class="popup-confirmation-field">
            <input class="form-input" :placeholder="button.confirmation" v-model.trim="confirmationText">
            <span class="circle-warning">!</span>
            <span class="hint-message" v-if="!confirmationText.length">Can't be blank</span>
          </div>
          <div class="popup-confirmation-button">
            <button class="btn" type="submit" :class="button.class" :disabled="confirmationText !== button.confirmation">
              {{ button.text }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import {DefaultPopupMixin} from "../../../mixins/default-popup-mixin.js";

export default {
  mixins: [DefaultPopupMixin],
  data() {
    return {
      confirmationText: '',
      items: []
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