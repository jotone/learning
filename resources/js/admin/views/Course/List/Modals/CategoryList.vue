<template>
  <draggable item-key="field" handle="li" tag="ul" :list="list">
    <template #item="{ element, index }">
      <li>
        <i class="icon sort hellip-double-icon"></i>
        <span class="sortable-list__name">{{ element.name }}</span>
        <div class="sortable-list__controls" @click="emit('showControls', $event, index)">
          <i class="icon hellip-icon"></i>
        </div>
      </li>
    </template>
  </draggable>
</template>

<script setup>
// Drag and drop lib
import draggable from 'vuedraggable';

// Assign the function to emit
const emit = defineEmits(['showControls'])
// Get component properties
const props = defineProps({
  list: {
    type: Array,
    required: true
  }
})
</script>