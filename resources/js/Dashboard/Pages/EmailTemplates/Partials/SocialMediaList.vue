<template>
  <draggable class="drag-list" item-key="id" handle=".drag-list-handle" tag="ul" :list="list" @change="onSort">
    <template #item="{ element }">
      <li :data-id="element.id">
        <div class="drag-list-handle">
          <i class="icon v-hellip-icon" style="background-color: #aca7bf"></i>
        </div>
        <div class="drag-list-dropdown">
          <select name="social_type">
            <option v-for="(media, i) in socialMedia" :selected="i === element.type" :value="i">
              {{ media }}
            </option>
          </select>
        </div>
        <div class="drag-list-url">
          <input class="form-input" name="social_url" :value="element.url" @input="socialKeyInput">
        </div>
        <div class="drag-list-remove">
          <a :href="removeLink(element.id)" class="remove" @click.prevent="socialRemove"></a>
        </div>
      </li>
    </template>
  </draggable>
</template>

<script>
import debounce from "debounce"
import draggable from "vuedraggable";

export default {
  components: {draggable},
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
  name: "SocialMediaList",
  props: ["list"],
  methods: {
    onSort() {
      let items = []
      $(this.$el).find('li').each(function () {
        items.push($(this).data('id'));
      })

      this.$parent.$parent.request({
        method: 'patch',
        url: this.$page.props.routes.social.sort,
        headers: {
          'content-type': 'application/json'
        },
        data: {list: items},
        preventNotification: !0,
        beforeRequest: null
      })
    },
    /**
     * Build a social media remove link
     * @param id
     * @returns {*}
     */
    removeLink(id) {
      return this.$page.props.routes.social.destroy.replace(/:id/, id)
    },
    /**
     * Update social media item
     * @param obj
     */
    socialUpdate(obj) {
      let formData = new FormData();
      formData.append('_method', 'put')
      formData.append('type', obj.find('select[name="social_type"]').val())
      formData.append('url', obj.find('input[name="social_url"]').val().trim())

      this.$parent.$parent.request({
        method: 'post',
        url: this.$page.props.routes.social.update.replace(/:id/, obj.data('id')),
        data: formData,
        msg: "Social Media Link was successfully saved."
      })
    },
    /**
     * Update social item value after 500 mls of input
     */
    socialKeyInput: debounce(function (e) {
      this.socialUpdate($(e.target).closest('li'))
    }, 500),
    /**
     * Remove social item
     * @param e
     */
    socialRemove(e) {
      // document parent <a> object
      const url = $(e.target).closest('a').attr('href')
      // Sending request
      this.$parent.$parent.request({
        method: 'delete',
        url: url,
        preventNotification: !0,
        onSuccess: response => {
          // If 204 No Content -> remove social list item
          if (204 === response.status) {
            const id = parseInt(url.substring(url.lastIndexOf('/') + 1))
            // If item id is equal to the list item id -> remove this list item
            for (let i in this.list) {
              this.list[i].id === id && this.list.splice(i, 1);
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
<script setup>
</script>