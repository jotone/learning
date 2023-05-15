<template>
  <div class="card">
    <div class="card-title">
      {{ __('user.profile.data') }}
    </div>

    <ImageUpload :caption="__('user.profile.image')" name="img_url" :dimensions="[200, 200]" :value="model?.img_url"/>

    <InputText :caption="__('user.profile.first_name')" name="first_name" :value="model?.first_name"/>

    <InputText :caption="__('user.profile.last_name')" name="last_name" :value="model?.last_name"/>

    <InputText :caption="__('user.profile.email')" name="email" :value="model?.email"/>

    <InputText :caption="__('user.password.txt')" name="password" type="password"/>

    <InputText :caption="__('user.password.confirmation')" name="confirmation" type="password"/>

    <TextArea :caption="__('user.profile.bio')" name="about" :value="model?.about"/>
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