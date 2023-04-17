<template>
  <Layout>
    <template v-slot:optionals>
      <SaveButton form="template"/>
    </template>

    <template v-slot:content>
      <div class="page-content-wrap">
        <div class="row">
          <div class="col-1-2">

            <form
              class="card"
              id="template"
              :action="$attrs.routes.email.store"
              data-save-message="Email template was successfully saved."
              method="POST"
              @submit.prevent="submit"
            >
              <div class="card-title">
                Registration email
              </div>

              <InputText
                caption="Email Title"
                name="title"
                :required="true"
              />

              <InputText
                caption="Email Header"
                name="subject"
              />

              <div class="form-group">
                <textarea name="text" class="init-cke"></textarea>
              </div>

              <TextArea
                caption="Text below action button"
                name="footer_text"
              />
            </form>
          </div>
        </div>
      </div>
    </template>
  </Layout>
</template>

<script>
import {FormMixin} from "../../Mixins/form-mixin";
import InputText from "../../Shared/Form/InputText.vue";
import TextArea from "../../Shared/Form/TextArea.vue";

export default {
  components: {InputText, TextArea},
  mixins: [FormMixin],
  name: "EmailTemplates/Form",
  mounted() {
    $('.content-submenu').find('li[data-name="email"]').addClass('active');

    // Init CKEDITOR
    CKEDITOR.replace('text', {
      extraPlugins: ['font', 'justify'],
      toolbar: [
        [
          'Undo', 'Redo', '-',
          'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-',
          'FontSize', 'Font', '-',
          'NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-',
          'Link', 'Unlink', '-', 'Maximize', 'Source'
        ]
      ],
      removeButtons: ''
    });
  }
}
</script>