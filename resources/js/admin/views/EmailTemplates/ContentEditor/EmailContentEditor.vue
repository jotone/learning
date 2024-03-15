<template>
  <div class="email-editor-wrap">
    <table ref="table">
      <tbody>
      <tr v-for="(item, i) in items">
        <td>
          <div class="table-element-wrap" :style="item.style">
            <div class="table-element-content" v-html="item.text"></div>
            <div class="table-element-controls">
              <div class="table-element-controls-edit" @click="editRow(i)">
                <i class="icon edit-icon"></i>
              </div>
              <div class="table-element-controls-remove" @click="removeRow(i)">
                <i class="icon close-icon"></i>
              </div>
            </div>
          </div>
        </td>
      </tr>
      <tr class="add-row-wrap">
        <td title="Add a Row">
          <button class="add-row-btn" type="button" @click="addRow">
            <i class="icon plus-icon"></i>
          </button>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
// Assign the function to emit
const emit = defineEmits(['showSidebar'])
// Get component properties
const props = defineProps({
  items: {
    type: Array,
    required: true
  }
})
/*
 * Methods
 */
/**
 * Add a row to the email content list
 * @return {number}
 */
const addRow = () => {
  props.items.push({style: {'text-align': 'center'}, text: "<br>"})
  emit('showSidebar', props.items.length - 1)
}
/**
 * Edit a row of the email content list
 * @param {number} i
 */
const editRow = i => emit('showSidebar', i)
/**
 * Remove a row from the email content list
 * @param {number} i
 */
const removeRow = i => {
  let res = props.items[i].text.length ? confirm('Do you really want to remove this row?') : true;
  res && props.items.splice(i, 1);
}
</script>