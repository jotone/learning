<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup">
      <div class="default-popup__close" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup__title">{{ settings[type].title }}</div>

      <div class="popup__body">
        <form @submit.prevent="submit">
          <Label caption="Name">
            <InputText placeholder="Variable Name..." :required="true" v-model="items.name"/>
          </Label>

          <Label caption="Entity">
            <select class="form-select" name="type" v-model="items.type" @change="entityChanged">
              <option v-for="(item, type) in entities.list" :value="type">
                {{ type }}
              </option>
            </select>
          </Label>

          <Label caption="Entity Fields">
            <select class="form-select" name="field" v-model="items.field">
              <option v-for="(name, field) in entities.list[items.type].fields" :value="field">
                {{ name }}
              </option>
            </select>
          </Label>

          <Label caption="Entity Encryption Model">
            <select class="form-select" v-model="items.encrypt">
              <template v-for="(fields, type) in entities.encryption">
                <option v-for="(name, field) in fields" :value="`${type}:${field}`">
                  {{ type }} {{ name }}
                </option>
              </template>
            </select>
          </Label>

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
import {InputText, Label} from "../../../components/Form/index.js";

export default {
  components: {InputText, Label},
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
    /**
     * Entity change handler
     */
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