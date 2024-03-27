<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">{{ settings[type].title }}</div>

      <div class="popup-body-wrap">
        <form @submit.prevent="submit" :action="type === 'add' ? $page.props.routes.socials.store : editUrl">
          <label class="caption">
            <span>Type</span>

            <CustomSelector
              :options="options"
              :template="selectorRow"
              :selected="items.type"
              @change="updateTypeValue"
            />
          </label>

          <label class="caption">
            <span>Caption</span>

            <input required class="form-input" placeholder="Social media caption" v-model="items.caption">
          </label>

          <label class="caption">
            <span>Icon</span>

            <input class="form-input" placeholder="Social media icon" v-model="items.icon">
          </label>

          <div class="form-row">
            <button class="btn blue" type="submit">
              {{ settings[type].button }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
// Mixin
import {DefaultPopupMixin} from "../../../../mixins/default-popup-mixin.js";
// Components
import {CustomSelector} from "../../../components/Form";

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
          button: 'Add'
        },
        edit: {
          title: 'Edit Social Media',
          button: 'Apply'
        }
      },
      type: 'add',
      options: options
    }
  },
  props: {
    socials: {
      type: Object,
      required: true
    }
  },
  computed: {
    editUrl() {
      return this.$page.props.routes.socials.update.replace(/:id/, this.items.id)
    }
  },
  methods: {
    /**
     * Open modal window
     * @param item
     * @return {Promise<unknown>}
     */
    open(item = null) {
      this.active = true;

      this.type = null === item ? 'add' : 'edit';
      this.items = {
        id: null === item ? null : item.id,
        type: null === item ? this.options[0].value : item.type,
        caption: null === item ? '' : item.caption,
        icon: null === item ? '' : item.icon
      }

      return new Promise(resolve => {
        this.resolver = resolve
      })
    },
    /**
     * Custom selector row
     * @param option
     * @returns {string}
     */
    selectorRow(option) {
      return `<i class="icon ${option.icon}"></i><span>${option.text}</span>`
    },
    /**
     * Submit form handler
     * @param e
     */
    submit(e) {
      this.request({
        url: e.target.getAttribute('action'),
        method: this.type === 'add' ? 'post' : 'put',
        data: this.items,
        onSuccess: response => {
          if (201 === response.status || 200 === response.status) {
            this.items = response.data;
          }
          this.handle()
        }
      })
    },
    updateTypeValue(data) {
      this.items.type = data
    }
  }
}
</script>