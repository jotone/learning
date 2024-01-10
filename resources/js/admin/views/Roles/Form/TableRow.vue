<template>
  <tr>
    <td class="solid-col">
      {{ namespace }}\{{ controller }}
    </td>
    <td class="list-col">
      <label class="caption-inline" v-for="method in methods">
        <input
          type="checkbox"
          :name="`${namespace}[${controller}][${method}]`"
          :checked="list.hasOwnProperty(controller) && list[controller][method]"
          @change="emit('updateForm', namespace, controller, method, $event.target.value === 'on' ? 1 : 0)"
        >
        <span>{{ method }}</span>
      </label>
    </td>
  </tr>
</template>

<script setup>
const emit = defineEmits(['updateForm'])
const props = defineProps({
  controller: {
    type: String,
    required: true
  },
  list: {
    type: Object,
    default: {}
  },
  methods: {
    type: Array,
    default: []
  },
  namespace: {
    type: String,
    required: true
  }
})
</script>
