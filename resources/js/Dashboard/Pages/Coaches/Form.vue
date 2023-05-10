<template>
  <Layout>
    <template v-slot:optionals>
      <SaveButton form="coachForm"/>
    </template>

    <template v-slot:content>
      <form
        class="page-content-wrap"
        data-success-callback="resetForm"
        id="coachForm"
        :action="$attrs.routes.coaches.form"
        method="POST"
        @submit.prevent="submit"
      >
        <input name="_method" type="hidden" value="PUT" v-if="$attrs.hasOwnProperty('model')">
        <input name="role_id" type="hidden" :value="$attrs.role">
        <div class="row">
          <div class="col-1-2">
            <SimpleUserForm :model="$attrs?.model"/>
          </div>
        </div>
      </form>
    </template>
  </Layout>
</template>

<script>
import {FormMixin} from "../../Mixins/form-mixin";
import SimpleUserForm from "../Users/Partials/SimpleUserForm.vue";

export default {
  components: {SimpleUserForm},
  methods: {
    saveMessage(response) {
      return 201 === response.status
        ? `Coach "${response.data.email}" was successfully created.`
        : `Coach "${response.data.email}" was successfully modified.`
    }
  },
  mixins: [FormMixin],
  name: "Coaches/Form"
}
</script>