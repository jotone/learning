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
        <legend title="Role data">Role data</legend>

        <div class="row padding">
          <div class="col-1-2">
            <Label caption="Name">
              <InputText placeholder="Role name..." :required="true" v-model="form.name"/>
            </Label>

            <Label caption="Slug" v-if="$attrs.auth.role.level === 0">
              <InputText placeholder="Role slug..." :required="true" v-model="form.slug"/>
            </Label>
          </div>
          <div class="col-1-2">
            <Label caption="Level">
              <input
                autocomplete="off"
                class="form-input"
                name="level"
                placeholder="Role level..."
                type="number"
                required
                :min="$attrs.auth.role.level"
                max="255"
                v-model="form.level"
              >
            </Label>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend title="List of permissions">List of permissions</legend>

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
          <template v-for="(methods, controller) in $attrs.permissions.api">
            <TableRow
              :controller="controller"
              :methods="methods"
              :list="form.api"
              namespace="Api"
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
import {inject, reactive} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Other Libs
import {Notification} from '../../../libs/Notification';
// Components
import Layout from '../../../shared/Layout.vue';
import TableRow from './TableRow.vue';
import Notifications from '../../../components/Default/Notifications.vue';
import {InputText, Label} from "../../../components/Form";

defineOptions({layout: Layout})

// Assign the GraphQL request function
const requestGraphQL = inject('requestGraphQL')
// Page variables
const page = usePage();

/*
 * --------------- Permissions builder ---------------
 */
/**
 * Build the role permissions list
 * @param form
 * @param rolePermissions
 */
const buildPermissions = (rolePermissions: object, form: any) => {
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

// Page form variables
let form = reactive(buildPermissions(getRolePermissions(), {
  name: page.props?.model?.name || '',
  slug: page.props?.model?.slug || '',
  level: page.props?.model?.level || page.props.auth.role.level
}))


/*
 * --------------- Form Handlers ---------------
 */

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

  let permissions = {}
  for (let field in form) {
    if (field === 'slug' && form.slug.length) {
      query = `slug:"${form.slug}",${query}`
    } else if (['api', 'dashboard', 'graphql'].indexOf(field) >= 0) {
      for (let controller in form[field]) {
        for (let method in form[field][controller]) {
          if (form[field][controller][method]) {
            let path = (
              field === 'graphql'
                ? 'App\\GraphQL\\Schemas\\'
                : 'App\\Http\\Controllers\\' + (field === 'api' ? 'Api\\' : 'Dashboard\\')
            ) + controller;

            if (!permissions.hasOwnProperty(path)) {
              permissions[path] = []
            }

            permissions[path].push(method)
          }
        }
      }
    }
  }

  // Set permissions to the query
  query += `,permissions:"${btoa(JSON.stringify(permissions))}"`

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
          form = buildPermissions(getRolePermissions(), form)
        }
      }
    })
}
</script>