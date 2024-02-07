<template>
  <span class="color-picker-wrap" v-click-outside="close" >
    <span class="color-picker-preview" :style="{'background-color': color}" @click="open"></span>

    <span class="color-picker-menu" v-if="show">
      <ColorPicker theme="light" :color="color" :sucker-hide="true" @changeColor="changeColor"/>
    </span>

    <input class="form-input" :value="color">
  </span>
</template>

<script setup>
import {ref} from "vue";
import 'vue-color-kit/dist/vue-color-kit.css';
import {ColorPicker} from 'vue-color-kit';

const emit = defineEmits(['change'])

const props = defineProps({
  value: {
    type: String,
    default: "#ffffff"
  }
})
/*
 * Methods
 */
/**
 * Change color handler
 * @param data
 */
const changeColor = data => {
  color.value = data.hex;
  emit('change', data.hex)
}
/**
 * Close color picker
 */
const close = () => {show.value = false}
/**
 * View color picker
 */
const open = () => {show.value = true}

/*
 * Variables
 */
let color = ref(props.value)
let show = ref(false)

</script>