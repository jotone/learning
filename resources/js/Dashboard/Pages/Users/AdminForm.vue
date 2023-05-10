<template>
  <Layout>
    <template v-slot:optionals>
      <SaveButton form="form"/>
    </template>

    <template v-slot:content>
      <form
        class="page-content-wrap"
        data-save-message="Your profile was successfully updated."
        id="form"
        :action="$attrs.routes.update"
        method="POST"
        @submit.prevent="submit"
      >
        <input name="_method" type="hidden" value="PUT" v-if="$attrs.hasOwnProperty('model')">
        <input name="role_id" type="hidden" :value="$attrs.model.role_id">
        <div class="row">
          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                Profile Data
              </div>

              <ImageUpload caption="Profile Image" name="img_url" :dimensions="[200, 200]" :value="$attrs?.model?.img_url"/>

              <InputText caption="First Name" name="first_name" :value="$attrs?.model?.first_name"/>

              <InputText caption="Last Name" name="last_name" :value="$attrs?.model?.last_name"/>

              <InputText caption="E-mail" name="email" :value="$attrs?.model?.email"/>

              <InputText caption="Password" name="password" type="password"/>

              <InputText caption="Confirm Password" name="confirmation" type="password"/>

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
  mixins: [FormMixin],
  name: "Users/AdminForm"
}
</script>