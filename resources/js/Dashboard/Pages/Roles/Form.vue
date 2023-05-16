<template>
  <Layout>
    <template v-slot:optionals>
      <SaveButton form="roleForm"/>
    </template>

    <template v-slot:content>
      <form
        class="page-content-wrap"
        id="roleForm"
        :action="$attrs.routes.form"
        method="POST"
        @submit.prevent="submit"
      >
        <input name="_method" type="hidden" value="PUT" v-if="$attrs.hasOwnProperty('model')">

        <div class="card">
          <div class="card-title">
            {{ __('common.main_data') }}
          </div>
          <div class="row">
            <div class="col-1-2">
              <InputText :caption="__('common.name')" name="name" :value="$attrs?.model?.name"/>
            </div>
            <div class="col-1-2">
              <InputText
                :caption="__('role.fields.level')"
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
            {{ __('role.fields.permissions') }}
          </div>

          <div class="row">
            <table class="permission-list">
              <thead>
              <tr>
                <th>{{ __('role.fields.controller') }}</th>
                <th>{{ __('role.fields.methods') }}</th>
              </tr>
              </thead>
              <tbody>
              <template v-for="permission in $attrs.permissions">
                <tr v-if="$attrs.userPermissions.hasOwnProperty(permission.class)">
                  <td class="marked">
                    {{ permission.file }}
                  </td>
                  <td>
                    <div class="row fs">
                      <div class="form-group" v-for="method in permission.methods">
                        <label
                          class="inline-caption"
                          v-if="$attrs.userPermissions[permission.class].indexOf(method) >= 0"
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

export default {
  components: {InputText},
  methods: {
    saveMessage(response) {
      return this.__(`role.msg${201 === response.status ? 'created' : 'modified'}`, response.data.name)
    }
  },
  mixins: [FormMixin],
  name: "Roles/Form",
}
</script>