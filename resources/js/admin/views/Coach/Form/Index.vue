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
        <legend>Coach Details</legend>

        <div class="form-row" style="padding: 20px 25px">
          <label class="caption" style="width: 100%">
            <span>First Name</span>
            <input
              autocomplete="off"
              class="form-input"
              name="first_name"
              placeholder="First Name..."
              required
              v-model.trim="form.first_name"
            >
          </label>

          <label class="caption" style="width: 100%">
            <span>Last Name</span>
            <input
              autocomplete="off"
              class="form-input"
              name="last_name"
              placeholder="Last Name..."
              required
              v-model.trim="form.last_name"
            >
          </label>

          <label class="caption" style="width: 100%">
            <span>Email</span>
            <input
              autocomplete="off"
              class="form-input"
              name="email"
              type="email"
              placeholder="Email..."
              required
              v-model.trim="form.email"
            >
          </label>

          <label class="caption" style="width: 100%">
            <span>Password</span>
            <input
              autocomplete="off"
              class="form-input"
              name="password"
              type="password"
              placeholder="Enter Password..."
              v-model="form.password"
            >
          </label>

          <label class="caption" style="width: 100%">
            <span>Confirm Password</span>
            <input
              autocomplete="off"
              class="form-input"
              name="confirmation"
              type="password"
              placeholder="Confirm Password..."
              v-model="form.confirmation"
            >
          </label>
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

defineOptions({layout: Layout})

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

  let query = `first_name:"${form.first_name}",last_name:"${form.last_name}",email:"${form.email}"`;

  if (page.props.hasOwnProperty('model')) {
    query += `,id:${page.props.model.id}`
  }

  if (null !== form.password && form.password.length) {
    query += `,password:"${form.password}",confirmation:"${form.confirmation}",role_id:${page.props.role_id}`;
  }

  requestGraphQL(page.props.routes.api, `mutation {${mutationType} (${query}) {id,first_name,last_name,email}}`)
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
  password: null,
  confirmation: null
})
</script>