<template>
  <div class="course-card" :data-id="model.id">
    <div class="course-status">
      <i :class="`icon ${model.status ? 'clock-icon' : 'ok-icon active'}`"></i>
      <span>{{ model.status ? 'Coming soon' : 'Active'}}</span>
    </div>
    <div class="course-card-drag">
      <i class="icon v-hellip-icon"></i>
    </div>
    <div class="course-card-image">
      <img :src="model.img_url" alt="" v-if="null !== model.img_url">
      <svg v-else xmlns="http://www.w3.org/2000/svg" width="604" height="445"><path d="M0 0h604v445H0z"/></svg>
    </div>

    <div class="course-card-text">
      <div class="course-card-name">{{ model.name }}</div>

      <div v-html="model.description"></div>
    </div>

    <div class="course-card-controls">
      <a class="btn regular" href="javascript:void(0)">Settings</a>
      <a class="btn" href="javascript:void(0)">Preview</a>
    </div>
  </div>
</template>

<script>

export default {
  name: "CourseTableRow",
  props: ["model"]
}
</script>