<template>
  <div
    class="row-actions-popup"
    :class="{active: active}" :style="{right: right + 'px', top: top + 'px'}"
    v-click-outside="closeRowActions"
  >
    <ul>
      <li v-for="action in actions" @click="action.callback">
        <template v-if="action.link">
          <a :href="buildUrl(action.link)">
            <i class="icon" :class="action.icon"></i>
            {{ action.name }}
          </a>
        </template>

        <template v-else>
          <i class="icon" :class="action.icon"></i>
          <span>{{ action.name }}</span>
        </template>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import {nextTick, ref, watch} from "vue";

const props = defineProps({
  actions: {
    type: Array,
    default: []
  },
  model: {
    type: Object,
    default: {}
  },
  right: {
    type: Number,
    default: 0
  },
  top: {
    type: Number,
    default: 0
  },
  show: {
    type: Boolean,
    default: false
  }
})

/*
 * Methods
 */
/**
 * Build an action link
 * @param {string} link
 */
const buildUrl = (link: string) => link.replace(/:id/, props.model.id);

const closeRowActions = async (e) => {
  await nextTick()
  if (active.value && null === e.target.closest('.row-actions')) {
    active.value = false
  }
}

/*
 * Watchers
 */
watch(props, val => {active.value = val.show});

/*
 * Variables
 */
let active = ref(props.show)
</script>