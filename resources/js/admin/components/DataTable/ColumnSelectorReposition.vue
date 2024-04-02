<template>
  <draggable item-key="field" handle="li" tag="ul" :list="list">
    <template #item="{ element }">
      <li>
        <i class="icon sort hellip-double-icon"></i>
        <span class="sortable-list__name">{{ element.name }}</span>
        <div class="sortable-list__controls">
          <i class="icon close-icon"></i>
        </div>
      </li>
    </template>
  </draggable>
</template>

<script setup>
// Other Libs
import draggable from 'vuedraggable';

// Get component properties
const props = defineProps({
  list: {
    type: Array,
    required: true
  }
})
</script>