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
        method="POST"
        :action="$attrs.routes.coaches.form"
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
    /**
     * Reset form when entity is created
     *
     * @param response
     */
    resetForm(response) {
      201 === response.status && $('form')[0].reset()
    },
    /**
     * Show one of these message after request
     *
     * @param response
     * @returns {string}
     */
    saveMessage(response) {
      return this.__(`coach.msg.${201 === response.status ? 'created' : 'modified'}`, response.data.email)
    }
  },
  mixins: [FormMixin],
  name: "Coaches/Form"
}
</script>