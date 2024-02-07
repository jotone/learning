<template>
  <header>
    <div class="page-name-wrap">
      <h1>Emails</h1>

      <button class="btn" form="templateForm" type="submit">
        <span>Save Changes</span>
      </button>
    </div>
  </header>

  <Notifications/>

  <form id="templateForm" @submit.prevent="submit" class="f-st-fs">
    <div class="col-4-5">
      <section>
        <h3 class="section-name">{{ form.title }}</h3>
        <div class="card">
          <div class="row">
            <label class="caption col-1-2">
              <span>Email Title</span>
              <input
                autocomplete="off"
                class="form-input"
                name="title"
                placeholder="Email Title..."
                required
                v-model="form.title"
              >
            </label>

            <label class="caption col-1-2" v-if="$attrs.auth.role.level === 0">
              <span>Email Slug</span>
              <input
                autocomplete="off"
                class="form-input"
                name="slug"
                placeholder="Email Slug..."
                required
                v-model="form.slug"
              >
            </label>
          </div>
        </div>
      </section>

      <section>
        <h3 class="section-name">Variables</h3>
        <div class="card">
          <div class="simple-table-wrap">
            <div class="table-container">
              <VariablesTable :entities="entities" :variables="form.variables"/>
            </div>
          </div>
        </div>
      </section>

      <section>
        <h3 class="section-name">Email Edit</h3>

        <EmailContentEditor :items="form.body"/>
      </section>
    </div>
  </form>
</template>

<script setup>
// Vue libs
import {inject, reactive} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Other Libs
import {Notification} from '../../libs/Notification';
// Components
import EmailContentEditor from './EmailContentEditor.vue';
import Notifications from '../../components/Default/Notifications.vue';
import VariablesTable from './VariablesTable.vue';
// Layout
import Layout from '../../shared/Layout.vue';

// Assign the http request function
const request = inject('request');

defineOptions({layout: Layout})

// Page variables
const page = usePage()

const submit = () => {
  request({
    url: page.props.routes.save,
    method: page.props.hasOwnProperty('model') ? 'PUT' : 'POST',
    data: form,
    onSuccess: (response) => {
      if (200 === response.status) {
        Notification.success(`Email template "${response.data.title}" was successfully modified.`)
      }
      if (201 === response.status) {
        Notification.success(`Email template  "${response.data.title}" was successfully created.`);
        form.title = '';
        form.slug = '';
        form.subject = '';
        form.variables = {};
        form.body = [];
      }
    }
  })
}

/*
 * Variables
 */
// Page form variables
let form = reactive({
  title: page.props?.model?.title || '',
  slug: page.props?.model?.slug || '',
  subject: page.props?.model?.subject || '',
  variables: page.props?.model?.variables || {},
  body: page.props?.model?.body || []
})

const entities = {
  encryption: {
    User: {
      id: 'ID',
      email: 'email'
    }
  },
  list: {
    User: {
      type: 'model',
      fields: {
        first_name: 'First Name',
        last_name: 'Last Name',
        full_name: 'Full Name',
        email: 'Email',
        country: 'Country',
        region: 'Region',
        city: 'City',
        address: 'Address',
        ext_addr: 'Extended Address',
        zip: 'Zip-code',
        phone: 'Phone number',
        activated_at: 'Activation Date',
        created_at: 'Account Creation Date'
      }
    },
    Course: {
      type: 'model',
      fields: {
        name: 'Name',
        url: 'Course URL',
        certificated_at: 'Course Finish Date',
        enrolled_at: 'Course Enroll Date'
      }
    },
    Route: {
      type: 'route',
      fields: {
        'sign-up.show': 'Activation URL',
        'reset.index': 'Reset Password',
        'home.index': 'Site URL'
      }
    }
  }
}
</script>
