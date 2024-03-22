<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap category-popup">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">Create Course</div>

      <form class="popup-body-wrap" @submit.prevent="submit">
        <Label caption="Name of the Course">
          <TextArea
            placeholder="Name of the Course..."
            style="{'min-height': '85px'}"
            max="90"
            ref="nameProgress"
            v-model="form.name"
            :enableProgress="true"
            :showRemnant="true"
            @onInput="checkStatus"
          />
        </Label>

        <Label caption="Course Description">
          <TextArea
            placeholder="Course Description..."
            style="{'min-height': '200px'}"
            max="300"
            ref="descriptionProgress"
            v-model="form.description"
            :enableProgress="true"
            :showRemnant="true"
            @onInput="checkStatus"
          />
        </Label>

        <Label caption="Status">
          <select class="form-select" v-model="form.status">
            <option v-for="status in statuses" :value="status">
              {{ status }}
            </option>
          </select>
        </Label>

        <div class="row form-row" style="justify-content: flex-end">
          <button class="btn blue" type="submit" :disabled="preventSave">
            Create Course
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
// Mixin
import {DefaultPopupMixin} from "../../../../../mixins/default-popup-mixin.js";
import {Label, TextArea} from "../../../../components/Form/index.js";

export default {
  components: {Label, TextArea},
  mixins: [DefaultPopupMixin],
  data() {
    return {
      preventSave: true,
      form: {
        name: '',
        description: '',
        status: 'active'
      },
    }
  },
  props: {
    statuses: {
      type: Array,
      required: true
    }
  },
  methods: {
    checkStatus() {
      this.preventSave = this.$refs.nameProgress.getStatus() > 1
        || this.$refs.descriptionProgress.getStatus() > 1
        || this.form.name?.length < 1
        || this.form.description?.length < 1;
    },
    /**
     * Open course modal window
     * @returns {Promise<unknown>}
     */
    open() {
      this.active = true;
      return new Promise(resolve => {
        this.resolver = resolve
      })
    },
    submit() {
      const query = `mutation {create (name: "${this.form.name}", description: "${this.form.description}", status: "${this.form.status}") {id}}`
      this.requestGraphQL(this.$page.props.routes.course.api, query)
        .then(response => {
          if (null !== response?.data?.data?.create) {
            this.active = false
            this.resolver(true)
          }
        })
    }
  },
  inject: ['requestGraphQL']
}
</script>