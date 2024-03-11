<template>
  <draggable item-key="field" handle="li" tag="ul" :list="list">
    <template #item="{ element }">
      <li>
        <i class="icon sort hellip-double-icon"></i>
        <span class="sortable-list-name">{{ element.name }}</span>
        <div class="sortable-list-controls">
          <i class="icon hellip-icon"></i>
        </div>
      </li>
    </template>
  </draggable>
</template>

<script setup>
import draggable from 'vuedraggable';

const props = defineProps({
  list: {
    type: Array,
    required: true
  }
})
</script>