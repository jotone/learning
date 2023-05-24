<template>
  <div class="form-group">
    <label class="caption">
      <span>{{ caption }}</span>
      <input
        type="file"
        style="display: none"
        :accept="accept"
        :name="name"
        @change="imageUploaded"
      >

      <span class="form-image-upload">
        <i class="image-holder" v-if="empty"></i>
        <span class="image-preview" v-if="!empty">
          <img :src="value || imgSrc" alt="" v-if="!isSvg">
          <span v-if="isSvg" v-html="imgSrc"></span>
        </span>
        <span class="image-text" v-html="__('common.image.text')"></span>
        <span class="image-info" v-html="__('common.image.info', getFormats(), size, getDimensions())"></span>
      </span>
    </label>
  </div>
</template>

<script>
export default {
  name: "ImageUpload",
  props: {
    caption: {
      type: String,
      default: ''
    },
    change: {
      type: Function,
      default: null
    },
    dimensions: {
      type: Array,
      default: () => null
    },
    formats: {
      type: [String, Array],
      default: () => null
    },
    name: {
      type: String,
      default: ''
    },
    size: {
      type: String,
      default: '300KB'
    },
    value: {
      type: String,
      default: () => null
    }
  },
  data() {
    return {
      accept: '', // accept attribute
      empty: !this.value, // image is not uploaded marker
      imgSrc: '', // image value (src attribute or svg tag)
      isSvg: false, // image is SVG marker
      mimes: {  // allowed mime-types
        "image/bmp": ["bmp"],
        "image/gif": ["gif"],
        "image/x-icon": ["ico"],
        "image/jpeg": ["jpe", "jpeg", "jpg", "pjpg", "jfif", "jfif-tbnl", "jif"],
        "image/png": ["png"],
        "image/svg+xml,image/svg": "svg",
        "image/tiff": ["tif", "tiff"],
        "image/webp": ["webp"]
      }
    }
  },
  methods: {
    /**
     * Set dimensions
     * @returns {string}
     */
    getDimensions() {
      const dimensions = this.dimensions || [380, 144]
      return dimensions.join(' x ')
    },
    /**
     * Set file formats
     * @returns {*|string}
     */
    getFormats() {
      // Check if message is already is string
      if (typeof this.formats === 'string') {
        return this.formats;
      }
      // Check the formats are empty then set it as a default array
      let formats = this.formats || ['jpg', 'png']
      // Check the formats are object then convert it to array
      if (typeof formats === 'object' && !Array.isArray(formats)) {
        formats = Object.keys(formats).map((key) => [Number(key), formats[key]]);
      }
      // Check if formats are an array
      if (Array.isArray(formats)) {
        // Prepare accept attribute for file input
        let accept = []
        for (let i = 0, n = formats.length; i < n; i++) {
          // The given file format is unknown
          let unknownFormat = true;
          for (let k in this.mimes) {
            if (this.mimes[k].indexOf(formats[i]) >= 0) {
              // Fill accept array with the file mime-type
              accept.push(k)
              // Remove unknown format marker
              unknownFormat = false
            }
          }
          // Throw an error if the file format is unknown
          if (unknownFormat) {
            throw new RangeError('Unknown file type in mime list. See Layouts/Form/ImageUpload. The given file format is ' + formats[i])
          }
        }
        // Set an accept attribute value
        this.accept = accept.join(',')

        // Set format names
        formats = formats.map(str => str.toUpperCase()).join(', ')
      }

      return formats.replace(/,\s(?=[^,]*$)/, ' or ') // TODO: make here translation for " or "
    },
    /**
     * Preview image on input file upload
     * @param e
     */
    imageUploaded(e) {
      const _this = $(e.target).closest('input');
      const file = _this.prop('files')[0];
      const reader = new FileReader();
      // Set the image no need the default preview svg anymore
      this.empty = false
      // Set image body to source
      reader.onload = () => {
        this.imgSrc = reader.result
        if (typeof this.change === 'function') {
          this.change(reader)
        }
      }
      // Check if uploaded image is an SVG file
      if (file.type === 'image/svg+xml') {
        this.isSvg = true
        reader.readAsText(file);
      } else {
        this.isSvg = false
        reader.readAsDataURL(file);
      }
    }
  }
}
</script>