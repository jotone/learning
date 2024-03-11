<template>
  <div class="col-name">
    <span>{{ name }}</span>

    <div v-if="showPlusIcon" class="plus-icon-wrap" @click="plusClick">
      <i class="icon plus-icon"></i>
    </div>

    <div v-if="showInfoIcon" class="info-icon-wrap" @mouseover="showTooltip">
      <i class="icon info-icon"></i>
    </div>

    <div
      v-if="null !== field"
      class="order"
      :class="{active: colIsActive(field), desc: colDirectDesc(field)}"
      @click="changeOrder"
    ></div>
  </div>
</template>

<script setup lang="ts">
// Vue libs
import {PropType} from "vue";
// Interfaces
import {FiltersInterface} from "../../../contracts/FiltersInterface";

const emit = defineEmits(['changeDirection', 'hover', 'plusClick'])

const props = defineProps({
  field: {
    type: String,
    default: null
  },
  filters: {
    type: Object as PropType<FiltersInterface>,
    default: {}
  },
  name: {
    type: String,
    required: true
  },
  showInfoIcon: {
    type: Boolean,
    default: false
  },
  showPlusIcon: {
    type: Boolean,
    default: false
  }
});

/*
 * Methods
 */
/**
 * Check the order field is equal to the column name
 * @param {string} col
 * @return {boolean}
 */
const colIsActive = (col: string): boolean => props.filters.order.by === col

/**
 * Check the field direction is DESC
 * @param {string} col
 * @return {boolean}
 */
const colDirectDesc = (col: string): boolean => colIsActive(col) && props.filters.order.dir === 'desc';

/**
 * Order element click handler
 */
const changeOrder = () => {
  if (null !== props.field) {
    let order = Object.assign({}, props.filters.order);
    if (props.filters.order.by === props.field) {
      order.dir = order.dir === 'desc' ? 'asc' : 'desc';
    } else {
      order.by = props.field
    }
    emit('changeDirection', order)
  }
}

/**
 * Click the plus button
 * @param e
 */
const plusClick = e => emit('plusClick', e)

/**
 * Hover status icon
 * @param e
 */
const showTooltip = e => emit('hover', e, true)
</script>