<template>
  <tr>
    <td class="static">
      <div class="static-fields">
        <a :href="editUrl">{{ role.name }}</a>
      </div>
    </td>
    <td>
      <a :href="editUrl">{{ role.level }}</a>
    </td>
    <td>
      <a :href="editUrl">{{ convertDate(role.created_at) }}</a>
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
import {usePage} from "@inertiajs/vue3";
// Interfaces
import {RoleInterface} from "../../../../contracts/RoleInterface.js";

// Implement function to convert dates into the proper view
const convertDate = inject('convertDate');

// Use data passed to the page
const page = usePage();

const emit = defineEmits(['action']);

const props = defineProps({role: Object as PropType<RoleInterface>});

/**
 * Link for the role edit page
 * @return {string}
 */
const editUrl = computed((): string => page.props.routes.edit.replace(/:id/, props.role.id))

/**
 * Emit action click event
 * @param {event} e
 */
const showActionsPanel = e => emit('action', e, props.role)
</script>