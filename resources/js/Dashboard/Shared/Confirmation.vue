<template>
  <div class="overlay" @click="checkCanBeClosed">
    <div class="confirmation-popup">
      <div class="popup-close" @click="close">
        <i class="icon times-icon"></i>
      </div>
      <div class="popup-content">
        <div class="question-text" v-html="text"></div>
      </div>
      <div class="buttons-wrap">
        <button name="apply" type="button" class="btn" :class="okBtnClass">
          {{ okText || __('common.yes') }}
        </button>
        <button name="cancel" type="button" class="btn" :class="noBtnClass">
          {{ noText || __('common.cancel') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    checkCanBeClosed(e) {
      !$(e.target).closest('.confirmation-popup') && this.close()
    },
    /**
     * Handle confirmation buttons events
     * @param resolve
     * @param result
     */
    handler(resolve, result) {
      this.close();
      resolve(result)
    },
    /**
     * Close confirmation popup
     */
    close() {
      $('.overlay').css({display: 'none'})
    },
    /**
     * Open confirmation popup
     * @returns {Promise<unknown>}
     */
    open() {
      const overlay = $('.overlay')
      overlay.css({display: 'flex'});
      return new Promise(resolve => {
        overlay
          .on('click', 'button[name="apply"]', () => this.handler(resolve, true))
          .on('click', 'button[name="cancel"]', () => this.handler(resolve, false))
      })
    }
  },
  name: "Confirmation",
  props: {
    text: {
      type: String,
      required: true
    },
    okBtnClass: {
      type: String,
      default: '.blue'
    },
    okText: {
      type: String,
      default: ''
    },
    noBtnClass: {
      type: String,
      default: ''
    },
    noText: {
      type: String,
      default: ''
    }
  }
}
</script>