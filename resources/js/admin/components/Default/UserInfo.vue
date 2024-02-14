<template>
  <component :is="props.tag" class="user-info-wrap">
    <Avatar :user="user"/>

    <div class="user-credentials">
      <div class="user-name">{{ user.first_name }} {{ user.last_name }}</div>
      <div class="user-email">{{ user.email }}</div>
    </div>
  </component>
</template>

<script setup lang="ts">
// Vue libs
import {PropType, h} from 'vue';
// Interfaces
import {UserDataInterface} from '../../../contracts/UserDataInterface';
// Components
import Avatar from '../User/Avatar.vue';

// Get component properties
const props = defineProps({
  user: Object as PropType<UserDataInterface>,
  tag: {
    type: String,
    default: 'div'
  }
});
</script>