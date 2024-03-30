<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap category-popup">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">{{ title }}</div>

      <form class="popup-body-wrap" @submit.prevent="handle">
        <Label :caption="text">
          <select class="form-select" @change="changeSelected">
            <option
              v-for="option in options"
              :selected="selected.id === option.id"
              :value="option.id"
            >
              {{ option.name }}
            </option>
          </select>
        </Label>

        <p
          class="popup-text-row"
          v-if="caption.length"
          v-html="caption.replace(':entity', selected.name)"
        ></p>

        <div class="popup-items-list-wrap">
          <ul class="scrollbar" v-if="items.length">
            <li v-for="item in items">
              <span class="item-text">{{ item.text }}</span>
              <span class="item-remove" @click="removeItem(item.id)"><i class="icon close-icon"></i></span>
            </li>
          </ul>
        </div>

        <p class="popup-text-row">Type <b>{{ button.confirmation }}</b> to confirm.</p>

        <div class="popup-confirmation-wrap">
          <div class="popup-confirmation-field">
            <input class="form-input" :placeholder="button.confirmation" v-model.trim="confirmationText">
            <span class="hint-message" v-if="!confirmationText.length">Can't be blank</span>
          </div>
          <div class="popup-confirmation-button">
            <button
              class="btn"
              type="submit"
              :class="button.class"
              :disabled="confirmationText !== button.confirmation"
            >
              {{ button.text }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import {DefaultPopupMixin} from "../../../mixins/default-popup-mixin.js";
import {Label} from "../Form/index.js";

export default {
  components: {Label},
  mixins: [DefaultPopupMixin],
  data() {
    return {
      confirmationText: '',
      options: [],
      selected: null
    }
  },
  props: {
    button: {
      type: Object,
      default: {
        class: 'blue',
        confirmation: 'Add',
        text: 'Add'
      }
    },
    caption: {
      type: String,
      default: ''
    },
    text: {
      type: String,
      default: ''
    },
    title: {
      type: String,
      required: true
    }
  },
  methods: {
    isNumeric(val) {
      return !isNaN(val) && !isNaN(parseFloat(val));
    },
    /**
     * Select category
     * @param e
     */
    changeSelected(e) {
      let value = e.target.value;
      value = this.isNumeric(value) ? parseInt(value) : value;

      for (let i = 0, n = this.options.length; i < n; i++) {
        if (this.options[i].id === value) {
          this.selected =  this.options[i];
          break;
        }
      }
    },
    /**
     * Open modal window
     * @param {Array} items
     * @param {Array} options
     * @return {Promise<unknown>}
     */
    open(items, options) {
      this.items = items;
      this.options = options

      if (this.options.length) {
        this.selected = this.options[0];
        this.active = true;
      }

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
    },
    /**
     * Click button handler
     */
    handle() {
      this.resolver({
        items: this.items.map(item => item.id),
        option: this.selected.id
      })
      this.active = false;
      if (typeof this.reset === 'function') {
        this.reset();
      }
    }
  }
}
</script>