<template>
  <header>
    <div class="page-name-wrap">
      <h1>{{ $attrs.course.name }}</h1>

      <StatusInfo caption="Active" :type="courseStatus"/>
    </div>
  </header>

  <Notifications/>

  <TopMenu :menu="$attrs.top_menu"/>

  <form id="courseForm" @submit.prevent="submit">
    <fieldset class="card">
      <legend title="General Information">General Information</legend>

      <div class="row">
        <div class="col-2-3">
          <div class="row padding">
            <div class="col-1-2">
              <Label caption="Title">
                <InputText
                  placeholder="Title..."
                  :enableProgress="true"
                  :max="120"
                  :required="true"
                  :style="{'padding-right': '40px'}"
                  v-model="form.name"
                />
              </Label>
            </div>

            <div class="col-1-2">
              <Label caption="Status">
                <select class="form-select" v-model="form.status">
                  <option v-for="status in $attrs.statuses" :value="status">{{ status }}</option>
                </select>
              </Label>
            </div>
          </div>

          <div class="row padding">
            <div class="col-1-2">
              <Label caption="Course URL" v-if="0 === page.props.auth.role.level">
                <InputText placeholder="Course URL..." :required="true" v-model="form.url"/>
              </Label>
            </div>
          </div>

          <div class="row padding">
            <div class="col-1-2">
              <Label caption="Sales Page URL">
                <InputText placeholder="Sales Page Url..." v-model="form.sale_page_url"/>
              </Label>
            </div>

            <div class="col-1-2">
              <Label caption="Expiry Link URL">
                <InputText placeholder="Expiry Link Url..." v-model="form.expire_url"/>
              </Label>
            </div>
          </div>

          <div class="row padding">
            <Label class="col" caption="Description">
              <TextArea placeholder="Short Description..." v-model="form.description"/>
            </Label>
          </div>

          <div class="row padding">
            <Label caption="Category" class="col">
              <MultipleSelector
                :options="$attrs.categories"
                :selected="form.categories"
                @change="categorySelected"
              />
            </Label>
          </div>

          <div class="form-row padding">
            <SliderCheckbox
              name="terms_conditions_enable"
              text="Terms & Conditions"
              :checked="form.terms_conditions_enable"
              @change="setFormValue"
            />

            <Label class="col" caption="Terms & Conditions Description">
              <TextArea placeholder="Terms & Conditions Description..." v-model="form.terms_conditions_text"/>
            </Label>
          </div>

          <div class="form-row padding">
            <SliderCheckbox
              name="optional_duration_enable"
              text="Set course as optional"
              :checked="form.optional_duration > 0"
            />
          </div>

          <div class="row padding">
            <div class="col-1-2">
              <Label caption="Default Duration">
                <select name="optional_duration" class="form-select" v-model="form.optional_duration">
                  <option :value="0">Unlimited</option>
                  <option v-for="i in 12" :value="i">
                    {{ i }} {{ i > 1 ? 'months' : 'month' }}
                  </option>
                </select>
              </Label>
            </div>
            <div class="col-1-2">
              <Label caption="Expire Sale Page (Optional)">
                <InputText
                  placeholder="Link to expire sale page..."
                  v-model="form.optional_expire_page"
                />
              </Label>
            </div>
          </div>
        </div>

        <div class="col-1-3 form-row padding">
          <ImageUpload
            ref="imageUpload"
            placeholderText="Upload Course Image"
            :value="form.img_url"
            @onRemove="clearImage"
          />

          <div class="form-row">
            <Label caption="Actions">
              <ul class="actions-list">
                <li
                  v-for="action in actions"
                  :title="action?.title"
                  @click="action.hasOwnProperty('callback') && action.callback()"
                >
                  <template v-if="action.hasOwnProperty('link')">
                    <a :href="action.link"><i class="icon" :class="action.icon"></i></a>
                  </template>

                  <template v-else>
                    <span><i class="icon" :class="action.icon"></i></span>
                  </template>
                </li>
              </ul>
            </Label>
          </div>

          <button class="btn blue" type="submit" style="min-width: 162px">
            Save Changes
          </button>
        </div>
      </div>
    </fieldset>
  </form>

  <RemovePopup
    title="Are you sure you want to delete this Course?"
    ref="removeCourseModal"
    :listMessages="{
      bottom: ['This will delete all content irrevocably.', 'Type <b>Delete</b> to confirm.']
    }"
  />
</template>

<script setup>
// Vue libs
import {inject, reactive, ref} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Components
import {ImageUpload, Label, MultipleSelector, SliderCheckbox, TextArea} from "../../../components/Form";
import {Notifications, StatusInfo} from "../../../components/Default";
import TopMenu from "../../../components/Menu/TopMenu.vue";
// Layout
import Layout from '../../../shared/Layout.vue';
import {InputText} from "../../../components/Form/index.js";
import {Notification} from "../../../libs/Notification.js";
import RemovePopup from "../../../components/Popup/RemovePopup.vue";

