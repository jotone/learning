<template>
  <div class="form-group">
    <select :name="name" class="form-select">
      <template v-for="(timezone, key) in timezones">
        <optgroup :label="key">
          <option
            :selected="value === key"
            :value="key"
            v-html="timezone"
          >
          </option>
        </optgroup>
      </template>
    </select>
  </div>
</template>
<script>

import { Timezone } from "../../../libs/timezone";

export default {
  data() {
    return {
      timezones: Timezone
    }
  },
  mounted() {
    $('select[name="site_timezone"]').select2({
      allowHtml: true,
      minimumResultsForSearch: -1
    })
  },
  name: "TimezoneSelector",
  props: {
    name: {
      type: String,
      default: ''
    },
    value: {
      type: [Number, String],
      default: null
    }
  }
}
</script>