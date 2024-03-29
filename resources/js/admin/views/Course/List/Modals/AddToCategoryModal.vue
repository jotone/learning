<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap category-popup">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">Add to Category</div>

      <form class="popup-body-wrap" @submit.prevent="handle">
        <Label caption="Select the category to add the selected courses:">
          <select class="form-select" @change="changeSelected">
            <option
              v-for="category in categories"
              :selected="selected.id === category.id"
              :value="category.id"
            >
              {{ category.name }}
            </option>
          </select>
        </Label>

        <p class="popup-text-row">
          The courses you want to enroll to “{{ selected.name }}” are the following:
        </p>

        <div class="popup-items-list-wrap">
          <ul class="scrollbar" v-if="items.length">
            <li v-for="item in items">
              <span class="item-text">{{ item.text }}</span>
              <span class="item-remove" @click="removeItem(item.id)"><i class="icon close-icon"></i></span>
            </li>
          </ul>
        </div>

        <p class="popup-text-row">Type <b>Add</b> to confirm.</p>

        <div class="popup-confirmation-wrap">
          <div class="popup-confirmation-field">
            <input class="form-input" placeholder="Add" v-model.trim="confirmationText">
            <span class="hint-message" v-if="!confirmationText.length">Can't be blank</span>
          </div>
          <div class="popup-confirmation-button">
            <button class="btn blue" type="submit" :disabled="confirmationText !== confirmation">
              Add
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
// Mixin
import {DefaultPopupMixin} from "../../../../../mixins/default-popup-mixin.js";
import {Label} from "../../../../components/Form/index.js";

export default {
  components: {Label},
  mixins: [DefaultPopupMixin],
  props: {
    getCategories: {
      type: Function,
      required: true
    }
  },
  data() {
    return {
      categories: [],
      confirmation: 'Add',
      confirmationText: "",
      selected: null
    }
  },
  methods: {
    /**
     * Select category
     * @param e
     */
    changeSelected(e) {
      const value = parseInt(e.target.value);
      for (let i = 0, n = this.categories.length; i < n; i++) {
        if (this.categories[i].id === value) {
          this.selected =  this.categories[i];
          break;
        }
      }
    },
    /**
     * Open modal window
     * @param {Array} courses
     * @return {Promise<unknown>}
     */
    open(courses) {
      this.items = courses;
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
    },
    /**
     * Click button handler
     */
    handle() {
      this.resolver({
        items: this.items.map(item => item.id),
        category: this.selected.id
      })
      this.active = false;
      if (typeof this.reset === 'function') {
        this.reset();
      }
    }
  },
  beforeMount() {
    this.getCategories().then(categoryList => {
      this.categories = categoryList.data;
      if (this.categories.length) {
        this.selected = this.categories[0];
      }
    })
  }
}
</script>