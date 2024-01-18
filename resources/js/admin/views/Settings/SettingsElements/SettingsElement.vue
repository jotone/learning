<template>
  <li :class="{active: show}">
    <div class="list-element-title" @click="toggleShow">
      <span>{{ name }}</span>

      <div class="list-show">
        <i class="icon chevron-icon"></i>
      </div>
    </div>

    <div class="list-element-content-wrap" :class="{active: show}">
      <slot/>
    </div>
  </li>
</template>

<script setup>
import {ref} from "vue";

const props = defineProps({
  name: {
    type: String,
    default: ''
  }
});

let show = ref(false);
const toggleShow = () => {
  show.value = !show.value
}
</script>