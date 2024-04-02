<template>
  <component :is="props.tag" class="user-info">
    <Avatar :user="user"/>

    <div class="user-info__credentials">
      <div class="user-info__credentials__name">{{ user.first_name }} {{ user.last_name }}</div>
      <div class="user-info__credentials__email">{{ user.email }}</div>
    </div>
  </component>
</template>

<script setup lang="ts">
// Vue libs
import {PropType} from 'vue';
// Interfaces
import {UserDataInterface} from '../../../contracts/UserDataInterface';
// Components
import Avatar from './Avatar.vue';

// Get component properties
const props = defineProps({
  user: Object as PropType<UserDataInterface>,
  tag: {
    type: String,
    default: 'div'
  }
});
</script>