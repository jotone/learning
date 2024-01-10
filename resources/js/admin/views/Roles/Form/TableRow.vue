<template>
  <tr>
    <td class="solid-col">
      {{ namespace }}\{{ controller }}
    </td>
    <td class="list-col">
      <label class="caption-inline" v-for="method in methods">
        <input
          :name="`${namespace}[${controller}][${method}]`"
          type="checkbox"
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
