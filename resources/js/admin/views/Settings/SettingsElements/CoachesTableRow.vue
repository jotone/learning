<template>
  <tr>
    <td class="static">
      <div class="static-fields">
        <UserInfo :user="user"/>
      </div>
    </td>
    <td>
      <span>{{ convertDate(user.created_at) }}</span>
    </td>
    <td>
      <span>{{ convertDate(user.last_activity) }}</span>
    </td>
    <td>
      <a class="row-actions" @click.prevent="showActionsPanel">
        <i class="icon hellip-icon"></i>
      </a>
    </td>
  </tr>
</template>

<script setup lang="ts">
// Vue libs
import {inject, PropType} from 'vue';
// Interfaces
import {UserDataInterface} from '../../../../contracts/UserDataInterface.js';
// Components
import UserInfo from '../../../components/Default/UserInfo.vue';

// Implement function to convert dates into the proper view
const convertDate = inject('convertDate');

const emit = defineEmits(['action']);

const props = defineProps({user: Object as PropType<UserDataInterface>});
/**
 * Emit action click event
 * @param {event} e
 */
const showActionsPanel = e => emit('action', e, props.user)
</script>
