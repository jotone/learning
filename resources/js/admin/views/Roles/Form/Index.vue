<template>
  <header>
    <div class="page-name-wrap">
      <h1 v-html="$attrs.hasOwnProperty('model') ? 'Edit Role' : 'Create Role'"></h1>

      <button class="btn" form="roleForm" type="submit">
        <span>Save Changes</span>
      </button>
    </div>
  </header>

  <Notifications/>

  <form id="roleForm" @submit.prevent="submit" class="f-st-fs">
    <div class="col-4-5">
      <fieldset class="card">
        <legend>Role data</legend>

        <div class="row">
          <div class="col-1-2">
            <label class="caption">
              <span>Name</span>
              <input
                autocomplete="off"
                class="form-input"
                name="name"
                placeholder="Role name..."
                required
                v-model="form.name">
            </label>

            <label class="caption" :style="{display: $attrs.auth.role.level === 0 ? 'block': 'none'}">
              <span>Slug</span>
              <input
                autocomplete="off"
                class="form-input"
                name="slug"
                placeholder="Role slug..."
                v-model="form.slug">
            </label>
          </div>
          <div class="col-1-2">
            <label class="caption">
              <span>Level</span>
              <input
                autocomplete="off"
                class="form-input"
                name="level"
                placeholder="Role level..."
                type="number"
                max="255"
                required
                :min="$attrs.auth.role.level"
                v-model="form.level"
              >
            </label>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>List of permissions</legend>

        <table class="characteristics-table">
          <thead>
          <tr>
            <th>Controller</th>
            <th>Methods</th>
          </tr>
          </thead>
          <tbody>
          <template v-for="(methods, controller) in $attrs.permissions.graphql">
            <TableRow
              :controller="controller"
              :methods="methods"
              :list="form.graphql"
              namespace="GraphQL"
              @updateForm="updateForm"
            />
          </template>
          <template v-for="(methods, controller) in $attrs.permissions.dashboard">
            <TableRow
              :controller="controller"
              :methods="methods"
              :list="form.dashboard"
              namespace="Dashboard"
              @updateForm="updateForm"
            />
          </template>
          </tbody>
        </table>
      </fieldset>
    </div>
  </form>
</template>

<script setup lang="ts">
// Vue libs
import {inject, reactive} from "vue";
import {usePage} from "@inertiajs/vue3";
// Other Libs
import {Notification} from "../../../libs/Notification";
// Components
import Layout from "../../../shared/Layout.vue";
import TableRow from "./TableRow.vue";
import Notifications from "../../../components/Default/Notifications.vue";

defineOptions({layout: Layout})

// Assign the GraphQL request function
const requestGraphQL = inject('requestGraphQL')
// Page variables
const page = usePage();

/*
 * Methods
 */
/**
 * Build the already set role permissions
 */
const getRolePermissions = () => {
  let rolePermissions = {};
  if ('model' in page.props) {
    for (let i = 0, n = page.props.model.permissions.length; i < n; i++) {
      const permission = page.props.model.permissions[i]

      const controller = permission.controller.split('\\').pop();
      rolePermissions[controller] = permission.allowed_methods
    }
  }
  return rolePermissions
}

/**
 * Build the role permissions list
 * @param form
 * @param rolePermissions
 */
const buildPermissions = (form: any, rolePermissions: object) => {
  for (let type in page.props.permissions) {
    // Set form type body if it does not exist
    !form.hasOwnProperty(type) && (form[type] = {})
    for (let controller in page.props.permissions[type]) {
      // Set form controller type if it does not exist
      !form[type].hasOwnProperty(controller) && (form[type][controller] = {})
      for (let i = 0, n = page.props.permissions[type][controller].length; i < n; i++) {
        const action = page.props.permissions[type][controller][i]
        if (!form[type][controller].hasOwnProperty(action)) {
          form[type][controller][action] = +(rolePermissions.hasOwnProperty(controller) && rolePermissions[controller].indexOf(action) >= 0);
        }
      }
    }
  }

  return form;
}

/**
 * Update the list of permissions
 * @param {string} type
 * @param {string} controller
 * @param {string} action
 * @param {int} value
 */
const updateForm = (type: string, controller: string, action: string, value: number) => {
  form[type.toLowerCase()][controller][action] = value;
}

/**
 * Send form request
 * @param e
 */
const submit = (e: SubmitEvent) => {
  // The mutation type depends on if a model exists or not
  const mutationType = page.props.hasOwnProperty('model') ? 'update' : 'create';

  let query = `name:"${form.name}",level:${form.level}`
  // If it is an update mutation
  if (page.props.hasOwnProperty('model')) {
    query = `id:${page.props.model.id},${query}`;
  }

  let data = {
    permissions: {}
  }
  for (let field in form) {
    if (field === 'slug' && form.slug.length) {
      query = `slug:"${form.slug}",${query}`
    } else if (field === 'dashboard' || field === 'graphql') {
      for (let controller in form[field]) {
        data.permissions[field === 'dashboard'
          ? 'App\\Http\\Controllers\\Dashboard\\'
          : 'App\\GraphQL\\Schemas\\' + controller] = form[field][controller];
      }
    }
  }
  // Set permissions to the query
  query += `,permissions:"${btoa(JSON.stringify(data.permissions))}"`

  // Send request
  requestGraphQL(page.props.routes.roles.api, `mutation {${mutationType} (${query}) {id, name, slug, level}}`)
    .then(response => {
      if (response.data.hasOwnProperty('data')) {
        // Show notification
        Notification.success(
          page.props.hasOwnProperty('model')
            ? `Role "${response.data.data.update.name}" was successfully modified.`
            : `Role "${response.data.data.create.name}" was successfully created.`
        )

        // Reset form if created
        if (!page.props.hasOwnProperty('model')) {
          form.name = '';
          form.slug = '';
          form.level = '';
          e.target.reset();
          form = buildPermissions(form, getRolePermissions())
        }
      }
    })
}
/*
 * Variables
 */
// Page form variables
let form = reactive(
  buildPermissions(
    {
      name: page.props?.model?.name || '',
      slug: page.props?.model?.slug || '',
      level: page.props?.model?.level || page.props.auth.role.level
    },
    getRolePermissions()
  )
)
</script>