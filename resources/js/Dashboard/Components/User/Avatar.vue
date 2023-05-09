<template>
  <span class="avatar-wrap" :style="`background-color: rgb(${ avatarOptions.bg.join(',') })`">
    <span :style="`color: ${ avatarOptions.color }`">
      {{ avatarOptions.text }}
    </span>
  </span>
</template>

<script>

import {userAvatarColors} from "../../../libs/user-avatar-colors";

export default {
  name: "Avatar",
  computed: {
    avatarOptions() {
      return userAvatarColors(this.$page.props.auth)
    }
  }
}
</script>