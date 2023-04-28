<template>
  <div class="table-group">
    <div class="table-wrap">
      <table>
        <thead>
        <tr>
          <th><span>Name</span></th>
          <th><span>Actions</span></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="template in list">
          <td><a :href="editLink(template.id)">{{ template.name }}</a></td>
          <td>
            <a
              class="remove"
              :href="removeLink(template.id)"
              @click.prevent="removeTemplate"
            ></a>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    /**
     * Edit email template url
     * @param id
     * @return {string}
     */
    editLink(id) {
      return this.$page.props.routes.emails.edit.replace(/0$/, id)
    },
    /**
     * Remove email template url
     * @param id
     * @return {string}
     */
    removeLink(id) {
      return this.$page.props.routes.emails.destroy.replace(/0$/, id)
    },
    /**
     * Remove email template
     * @param e
     */
    removeTemplate(e) {
      const url = $(e.target).closest('a').attr('href')
      this.$parent.$parent.request({
        method: 'delete',
        url: url,
        preventNotification: !0,
        onSuccess: response => {
          // If 204 No Content -> remove template
          if (204 === response.status) {
            const id = parseInt(url.substring(url.lastIndexOf('/') + 1))
            // If item id is equal to the template id -> remove this template
            for (let i in this.list) {
              this.list[i].id === id && this.list.splice(i, 1);
            }
          }
        }
      })
    }
  },
  name: "EmailTemplateList",
  props: ["list"]
}
</script>