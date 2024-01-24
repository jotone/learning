<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">{{ type ? titleEdit :titleAdd }}</div>

      <div class="popup-body-wrap">
        <form>
          <label class="caption">
            <span>Type</span>
<!--            <Select2 :settings=""/>  https://stackoverflow.com/questions/57158985/adding-image-and-text-to-vue-select-dropdown-->
          </label>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Select2 from 'v-select2-component';
import {DefaultPopupMixin} from "../../../mixins/default-popup-mixin.js";

export default {
  mixins: [DefaultPopupMixin],
  components: {Select2},
  data() {
    return {
      titleAdd: 'Add Social Media',
      titleEdit: 'Edit Social Media',
      type: false,
      form: {
        type: '',
        caption: '',
        img: '',
      },
      select2: {
        settings: {minimumResultsForSearch: -1}
      }
    }
  },
  props: {
    socials: {
      type: Object,
      required: true
    }
  },
  mounted() {
    console.log(this.socials);
  },
  methods: {
    /**
     * Open modal window
     * @param {Array} list
     * @return {Promise<unknown>}
     */
    open(list) {
      this.active = true;

      return new Promise(resolve => {
        this.resolver = resolve
      })
    }
  }
}
</script>