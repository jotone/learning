<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">{{ type ? titleEdit : titleAdd }}</div>

      <div class="popup-body-wrap">
        <form>
          <label class="caption">
            <span>Type</span>

            <CustomSelector :options="options" :optionRow="selectorRow"/>
          </label>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import {DefaultPopupMixin} from "../../../mixins/default-popup-mixin.js";
import CustomSelector from "../../components/Form/CustomSelector.vue";

export default {
  components: {CustomSelector},
  mixins: [DefaultPopupMixin],
  data() {
    return {
      titleAdd: 'Add Social Media',
      titleEdit: 'Edit Social Media',
      type: false,
      form: {
        type: '',
        caption: '',
        img: ''
      },
      options: []
    }
  },
  props: {
    socials: {
      type: Object,
      required: true
    }
  },
  mounted() {
    for (let type in this.socials) {
      this.options.push({
        name: this.socials[type],
        icon: type
      })
    }
  },
  methods: {
    selectorRow(option) {
      console.log(option)
      return `<i class="icon ${option.icon}"></i><span>${option.name}</span>`
    },
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