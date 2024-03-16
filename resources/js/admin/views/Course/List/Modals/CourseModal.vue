<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap category-popup">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">Create Course</div>

      <form class="popup-body-wrap" @submit.prevent="submit">
        <label class="caption">
          <span>Name of the Course</span>

          <textarea
            class="form-text scrollbar"
            placeholder="Name of the Course..."
            style="min-height: 85px"
            v-model.trim="form.name"
            @input="checkStatus"
          ></textarea>

          <CircleProgress
            ref="nameProgress"
            :showRemnant="true"
            :current="form.name?.length || 0"
            max="90"
          />
        </label>

        <label class="caption">
          <span>Course Description</span>

          <textarea
            class="form-text scrollbar"
            placeholder="Course Description..."
            style="min-height: 200px"
            v-model.trim="form.description"
            @input="checkStatus"
          ></textarea>

          <CircleProgress
            ref="descriptionProgress"
            :showRemnant="true"
            :current="form.description?.length || 0"
            max="300"
          />
        </label>

        <label class="caption">
          <span>Status</span>

          <select class="form-select" v-model="form.status">
            <option v-for="status in statuses" :value="status">
              {{ status }}
            </option>
          </select>
        </label>

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
import {CircleProgress} from "../../../../components/Form/index.js";

export default {
  components: {CircleProgress},
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