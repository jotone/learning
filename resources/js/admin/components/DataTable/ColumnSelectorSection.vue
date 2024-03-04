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
import {ref} from 'vue';
import {SliderCheckbox} from '../Form/index.js';
import {ColumnSectionInterface} from '../../../contracts/ColumnSectionInterface.js';

const emit = defineEmits(['changeColumnStatus'])

const props = defineProps({
  section: {
    type: Object<ColumnSectionInterface>,
    required: true
  }
})

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

const showFields = ref(false)
</script>