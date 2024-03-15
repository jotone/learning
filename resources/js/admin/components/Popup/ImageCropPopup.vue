<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap image-upload-popup">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-body-wrap">
        <form class="popup-confirmation-wrap" ref="form">
          <Cropper :src="src" ref="cropper"/>

          <div class="popup-confirmation-button">
            <button class="btn blue--inverse" type="button" @click="cancel">
              Cancel
            </button>
            <button class="btn blue" type="button" @click="cropImage">
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
// Mixin
import {DefaultPopupMixin} from "../../../mixins/default-popup-mixin.js";
// Image Cropper lib
import {Cropper} from "vue-advanced-cropper";
import 'vue-advanced-cropper/dist/style.css';

export default {
  components: {Cropper},
  mixins: [DefaultPopupMixin],
  emits: ['cancel'],
  data() {
    return {
      src: ''
    }
  },
  methods: {
    /**
     * Cancel crop
     */
    cancel() {
      this.$emit('cancel')
      this.active = false
      this.resolver(false)
    },
    /**
     * Crop image and return base64 encoded binary string
     */
    cropImage() {
      this.resolver(this.$refs.cropper.getResult().canvas.toDataURL())
    },
    /**
     * Open the crop modal window
     * @returns {Promise<unknown>}
     */
    open() {
      this.active = true;
      return new Promise(resolve => {
        this.resolver = resolve
      })
    },
    /**
     * Reset file input event
     */
    reset() {
      this.$emit('cancel')
    },
  }
}
</script>