defineOptions({layout: Layout});

// Assign the file upload query function
const graphQlFileUploadQuery = inject('graphQlFileUploadQuery')
// Assign the GraphQL form serialization function
const serialize = inject('graphQlSerializeForm')
// Assign the http request function
const request = inject('request');
// Assign the GraphQL request function
const requestGraphQL = inject('requestGraphQL');

// Page variables
const page = usePage();

/*
 * --------------- Forms ---------------
 */

let form = reactive({
  id: page.props.course.id,
  name: page.props.course?.name,
  url: page.props.course?.url,
  img_url: page.props.course?.img_url,
  status: page.props.course?.status || 'draft',
  sale_page_url: page.props.course?.sale_page_url,
  expire_url: page.props.course?.expire_url,
  description: page.props.course?.description,
  terms_conditions_enable: page.props.course?.terms_conditions_enable || false,
  terms_conditions_text: page.props.course?.terms_conditions_text,
  optional_duration: page.props.course?.optional_duration || 0,
  optional_expire_page: page.props.course?.optional_expire_page,
  categories: (() => page.props.course?.categories?.map(
    category => ({
      id: category.pivot.category_id,
      name: category.category_name
    })
  ) ?? [])(),
})

// Course remove modal referral variable
const removeCourseModal = ref(null);

// Default course status class
let courseStatus = page.props.course.status === 'active'
  ? 'success'
  : page.props.course.status === 'coming_soon' ? 'warning' : null;

/**
 * Set the list of categories
 * @param {Array} categories
 */
const categorySelected = categories => {
  form.categories = categories ?? [];
}

/**
 * Change form value
 * @param status
 * @param field
 */
const setFormValue = (status, field) => {
  form[field] = status
}

/**
 * Form submit handler
 */
const submit = () => {
  form.img_url = imageUpload.value.getData();
  form.categories = form.categories.map(item => item.id)
  // Build GraphQl query
  let query = serialize('update', form, [
    'id',
    'name',
    'url',
    'img_url',
    'status',
    'sale_page_url',
    'expire_url',
    'description',
    'terms_conditions_enable',
    'terms_conditions_text',
    'categories{id,name}'
  ], ['img_url'])

  // Update the course model
  requestGraphQL(page.props.routes.course.api, query).then(response => {
    if (response.data.hasOwnProperty('data')) {
      try {
        // Upload the category image
        if (typeof form.img_url !== 'string' && null !== form.img_url) {
          requestGraphQL(
            page.props.course.routes.api,
            graphQlFileUploadQuery(form.img_url, page.props.course.id, 'UpdateCourse', 'img_url'),
            {'Content-Type': 'multipart/form-data'}
          ).then(response => {
            form.img_url = response.data.data.update.img_url;
          })
        }
      } catch (e) {
      } finally {
        // Show notification
        Notification.success(`Course "${response.data.data.update.name}" was successfully modified.`)
      }
    }
  })
}


/*
 * --------------- Image Uploader ---------------
 */
// Image upload referral variable
const imageUpload = ref(null);

/**
 * Send a request to remove the image resource
 */
const clearImage = () => {
  let imgUrl = null;
  if (typeof form.img_url === 'object' && null !== form.img_url) {
    imgUrl = form.img_url?.original;
  }
  if (typeof form.img_url === 'string') {
    imgUrl = form.img_url;
  }

  null !== imgUrl && request({
    url: page.props.routes.img,
    method: 'delete',
    data: {
      path: imgUrl,
      entity: 'App\\Models\\Course',
      entity_id: page.props.course?.id,
      field: 'img_url'
    }
  })
}

/*
 * --------------- Course Actions List ---------------
 */

// List of page actions
const actions = [
  {
    icon: 'eye-icon',
    link: '#',
    title: 'View a course'
  }, {
    icon: 'copy-icon',
    title: 'Duplicate this course',
    callback: () => {
      console.log('duplicate')
    },
  }, {
    icon: 'trash-icon',
    title: 'Delete this course',
    callback: () => {
      removeCourseModal.value.open([{
        id: page.props.course.id,
        text: page.props.course.name
      }]).then(result => {
        if (false !== result && Array.isArray(result) && typeof result[0] !== 'undefined') {
          requestGraphQL(
            page.props.routes.course.api,
            `mutation {destroy(id:${result[0].id}){id}}`
          ).then(response => {
            if (typeof response.data.data !== 'undefined' && 'destroy' in response.data.data) {
              window.location = page.props.routes.course.index;
            }
          })
        }
      })
    }
  }
]
</script>