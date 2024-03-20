<template>
  <input
    autocomplete="off"
    class="form-input"
    :disabled="disabled"
    :readonly="readonly"
    :required="required"
    :name="name"
    :placeholder="placeholder"
    :style="style"
    :type="type"
    :value="modelValue"
    @input="onInput"
  >

  <template v-if="enableProgress">
    <CircleProgress
      class="input-shift"
      ref="progress"
      :showPercent="showPercent"
      :showRemnant="showRemnant"
      :current="modelValue?.length || 0"
      :max="max"
    />
  </template>
</template>

<script setup>
import CircleProgress from "./CircleProgress.vue";
import {ref} from "vue";

const emit = defineEmits(['onInput', 'update:modelValue']);
const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  name: {
    type: String,
    default: null
  },
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: null
  },
  style: {
    type: Object,
    default: null
  },
  enableProgress: {
    type: Boolean,
    default: false
  },
  max: {
    type: [Number, String],
    default: 65535
  },
  showPercent: {
    type: Boolean,
    default: false
  },
  showRemnant: {
    type: Boolean,
    default: false
  }
})

const onInput = e => {
  emit('update:modelValue', e.target.value.trim())
  emit('onInput', e)
};

const getStatus = () => progress.value.getStatus()

const progress = ref(null);

defineExpose({getStatus})
</script>