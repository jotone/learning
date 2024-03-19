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
              <label class="caption">
                <span>Title</span>
                <input
                  autocomplete="off"
                  class="form-input"
                  name="title"
                  placeholder="Title..."
                  required
                  style="padding-right: 40px"
                  v-model.trim="form.name"
                >

                <CircleProgress style="right: 25px" max="120" :current="form.name.length || 0"/>
              </label>
            </div>

            <div class="col-1-2">
              <label class="caption">
                <span>Status</span>
                <select class="form-select" v-model="form.status">
                  <option v-for="status in $attrs.statuses" :value="status">{{ status }}</option>
                </select>
              </label>
            </div>
          </div>

          <div class="row padding">
            <div class="col-1-2">
              <label class="caption" v-if="0 === page.props.auth.role.level">
                <span>Course URL</span>
                <input
                  autocomplete="off"
                  class="form-input"
                  name="url"
                  placeholder="Course URL..."
                  v-model.trim="form.url"
                >
              </label>
            </div>
          </div>

          <div class="row padding">
            <div class="col-1-2">
              <label class="caption">
                <span>Sales Page URL</span>
                <input
                  autocomplete="off"
                  class="form-input"
                  name="sale_page_url"
                  placeholder="Sales Page Url..."
                  v-model.trim="form.sale_page_url"
                >
              </label>
            </div>

            <div class="col-1-2">
              <label class="caption">
                <span>Expiry Link URL</span>
                <input
                  autocomplete="off"
                  class="form-input"
                  name="expire_url"
                  placeholder="Expiry Link Url..."
                  v-model.trim="form.expire_url"
                >
              </label>
            </div>
          </div>

          <div class="row padding">
            <label class="caption col">
              <span>Description</span>
              <textarea
                class="form-text"
                name="description"
                placeholder="Short Description"
                v-model.trim="form.description"
              ></textarea>
            </label>
          </div>

          <div class="row padding">
            <label class="caption col-1-2">
              <span>Category</span>
              <select class="form-select" v-model="form.category_id">
                <option :value="null">No category</option>
                <option v-for="category in $attrs.categories" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </label>
          </div>
        </div>

        <div class="col-1-3">
          <ImageUpload
            ref="imageUpload"
            placeholderText="Upload Course Image"
            :value="form.img_url"
            @onRemove="clearImage"
          />
        </div>
      </div>
    </fieldset>
  </form>
</template>

<script setup>
// Vue libs
import {inject, reactive, ref} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Components
import Notifications from "../../../components/Default/Notifications.vue";
import StatusInfo from "../../../components/Default/StatusInfo.vue";
// Layout
import Layout from '../../../shared/Layout.vue';
import TopMenu from "../../../components/Default/TopMenu.vue";
import ImageUpload from "../../../components/Form/ImageUpload.vue";
import {CircleProgress} from "../../../components/Form/index.js";

defineOptions({layout: Layout})

// Assign the GraphQL request function
const requestGraphQL = inject('requestGraphQL')
// Page variables
const page = usePage();

/*
 * Methods
 */
const clearImage = () => {

}

const submit = () => {

}

console.log(page.props)
/*
 * Variables
 */
let courseStatus = page.props.course.status === 'active'
  ? 'success'
  : page.props.course.status === 'coming_soon' ? 'warning' : null;

const form = {
  name: page.props.course?.name,
  url: page.props.course?.url,
  img_url: page.props.course?.img_url,
  status: page.props.course?.status || 'draft',
  sale_page_url: page.props.course?.sale_page_url,
  expire_url: page.props.course?.expire_url,
  description: page.props.course?.description,
  category_id: page.props.course?.category_id,
}
</script>