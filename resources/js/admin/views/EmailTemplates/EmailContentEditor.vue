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
          <button class="add-row-btn" @click="addRow">
            <i class="icon plus-icon"></i>
          </button>
        </td>
      </tr>
      </tbody>
    </table>
  </div>

  <Teleport to="body">
    <EditRowPopup ref="editRowModal"/>
  </Teleport>
</template>

<script setup>
import EditRowPopup from "./Modals/EditRowPopup.vue";
import {ref} from "vue";

const props = defineProps({
  items: {
    type: Array,
    required: true
  }
})
/*
 * Methods
 */
const addRow = () => props.items.push({style: {}, text: ""})
const editRow = (i) => {
  editRowModal.value.open(props.items[i]).then(res => {
    console.log(res)
  })
}
const removeRow = (i) => {
  let res = props.items[i].text.length ? confirm('Do you really want to remove this row?') : true;
  res && props.items.splice(i, 1);
}

const editRowModal = ref(null)
</script>