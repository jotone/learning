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
                <InputText placeholder="Sales Page Url..." v-model.trim="form.sale_page_url"/>
              </Label>
            </div>

            <div class="col-1-2">
              <Label caption="Expiry Link URL">
                <InputText placeholder="Expiry Link Url..." v-model.trim="form.expire_url"/>
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
              <MultipleSelector :options="$attrs.categories"/>
            </Label>
          </div>

          <div class="form-row padding">
            <SliderCheckbox
              name="terms_conditions_enable"
              text="Terms & Conditions"
              :checked="form.terms_conditions_enable"
            />

            <Label class="col" caption="Terms & Conditions Description">
              <TextArea placeholder="Terms & Conditions Description..." v-model="form.terms_conditions_text"/>
            </Label>
          </div>
        </div>

        <div class="col-1-3 form-row padding">
          <ImageUpload
            ref="imageUpload"
            placeholderText="Upload Course Image"
            :value="form.img_url"
            @onRemove="clearImage"
          />

          <button type="submit">
            Save
          </button>
        </div>
      </div>
    </fieldset>
  </form>
</template>

<script setup>
// Vue libs
import {inject, reactive} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Components
import {CircleProgress, ImageUpload, Label, MultipleSelector, SliderCheckbox, TextArea} from "../../../components/Form";
import {Notifications, StatusInfo} from "../../../components/Default";
import TopMenu from "../../../components/Menu/TopMenu.vue";
// Layout
import Layout from '../../../shared/Layout.vue';
import {InputText} from "../../../components/Form/index.js";

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
  console.log(form)
}

console.log(page.props)
/*
 * Variables
 */
let courseStatus = page.props.course.status === 'active'
  ? 'success'
  : page.props.course.status === 'coming_soon' ? 'warning' : null;

let form = reactive({
  name: page.props.course?.name,
  url: page.props.course?.url,
  img_url: page.props.course?.img_url,
  status: page.props.course?.status || 'draft',
  sale_page_url: page.props.course?.sale_page_url,
  expire_url: page.props.course?.expire_url,
  description: page.props.course?.description,
  category_id: page.props.course?.category_id,
  terms_conditions_enable: page.props.course?.terms_conditions_enable || false,
  terms_conditions_text: page.props.course?.terms_conditions_text,
})
</script>