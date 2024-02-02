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
        <span>%{{ name }}%</span>
      </td>
      <td>
        <span>
          <template v-if="data.type === 'model'">
            {{ data.model }}
          </template>
          <template v-if="data.type === 'route'">
            {{ data.model }} {{ entities.list[data.model].fields[data.field] }}
          </template>
        </span>
      </td>
      <td>
        <span>
          <template v-if="data.type === 'model'">
            {{ entities.list[data.model].fields[data.field] }}
          </template>

          <template v-if="data.type === 'route'">
            {{ data.route }}
          </template>
        </span>
      </td>
      <td>
        <a href="#" class="row-actions" @click.prevent="variableRemove(name)">
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
/*
 * Methods
 */
const variablePopupAdd = () => variableModal.value.open().then(res => {
  if (res) {
    if ('Route' === res.type) {
      const model = res.encrypt.split(':')
      props.variables[res.name] = {
        type: "route",
        route: res.field,
        model: model[0],
        field: model[1]
      }
    } else {
      props.variables[res.name] = {
        type: "model",
        model: res.type,
        field: res.field
      }
    }
  }
})

const variableRemove = (name) => {
  const res = confirm('Do you really want to remove this variable?')
  if (res) {
    delete props.variables[name];
  }
}

const variableModal = ref(null)
</script>