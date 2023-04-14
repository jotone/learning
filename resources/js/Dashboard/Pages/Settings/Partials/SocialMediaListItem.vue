<template>
  <li :data-id="item.id">
    <div class="drag-list-handle">
      <i class="icon v-hellip-icon" style="background-color: #aca7bf"></i>
    </div>
    <div class="drag-list-dropdown">
      <select name="social_type">
        <option v-for="(media, i) in socialMedia" :selected="i === item.type" :value="i">
          {{ media }}
        </option>
      </select>
    </div>
    <div class="drag-list-url">
      <input class="form-input" name="social_url" :value="item.url" @input="socialKeyInput">
    </div>
    <div class="drag-list-remove">
      <a :href="removeLink" class="remove" @click.prevent="socialRemove"></a>
    </div>
  </li>
</template>

<script>
import debounce from "debounce"

export default {
  computed: {
    removeLink() {
      return this.$page.props.routes.social.destroy.replace(/0$/, this.item.id)
    }
  },
  data() {
    return {
      socialMedia: {
        facebook: 'Facebook',
        instagram: 'Instagram',
        linkedin: 'LinkedIn',
        tiktok: 'TikTok',
        twitter: 'Twitter',
        youtube: 'Youtube'
      }
    }
  },
  name: "SocialMediaListItem",
  props: ["item"],
  methods: {
    socialUpdate(obj) {
      let formData = new FormData();
      formData.append('_method', 'put')
      formData.append('type', obj.find('select[name="social_type"]').val())
      formData.append('url', obj.find('input[name="social_url"]').val().trim())

      this.$parent.$parent.request({
        method: 'post',
        url: this.$page.props.routes.social.update.replace(/0$/, obj.data('id')),
        data: formData,
        msg: "Social Media Link was successfully saved."
      })
    },
    socialKeyInput: debounce(function (e) {
      this.socialUpdate($(e.target).closest('li'))
    }, 500),
    /**
     * Remove social item
     * @param e
     */
    socialRemove(e) {
      // document parent <a> object
      const obj = $(e.target).closest('a')
      // Sending request
      this.$parent.$parent.request({
        method: 'delete',
        url: obj.attr('href'),
        preventNotification: !0,
        onSuccess: response => {
          // If 204 No Content -> remove social list item
          if (204 === response.status) {
            for (let i in this.$page.props.social) {
              // If item id is equal to the list item id -> remove this list item
              this.$page.props.social[i].id === this.item.id && this.$page.props.social.splice(i, 1);
            }
          }
        }
      })
    }
  },
  mounted() {
    $(document).on('change', 'select[name="social_type"]', e => {
      e.stopImmediatePropagation();
      this.socialUpdate($(e.target).closest('li'))
    })
  }
}
</script>