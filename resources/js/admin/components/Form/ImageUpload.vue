<template>
  <div class="image-upload">
    <div class="image-upload__preview" v-if="previewImage.src.length">
      <img :src="previewImage.src" alt="">
    </div>

    <div class="image-upload__placeholder" v-if="viewPlaceholder && !previewImage.src.length">
      <div class="image-upload__placeholder__wrap">
        <img src="/resources/assets/images/upload.png" alt="">
      </div>

      <div class="image-upload__placeholder__text" v-if="placeholderText.length" v-html="placeholderText"></div>

      <div class="image-upload__placeholder__helper">
        Upload an image by <a href="javascript:void(0)">clicking here</a> or drag and drop the file.
      </div>
    </div>

    <div class="image-upload__controls">
      <i class="icon file-upload-icon" title="Upload Image" @click="fileInput.click()"></i>
      <i class="icon trash-icon" title="Remove Image" @click="removeImage"></i>
    </div>

    <input type="file" ref="fileInput" @change="fileUploadEvent" :accept="Object.keys(mimes).join(',')"/>
  </div>

  <Teleport to="body">
    <ImageCropPopup ref="imageCropper" @cancel="clearFileInput"/>
  </Teleport>
</template>

<script setup>
// Vue libs
import {inject, reactive, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
// Mime type lib
import mimeType from "mime-types"
import ImageCropPopup from "../Popup/ImageCropPopup.vue";

// Assign the function to emit
const emit = defineEmits(['onRemove']);

// Assign the http request function
const request = inject('request')

// Page variable
const page = usePage();

// Get component properties
const props = defineProps({
  placeholderText: {
    type: String,
    default: ''
  },
  viewPlaceholder: {
    type: Boolean,
    default: true
  },
  value: {
    type: [Array, Object, String],
    default: null
  }
})

/*
 * Methods
 */
/**
 * Clear the file input value
 */
const clearFileInput = () => {
  fileInput.value.value = '';
}

/**
 * File upload event handler
 * @param {Event} e
 */
const fileUploadEvent = e => {
  const file = e.target.files[0];
  if (file && file.type.match("image.*")) {
    const reader = new FileReader();
    reader.readAsDataURL(file);

    // Read the file as a data URL or blob URL
    reader.onloadend = () => {
      imageCropper.value.src = reader.result
      imageCropper.value.open().then(res => {
        if (false === res) {
          clearFileInput()
        } else {
          previewImage.src = res;
          previewImage.name = file.name;
          previewImage.mime = file.type;
          previewImage.upload = true;
          imageCropper.value.cancel()
        }
      })
    };
  }
}

/**
 * Convert an image blob into the File object
 * @returns {File}
 */
const getData = () => urlToFile(previewImage);

/**
 * Remove image handler
 */
const removeImage = () => {
  if (typeof previewImage.src === 'string') {
    // Get id from the url
    let id = +([...window.location.href].reverse().find(char => !isNaN(+char)) || null);
    // Send a request to remove the image if ID is not a null
    null !== id && request({
      url: page.props.routes.resource.image.destroy,
      method: 'delete',
      data: {
        path: previewImage.src,
        entity: 'App\\Models\\Category',
        entity_id: id,
        field: 'img_url'
      }
    })
  }
  // Clear preview image
  previewImage.src = '';
  previewImage.name = '';
  previewImage.mime = null;
  previewImage.upload = false;
  emit('onRemove');
}

/**
 * Convert a base64 encoded binary string into the File object
 * @param file
 * @returns {*|File}
 */
const urlToFile = file => {
  if (!file.upload || null === file.mime) {
    return file.src;
  }
  // Split the base64 string into the data and mimeType
  const parts = file.src.split(';base64,');
  const mimeType = file.mime || parts[0].split(':')[1];
  // Decode base64
  const binaryString = atob(parts[1]);
  const length = binaryString.length;
  const uint8Array = new Uint8Array(length);

  // Convert binary string to Uint8Array
  for (let i = 0; i < length; i++) {
    uint8Array[i] = binaryString.charCodeAt(i);
  }

  // Create a Blob from the Uint8Array
  const blob = new Blob([uint8Array], {type: mimeType});

  // Create and return a File object
  return new File([blob], file.name, {type: mimeType});
}
/*
 * Variables
 */
// Allowed mime types
const mimes = {
  'image/jpeg': mimeType.extensions['image/jpeg'],
  'image/gif': mimeType.extensions['image/gif'],
  'image/png': mimeType.extensions['image/png'],
};

// File input
let fileInput = ref(null)
// Image cropper popup
let imageCropper = ref(null)
// Preview image src
let src = null;
// Image preview object
let previewImage = reactive({
  src: '',
  name: '',
  mime: null,
  upload: false
})

if (null !== props.value) {
  if (Array.isArray(props.value)) {
    src = props.value[0] || null;
  } else if (typeof props.value === 'string') {
    src = props.value;
  } else if (typeof props.value === 'object') {
    src = props.value.hasOwnProperty('original') ? props.value.original : Object.values(props.value)[0];
  } else {
    src = null;
  }
  if (null !== src) {
    let path = src.split('/')
    previewImage.src = src;
    previewImage.name = path.at(-1);
    for (let mime in mimes) {
      if (mimes[mime].indexOf(previewImage.name.split('.').at(-1)) >= 0) {
        previewImage.mime = mime;
        break
      }
    }
  }
}


defineExpose({getData})
</script>