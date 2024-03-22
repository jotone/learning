<template>
  <header>
    <div class="page-name-wrap">
      <h1>Edit Category</h1>

      <button class="btn" form="coachForm" type="submit">
        <span>Save Changes</span>
      </button>
    </div>
  </header>

  <Notifications/>

  <form id="coachForm" @submit.prevent="submit">
    <fieldset class="card">
      <legend title="Category Details">Category Details</legend>

      <div class="row padding">
        <label class="caption col-1-2">
          <span>Category Name</span>
          <input
            autocomplete="off"
            class="form-input"
            name="name"
            placeholder="Category Name..."
            required
            v-model.trim="form.name"
          >
        </label>

        <label class="caption col-1-2" v-if="0 === page.props.auth.role.level">
          <span>Category Url</span>
          <input
            autocomplete="off"
            class="form-input"
            name="url"
            placeholder="Category Url..."
            v-model.trim="form.url"
          >
        </label>
      </div>

      <div class="row padding f-s-s">
        <Label class="col-1-2" caption="Short Description">
          <TextArea
            placeholder="Short Description"
            :style="{'height': '93%'}"
            v-model="form.description"
          />
        </Label>

        <div class="col-1-2">
          <label class="caption">
            <span>Category Image</span>
          </label>

          <ImageUpload
            ref="imageUpload"
            placeholderText="Upload Category Image"
            :value="form.img_url"
            @onRemove="clearImage"
          />
        </div>
      </div>

      <div class="form-row padding">
        <label class="caption">
          <span>Learn more URL</span>
          <input
            autocomplete="off"
            class="form-input"
            name="learn_more_link"
            placeholder="Learn more URL..."
            v-model.trim="form.learn_more_link"
          >
        </label>
      </div>
    </fieldset>
  </form>
</template>

<script setup>
// Vue libs
import {inject, reactive, ref} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Other Libs
import {Notification} from '../../../libs/Notification';
// Components
import {ImageUpload, Label, TextArea} from "../../../components/Form/index.js";
import Notifications from "../../../components/Default/Notifications.vue";
// Layout
import Layout from '../../../shared/Layout.vue';

defineOptions({layout: Layout})

// Assign the file upload query function
const graphQlFileUploadQuery = inject('graphQlFileUploadQuery')
// Assign the GraphQL form serialization function
const serialize = inject('graphQlSerializeForm')
// Assign the GraphQL request function
const requestGraphQL = inject('requestGraphQL')
// Page variables
const page = usePage();

/*
 * Methods
 */
/**
 * Set the Form image as null
 */
const clearImage = () => {
  form.img_url = null;
}
/**
 * Submit form handler
 */
const submit = () => {
  form.img_url = imageUpload.value.getData();
  // Build GraphQl query
  let query = serialize('update', form, 'id,name,url,img_url,description,learn_more_link,type', ['img_url'])
  // Update the category model
  requestGraphQL(page.props.routes.api, query)
    .then(response => {
      if (response.data.hasOwnProperty('data')) {
        try {
          // Upload the category image
          if (typeof form.img_url !== 'string' && null !== form.img_url) {
            requestGraphQL(
              page.props.routes.api,
              graphQlFileUploadQuery(form.img_url, page.props.model.id, 'UpdateCategory', 'img_url'),
              {'Content-Type': 'multipart/form-data'}
            ).then(response => {
              form.img_url = response.data.data.update.img_url;
            })
          }
        } catch (e) {
        } finally {
          // Show notification
          Notification.success(`Category "${response.data.data.update.name}" was successfully modified.`)
        }
      }
    })
}

/*
 * Variables
 */
let form = reactive({
  id: page.props.model.id,
  name: page.props.model.name,
  url: page.props.model.url,
  img_url: page.props.model.img_url || null,
  description: page.props.model.description,
  learn_more_link: page.props.model.learn_more_link,
  type: 'course'
})

let imageUpload = ref(null)
</script>