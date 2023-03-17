<template>
  <div class="form-group">
    <label class="caption">
      <span>{{ caption || '' }}</span>
      <textarea
        class="form-textarea"
        :name="name"
        :placeholder="placeholder || caption + '...'"
      >{{ value || '' }}</textarea>
    </label>
  </div>
</template>

<script>
export default {
  name: "TextArea",
  props: ["caption", "name", "placeholder", "value"]
}
</script>