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
            <SimpleUserForm :model="$attrs?.model"/>
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
import SimpleUserForm from "./Partials/SimpleUserForm.vue";

export default {
  components: {SimpleUserForm},
  mixins: [FormMixin],
  name: "Users/AdminForm"
}
</script>