<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">{{ settings[type].title }}</div>

      <div class="popup-body-wrap">
        <form @submit.prevent="submit" :action="settings[type].url">
          <label class="caption">
            <span>Type</span>

            <CustomSelector
              textField="text"
              valueField="value"
              :options="options"
              :optionRow="selectorRow"
              @change="updateTypeValue"
            />
          </label>

          <label class="caption">
            <span>Caption</span>

            <input required class="form-input" placeholder="Social media caption" v-model="form.caption">
          </label>

          <label class="caption">
            <span>Icon</span>

            <input class="form-input" placeholder="Social media icon" v-model="form.icon">
          </label>

          <button class="btn blue" type="submit">
            {{ settings[type].title }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import {DefaultPopupMixin} from "../../../mixins/default-popup-mixin.js";
import CustomSelector from "../../components/Form/CustomSelector.vue";
import axios from "axios";

export default {
  components: {CustomSelector},
  mixins: [DefaultPopupMixin],
  inject: {
    request: "request"
  },
  data() {
    let options = []
    for (let type in this.socials) {
      options.push({
        value: this.socials[type],
        text: this.socials[type].ucfirst(),
        icon: type
      })
    }

    return {
      settings: {
        add: {
          title: 'Add Social Media',
          url: this.$page.props.routes.socials.store
        },
        edit: {
          title: 'Edit Social Media',
          url: '#'
        }
      },
      type: 'add',
      form: {
        type: options[0].value,
        caption: '',
        icon: ''
      },
      options: options
    }
  },
  props: {
    socials: {
      type: Object,
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
      this.active = true;

      return new Promise(resolve => {
        this.resolver = resolve
      })
    },

    selectorRow(option) {
      return `<i class="icon ${option.icon}"></i><span>${option.text}</span>`
    },
    submit(e) {
      this.request({
        url: e.target.getAttribute('action'),
        method: this.type === 'add' ? 'post' : 'put',
        data: this.form,
        onSuccess: response => {
          if (201 === response.status) {
            this.items = response.data;
          }
          this.handle()
        }
      })
    },
    updateTypeValue(data) {
      this.form.type = data
    }
  }
}
</script>