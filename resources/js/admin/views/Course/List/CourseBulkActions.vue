<template>
  <BulkActions
    :ref="bulkActionsRef"
    :total="list.total"
    :selected="bulkActions.selected"
    :options="bulkActions.options"
    :counter="bulkActions.checkedNum"
    @clear="bulkCheckBoxToggleAll"
  />
</template>

<script setup>
import {BulkActions} from "../../../components/DataTable/index.js";
import {reactive} from "vue";


// Show bulk actions marker
let bulkActions = reactive({
  checkedNum: 0,
  selected: 'Bulk Actions',
  options: [
    {value: null, disabled: true, text: 'Bulk Actions'},
    {value: 'delete', text: 'Delete', callback: courseRemoveHandler},
    {value: 'export', text: 'Export to CSV', callback: courseExportHandler},
    {value: 'addToCat', text: 'Add to Category', callback: courseAddToCategory},
    {value: 'renameFromCat', text: 'Remove from Category', callback: courseRemoveFromCategory},
    {value: 'activate', text: 'Set to Active'}
  ]
})
</script>