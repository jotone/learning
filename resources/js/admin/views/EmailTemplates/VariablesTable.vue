<template>
  <table>
    <thead>
    <tr>
      <th>
        <TableHeadCol name="Name"/>
      </th>
      <th>
        <TableHeadCol name="Entity"/>
      </th>
      <th>
        <TableHeadCol name="Entity Field"/>
      </th>
      <th>
        <TableHeadCol name="Actions"/>
      </th>
    </tr>
    </thead>
    <tbody>
    <tr v-for="(data, name) in variables">
      <td>
        <a href="#" @click.prevent="variablePopupEdit">%{{ name }}%</a>
      </td>
      <td>
        <a href="#" @click.prevent="variablePopupEdit">
          <template v-if="data.type === 'model'">
            {{ data.model }}
          </template>
          <template v-if="data.type === 'route'">
            {{ data.model }} {{ entities.list[data.model].fields[data.field] }}
          </template>
        </a>
      </td>
      <td>
        <a href="#" @click.prevent="variablePopupEdit">
          <template v-if="data.type === 'model'">
            {{ entities.list[data.model].fields[data.field] }}
          </template>

          <template v-if="data.type === 'route'">
            {{ data.route }}
          </template>
        </a>
      </td>
      <td>
        <a class="row-actions" href="#" @click.prevent="variableRemove(name)">
          <i class="icon trash-icon"></i>
        </a>
      </td>
    </tr>
    <tr>
      <td colspan="3">
        <button type="button" class="btn blue" @click="variablePopupAdd">
          Add Variable
        </button>
      </td>
    </tr>
    </tbody>
  </table>

  <Teleport to="body">
    <VariablePopup ref="variableModal" :entities="entities"/>
  </Teleport>
</template>

<script setup>
import {ref} from "vue";
import {TableHeadCol} from "../../components/DataTable/index.js";
import VariablePopup from "./Modals/VariablePopup.vue";

const props = defineProps({
  entities: {
    type: Object,
    required: true
  },
  variables: {
    type: Object,
    required: true
  }
})
const variablePopupAdd = () => {
  variableModal.value.open().then(res => {
    console.log(res)
  })
}

const variablePopupEdit = () => {

}

const variableRemove = () => {

}

const variableModal = ref(null)
</script>