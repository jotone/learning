<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">{{ settings[type].title }}</div>

      <div class="popup-body-wrap">
        <form @submit.prevent="submit">
          <label class="caption">
            <span>Name</span>
            <input class="form-input" name="name" placeholder="Variable Name..." required v-model="items.name">
          </label>

          <label class="caption">
            <span>Entity</span>
            <select class="form-select" name="type" v-model="items.type" @change="entityChanged">
              <option v-for="(item, type) in entities.list" :value="type">
                {{ type }}
              </option>
            </select>
          </label>

          <label class="caption">
            <span>Entity Fields</span>
            <select class="form-select" name="field" v-model="items.field">
              <option v-for="(name, field) in entities.list[items.type].fields" :value="field">
                {{ name }}
              </option>
            </select>
          </label>

          <label class="caption" v-if="items.type === 'Route'">
            <span>Entity Encryption Model</span>
            <select class="form-select" v-model="items.encrypt">
              <template v-for="(fields, type) in entities.encryption">
                <option v-for="(name, field) in fields" :value="`${type}:${field}`">
                  {{ type }} {{ name }}
                </option>
              </template>
            </select>
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

export default {
  mixins: [DefaultPopupMixin],
  props: {
    entities: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      items: {
        name: '',
        type: 'User',
        field: 'first_name',
        encrypt: null
      },
      settings: {
        add: {
          title: 'Add Variable',
          button: 'Add'
        },
        edit: {
          title: 'Edit Variable',
          button: 'Apply'
        }
      },
      type: 'add'
    }
  },
  methods: {
    entityChanged() {
      this.items.field = Object.keys(this.entities.list[this.items.type].fields)[0];
    },
    /**
     * Open modal window
     * @param item
     * @return {Promise<unknown>}
     */
    open(item = null) {
      this.active = true;

      this.type = null === item ? 'add' : 'edit';

      return new Promise(resolve => {
        this.resolver = resolve
      })
    },
    submit() {
      this.handle()
    }
  }
}
</script>