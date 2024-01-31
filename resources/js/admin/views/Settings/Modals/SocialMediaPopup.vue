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
              textField="text"
              valueField="value"
              :options="options"
              :optionRow="selectorRow"
              :value="items.type"
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
import {DefaultPopupMixin} from "../../../../mixins/default-popup-mixin.js";
import CustomSelector from "../../../components/Form/CustomSelector.vue";

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
  beforeMount() {
    this.reset();
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

      if (null !== item) {
        this.items = {
          id: item.id,
          type: item.type,
          caption: item.caption,
          icon: item.icon
        }
      }

      return new Promise(resolve => {
        this.resolver = resolve
      })
    },
    selectorRow(option) {
      return `<i class="icon ${option.icon}"></i><span>${option.text}</span>`
    },
    reset() {
      this.type = 'add';
      this.items = {
        id: null,
        type: this.options[0].value,
        caption: '',
        icon: ''
      }
    },
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