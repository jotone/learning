<template>
  <draggable item-key="field" handle="li" tag="ul" :list="list" @change="onSort">
    <template #item="{ element }">
      <li>
        <i class="icon sort hellip-double-icon"></i>
        <span class="column-reposition-field-name">{{ element.name }}</span>
        <div class="column-reposition-controls">
          <i class="icon close-icon"></i>
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
/*
 * Methods
 */
const onSort = () => {
}
</script>