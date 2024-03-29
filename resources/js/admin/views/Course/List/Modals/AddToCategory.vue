<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap category-popup">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">Add to Category</div>

      <form class="popup-body-wrap" @submit.prevent="submit">
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

        <p class="popup-text-row">The courses you want to enroll to “{{ selected.name }}” are the following:</p>
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
      selected: null
    }
  },
  methods: {
    changeSelected(e) {
      const value = parseInt(e.target.value);
      for (let i = 0, n = this.categories.length; i < n; i++) {
        if (this.categories[i].id === value) {
          this.selected =  this.categories[i];
          break;
        }
      }
    },
    open(courses) {
      this.active = true;
      return new Promise(resolve => {
        this.resolver = resolve
      })
    },
    submit() {
      console.log('add to cat')
    }
  },
  beforeMount() {
    this.getCategories().then(categoryList => {
      this.categories = categoryList.data;
      if (this.categories.length) {
        this.selected = this.categories[0];
      }
    })
  },
  watch: {
    categories(newVal) {
      console.log(newVal)
    }
  }
}
</script>