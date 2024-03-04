<template>
  <header>
    <div class="page-name-wrap content-container">
      <h1>Emails</h1>

      <button class="btn" form="templateForm" type="submit">
        <span>Save Changes</span>
      </button>
    </div>
  </header>

  <Notifications/>

  <form class="content-container" id="templateForm" @submit.prevent="submit">
    <fieldset class="card">
      <legend>{{ form.title }}</legend>

      <div class="row padding">
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
    </fieldset>

    <fieldset>
      <legend>Email Edit</legend>

      <EmailContentEditor :items="form.body" @showSidebar="toggleSidebar"/>
    </fieldset>

    <fieldset class="card">
      <legend>Variables</legend>
      <div class="simple-table-wrap">
        <div class="table-container">
          <VariablesTable :entities="entities" :variables="form.variables"/>
        </div>
      </div>
    </fieldset>
  </form>

  <Sidebar ref="sidebar" caption="Edit Email Content">
    <EmailRowEditor :item="emailRow" :key="rowEditorCounter"/>
  </Sidebar>
</template>

<script setup>
// Vue libs
import {inject, nextTick, reactive, ref} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Other Libs
import {Notification} from '../../libs/Notification';
// Components
import EmailContentEditor from './ContentEditor/EmailContentEditor.vue';
import EmailRowEditor from './ContentEditor/EmailRowEditor.vue';
import Notifications from '../../components/Default/Notifications.vue';
import Sidebar from '../../components/Default/Sidebar.vue';
import VariablesTable from './VariablesTable.vue';
// Layout
import Layout from '../../shared/Layout.vue';

// Assign the http request function
const request = inject('request');

defineOptions({layout: Layout})

// Page variables
const page = usePage()

/*
 * Methods
 */
/**
 * Force an EmailRowEditor to rerender
 */
const forceRerender = () => {
  rowEditorCounter.value += 1;
};

/**
 * Submit the email template form
 */
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

/**
 * Show the sidebar
 * @param i
 */
const toggleSidebar = i => {
  emailRow = form.body[i];
  sidebar.value.toggleShow(true);
  forceRerender();
}

/*
 * Variables
 */
// Sidebar element reference
let sidebar = ref(null);

// Editable email row
let emailRow = reactive({});

// EmailRowEditor Component re-render helper
const rowEditorCounter = ref(0);

// Page form variables
let form = reactive({
  title: page.props?.model?.title || '',
  slug: page.props?.model?.slug || '',
  subject: page.props?.model?.subject || '',
  variables: page.props?.model?.variables || {},
  body: page.props?.model?.body || []
});

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
