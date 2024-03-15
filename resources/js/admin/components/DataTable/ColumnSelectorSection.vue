<template>
  <li>
    <div class="column-selector-section-wrap" :class="{active: showFields}" @click="toggleSection">
      <div class="column-section-icon">
        <i class="icon" :class="section.icon"></i>
      </div>
      <div class="column-section-name">{{ section.name }}</div>

      <div class="column-expand-icon">
        <i class="icon arrow-icon"></i>
      </div>
    </div>

    <ul class="section-field-list" :class="{active: showFields}">
      <li v-for="column in section.columns">
        <SliderCheckbox
          :checked="column.enable"
          :name="column.field"
          :text="column.name"
          @change="toggleColumn"
        />
      </li>
    </ul>
  </li>
</template>

<script setup lang="ts">
// Vue libs
import {ref} from 'vue';
// Components
import {SliderCheckbox} from '../Form/index.js';
import {ColumnSectionInterface} from '../../../contracts/ColumnSectionInterface.js';

// Assign the function to emit
const emit = defineEmits(['changeColumnStatus'])

// Get component properties
const props = defineProps({
  section: {
    type: Object<ColumnSectionInterface>,
    required: true
  }
})

/*
 * Methods
 */
/**
 * Check the page column changed its status and pass the execution results to the parent component
 * @param value
 * @param name
 */
const toggleColumn = (value: boolean, name: string) => emit('changeColumnStatus', props.section.slug, name, value)

/**
 * Expand or collapse the section
 */
const toggleSection = () => {
  showFields.value = !showFields.value
}

/*
 * Variables
 */
const showFields = ref(false)
</script>