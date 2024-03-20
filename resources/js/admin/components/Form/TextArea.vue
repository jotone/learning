<template>
  <textarea
    class="form-text scrollbar"
    :name="name"
    :placeholder="placeholder"
    :style="style"
    :value="modelValue"
    @input="onInput"
  ></textarea>

  <template v-if="enableProgress">
    <CircleProgress
      ref="progress"
      :showPercent="showPercent"
      :showRemnant="showRemnant"
      :current="modelValue?.length || 0"
      :max="max"
    />
  </template>
</template>

<script setup>
import {ref} from "vue";
import CircleProgress from "./CircleProgress.vue";

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