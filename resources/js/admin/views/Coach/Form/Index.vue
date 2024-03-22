<template>
  <header>
    <div class="page-name-wrap">
      <h1 v-html="$attrs.hasOwnProperty('model') ? 'Edit Coach' : 'Create Coach'"></h1>

      <button class="btn" form="coachForm" type="submit">
        <span>Save Changes</span>
      </button>
    </div>
  </header>

  <Notifications/>

  <form id="coachForm" @submit.prevent="submit">
    <div class="col-1-2">
      <fieldset class="card">
        <legend title="Coach Details">Coach Details</legend>

        <div class="form-row padding">
          <Label caption="First Name" class="col">
            <InputText placeholder="First Name..." :required="true" v-model="form.first_name"/>
          </Label>

          <Label caption="Last Name" class="col">
            <InputText placeholder="Last Name..." :required="true" v-model="form.last_name"/>
          </Label>

          <Label caption="Last Name" class="col">
            <InputText placeholder="Email..." type="email" :required="true" v-model="form.email"/>
          </Label>

          <Label caption="Enter Password" class="col">
            <InputText placeholder="Enter Password..." type="password" v-model="form.password"/>
          </Label>

          <Label caption="Confirm Password" class="col">
            <InputText placeholder="Confirm Password..." type="password" v-model="form.confirmation"/>
          </Label>
        </div>
      </fieldset>
    </div>
  </form>
</template>

<script setup>
// Vue libs
import {inject, reactive} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Other Libs
import {Notification} from '../../../libs/Notification';
// Components
import Notifications from '../../../components/Default/Notifications.vue';
// Layout
import Layout from '../../../shared/Layout.vue';
import {InputText, Label} from "../../../components/Form/index.js";

defineOptions({layout: Layout})

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
 * Send form request
 * @param e
 */
const submit = e => {
  // The mutation type depends on if a model exists or not
  const mutationType = page.props.hasOwnProperty('model') ? 'update' : 'create';

  let query = serialize(mutationType, form, 'id,first_name,last_name,email')

  requestGraphQL(page.props.routes.api, query)
    .then(response => {
      if (response.data.hasOwnProperty('data')) {
        // Show notification
        Notification.success(
          page.props.hasOwnProperty('model')
            ? `Coach "${response.data.data.update.email}" was successfully modified.`
            : `Coach "${response.data.data.create.email}" was successfully created.`
        )

        // Reset form if created
        if (!page.props.hasOwnProperty('model')) {
          form.first_name = '';
          form.last_name = '';
          form.email = '';
          form.password = '';
          form.confirmation = '';
          e.target.reset();
        }
      }
    })
}

/*
 * Variables
 */
let form = reactive({
  first_name: page.props?.model?.first_name,
  last_name: page.props?.model?.last_name,
  email: page.props?.model?.email,
  role_id: page.props.role_id,
  password: null,
  confirmation: null
})
</script>