<template>
  <div class="form-group">
    <label class="caption">
      <span>{{ getCaption }}</span>
      <input
        autocomplete="off"
        class="form-input"
        :disabled="isDisabled"
        :name="name"
        :placeholder="getCaption.length ? getCaption + '...' : getCaption"
        :value="getValue">
    </label>
  </div>
</template>

<script>
export default {
  name: "InputText",
  props: ["caption", "disabled", "name", "value"],
  computed: {
    isDisabled() {
      return this.disabled ?? false;
    },
    getCaption() {
      return this.caption ?? ''
    },
    getValue() {
      return typeof this.value === 'string' ? this.value : ''
    }
  }
}
</script>