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
import {computed, inject, PropType} from 'vue';
import {usePage} from "@inertiajs/vue3";
import {RoleInterface} from "../../../../contracts/RoleInterface.js";

const convertDate = inject('convertDate');

const emit = defineEmits(['action'])

const page = usePage();

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