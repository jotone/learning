<template>
  <TransitionGroup class="notification-list" name="notification-list" tag="ul" v-if="notificationList.length">
    <li
      v-for="(message, i) in notificationList"
      v-html="message.text"
      :class="message.type"
      :key="i"
    ></li>
  </TransitionGroup>
</template>

<script setup>
import {ref} from "vue";
import {Notification} from "../../libs/Notification.js";

// Listen to the custom event (if you choose to use event dispatching)
let notificationList = ref([]);
// Notification delay time, ms
const notificationDelay = 3000;
// Listen if the localStorage.setItem function was called
window.addEventListener('localStorageSetItem', e => {
  if ('notifications' === e.detail.key && !!e.detail.value) {
    Notification.get().then(notifications => {
      // View notifications
      notificationList.value = notifications
      // Remove notifications
      setTimeout(() => {
        notificationList.value = []
      }, notificationDelay)
    })
  }
});
</script>