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
            <div class="card">
              <div class="card-title">
                Profile Data
              </div>
              <InputText caption="First Name" name="first_name" :value="$attrs?.model?.first_name"/>

              <InputText caption="Last Name" name="last_name" :value="$attrs?.model?.last_name"/>

              <InputText caption="E-mail" name="email" :value="$attrs?.model?.email"/>

              <InputText caption="Password" name="password" type="password"/>

              <InputText caption="Confirm Password" name="confirmation" type="password"/>

              <ImageUpload caption="Profile Image" name="img_url" :dimensions="[200, 200]" :value="$attrs?.model?.img_url"/>

              <TextArea caption="Short bio" name="about" :value="$attrs?.model?.about"/>
            </div>
          </div>
        </div>
      </form>
    </template>
  </Layout>
</template>

<script>
import {FormMixin} from "../../Mixins/form-mixin";
import ImageUpload from "../../Shared/Form/ImageUpload.vue";
import InputText from "../../Shared/Form/InputText.vue";
import TextArea from "../../Shared/Form/TextArea.vue";

export default {
  components: {TextArea, ImageUpload, InputText},
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