<template>
  <div class="card">
    <div class="card-title">
      Profile Data
    </div>

    <ImageUpload caption="Profile Image" name="img_url" :dimensions="[200, 200]" :value="model?.img_url"/>

    <InputText caption="First Name" name="first_name" :value="model?.first_name"/>

    <InputText caption="Last Name" name="last_name" :value="model?.last_name"/>

    <InputText caption="E-mail" name="email" :value="model?.email"/>

    <InputText caption="Password" name="password" type="password"/>

    <InputText caption="Confirm Password" name="confirmation" type="password"/>

    <TextArea caption="Short bio" name="about" :value="model?.about"/>
  </div>
</template>

<script>

import InputText from "../../../Shared/Form/InputText.vue";
import TextArea from "../../../Shared/Form/TextArea.vue";
import ImageUpload from "../../../Shared/Form/ImageUpload.vue";

export default {
  components: {ImageUpload, TextArea, InputText},
  props: ["model"]
}
</script>