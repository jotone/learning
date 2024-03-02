<template>
  <tr>
    <td class="static">
      <a :href="editUrl" class="static-fields">
        <UserInfo :user="user"/>
      </a>
    </td>
    <td>
      <a :href="editUrl">{{ convertDate(user.created_at) }}</a>
    </td>
    <td>
      <a :href="editUrl">{{ convertDate(user.last_activity) }}</a>
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
import {computed, inject, PropType} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Interfaces
import {UserDataInterface} from '../../../../contracts/UserDataInterface.js';
// Components
import UserInfo from '../../../components/Default/UserInfo.vue';

// Implement function to convert dates into the proper view
const convertDate = inject('convertDate');

const emit = defineEmits(['action']);

const props = defineProps({user: Object as PropType<UserDataInterface>});

const page = usePage();

/**
 * Generates an url to coach edit page
 */
const editUrl = computed(() => page.props.routes.user.edit.replace(/:id/, props.user.id))
/**
 * Emit action click event
 * @param {event} e
 */
const showActionsPanel = e => emit('action', e, props.user)
</script>
