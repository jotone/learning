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
      <a class="row-actions">
        <i class="icon hellip-icon"></i>
      </a>
    </td>
  </tr>
</template>

<script setup lang="ts">
import { computed, inject, PropType } from 'vue';
import { RoleInterface } from "../../../../contracts/RoleInterface.js";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const convertDate = inject('convertDate')

const props = defineProps({role: Object as PropType<RoleInterface>});
// Link for the role edit page
const editUrl = computed(() => page.props.routes.roles.edit.replace(/:id/, props.role.id))
</script>