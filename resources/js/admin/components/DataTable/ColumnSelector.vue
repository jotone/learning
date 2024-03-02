<template>
  <div class="column-selector-wrap">
    <ul class="column-selector-section-list">
      <template v-for="section in sections">
        <ColumnSelectorSection :section="section"/>
      </template>
    </ul>
  </div>

  <div class="column-selector-reposition-wrap">
    <div class="column-selector-reposition-caption">
      Column Reposition
    </div>

    <ColumnSelectorReposition :list="columns"/>
  </div>
</template>

<script setup lang="ts">
import {ColumnSectionInterface} from '../../../contracts/ColumnSectionInterface.js';
import ColumnSelectorSection from './ColumnSelectorSection.vue';
import ColumnSelectorReposition from './ColumnSelectorReposition.vue';

const props = defineProps({
  columns: {
    type: Array,
    default: []
  },
  sections: {
    type: Array<ColumnSectionInterface>,
    required: true
  }
})

</script>