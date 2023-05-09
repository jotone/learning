<template>
  <Layout>
    <template v-slot:optionals>
      <SaveButton form="roleForm"/>
    </template>


    <template v-slot:content>
      <form
        class="page-content-wrap"
        id="roleForm"
        :action="$attrs.routes.roles.form"
        method="POST"
        @submit.prevent="submit"
      >
        <input name="_method" type="hidden" value="PUT" v-if="$attrs.hasOwnProperty('model')">

        <div class="card">
          <div class="card-title">
            Main Data
          </div>
          <div class="row">
            <div class="col-1-2">
              <InputText caption="Name" name="name" :value="$attrs?.model?.name"/>
            </div>
            <div class="col-1-2">
              <InputText
                caption="Level"
                name="level"
                type="number"
                :min="$attrs.auth.role.level"
                :max="255"
                :value="$attrs?.model?.level || $attrs.auth.role.level"
              />
            </div>
          </div>
        </div>


        <div class="card">
          <div class="card-title">
            List of Permissions
          </div>

          <div class="row">
            <table class="permission-list">
              <thead>
              <tr>
                <th>Controller</th>
                <th>Methods</th>
              </tr>
              </thead>
              <tbody>
              <template v-for="permission in $attrs.permissions">
                <tr v-if="$attrs.user_permissions.hasOwnProperty(permission.class)">
                  <td class="marked">
                    {{ permission.file }}
                  </td>
                  <td>
                    <div class="row fs">
                      <div class="form-group" v-for="method in permission.methods">
                        <label
                          class="inline-caption"
                          v-if="$attrs.user_permissions[permission.class].indexOf(method) >= 0"
                        >
                          <input
                            class="form-checkbox"
                            type="checkbox"
                            :name="`permissions[${permission.class}][${method}]`"
                            :checked="$attrs?.model?.permission_list.hasOwnProperty(permission.class) && $attrs?.model?.permission_list[permission.class].indexOf(method) >= 0"
                          >
                          <span>{{ method.ucfirst() }}</span>
                        </label>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
              </tbody>
            </table>
          </div>
        </div>
      </form>
    </template>
  </Layout>
</template>

<script>
import {FormMixin} from "../../Mixins/form-mixin";
import InputText from "../../Shared/Form/InputText.vue";
import method from "../../Shared/Form/Method.vue";

export default {
  computed: {
    method() {
      return method
    }
  },
  components: {InputText},
  methods: {
    saveMessage(response) {
      return 201 === response.status
        ? `Role "${response.data.name}" was successfully created.`
        : `Role "${response.data.name}" was successfully modified.`
    }
  },
  mixins: [FormMixin],
  name: "Roles/Form",
}
</script>

<style>
@import "/public/css/dashboard/permissions.css";
</style>