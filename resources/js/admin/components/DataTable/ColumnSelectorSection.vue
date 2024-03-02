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
        <SliderCheckbox :name="column.field" :text="column.name" :checked="column.enable"/>
      </li>
    </ul>
  </li>
</template>

<script setup lang="ts">

import {SliderCheckbox} from '../Form/index.js';
import {ColumnSectionInterface} from '../../../contracts/ColumnSectionInterface.js';
import {ref} from 'vue';

const props = defineProps({
  section: {
    type: Object<ColumnSectionInterface>,
    required: true
  }
})

const toggleSection = () => {
  showFields.value = !showFields.value
}

const showFields = ref(false)
</script>