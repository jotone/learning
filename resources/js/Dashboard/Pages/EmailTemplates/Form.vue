<template>
  <Layout>
    <template v-slot:optionals>
      <SaveButton form="template"/>
    </template>

    <template v-slot:content>
      <div class="page-content-wrap">
        <form
          class="row"
          id="template"
          :action="$attrs.routes.email.form"
          data-save-message="Email template was successfully saved."
          data-success-callback="formSaved"
          method="POST"
          @submit.prevent="submit"
        >
          <input name="_method" type="hidden" value="PUT" v-if="$attrs.hasOwnProperty('model')">
          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                Registration email
              </div>

              <input type="hidden" name="variables" :value="JSON.stringify(variables)">

              <InputText
                caption="Email Title"
                name="title"
                :required="true"
                :value="$attrs?.model?.title"
              />

              <InputText
                caption="Email Header"
                name="subject"
                :value="$attrs?.model?.subject"
              />

              <div class="form-group">
                <textarea name="body" class="init-cke">{{ $attrs?.model?.body }}</textarea>
              </div>

              <TextArea
                caption="Text below action button"
                name="footer_text"
                :value="$attrs?.model?.footer_text"
              />
            </div>
          </div>

          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                Variables
              </div>

              <div class="table-group">
                <div class="table-wrap">
                  <table>
                    <thead>
                    <tr>
                      <th><span>Name</span></th>
                      <th><span>Entity</span></th>
                      <th><span>Entity Field</span></th>
                      <th><span>Actions</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(variable, i) in variables" :data-pos="i">
                      <td>
                        <a href="#" data-role="name" data-fancybox data-src="#variable" @click="variableEdit">
                          %{{ variable.name }}%
                        </a>
                      </td>
                      <td>
                        <a href="#" data-role="entity" data-fancybox data-src="#variable" @click="variableEdit">
                          {{ entities[variable.entity].name }}
                        </a>
                      </td>
                      <td>
                        <a href="#" data-role="field" data-fancybox data-src="#variable" @click="variableEdit">
                          {{ entities[variable.entity].fields[variable.field] }}
                        </a>
                      </td>
                      <td>
                        <a class="remove" href="#" @click.prevent="variableRemove"></a>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>

                <div class="form-group">
                  <a class="btn blue" href="#" data-fancybox data-src="#variable" @click="resetVariableForm">
                    Add Variable
                  </a>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </template>

    <template v-slot:popup>
      <div class="modal" id="variable" style="max-width: 700px; display: none">
        <div class="modal-title">
          Email variable
        </div>
        <form class="modal-body" @submit.prevent="variableSave">
          <InputText
            caption="Variable name"
            name="var_name"
            :required="true"
            :style="'min-width: 420px'"
          />
          <div class="form-group">
            <label class="caption">
              <span>Variable Entity</span>
              <select name="var_entity" class="form-select" @change="changeEntity">
                <option v-for="(entity, type) in entities" :value="type">
                  {{ entity.name }}
                </option>
              </select>
            </label>
          </div>
          <div class="form-group">
            <label class="caption">
              <span>Variable Entity Field</span>

              <select name="var_field" class="form-select">
                <option v-for="(field, type) in entities[chosenEntity].fields" :value="type">
                  {{ field }}
                </option>
              </select>
            </label>
          </div>
          <div class="form-group">
            <button class="btn blue" type="submit">
              Save
            </button>
          </div>
        </form>
      </div>
    </template>
  </Layout>
</template>

<script>
import {Fancybox} from "@fancyapps/ui";
import {FormMixin} from "../../Mixins/form-mixin";
import ContentTableHead from "../../Shared/CotentTable/ContentTableHead.vue";
import InputText from "../../Shared/Form/InputText.vue";
import TextArea from "../../Shared/Form/TextArea.vue";

export default {
  components: {ContentTableHead, InputText, TextArea},
  data() {
    return {
      chosenEntity: 'date',
      entities: {
        date: {
          name: "Date",
          fields: {
            now: "Now"
          }
        },
        course: {
          name: "Course",
          fields: {
            title: "Name",
            url: "Link",
            img_url: "Image",
            fb_link: "Facebook URL",
            created_at: "Creation date"
          }
        },
        settings: {
          name: "Settings",
          fields: {
            site_url: "Site URL",
            site_title: "Site title",
          }
        },
        user: {
          name: "User",
          fields: {
            first_name: "First Name",
            last_name: "Last Name",
            full_name: "Full Name",
            email: "Email",
            activated_at: "Activation Date"
          }
        }
      },
      variablePosition: null,
      variables: this.$attrs.hasOwnProperty('model') ? this.parseModelVariables() : []
    }
  },
  methods: {
    /**
     * Change variable entity
     * @param e
     */
    changeEntity(e) {
      this.chosenEntity = $(e.target).closest('select').val()
    },
    formSaved(response) {
      if (201 === response.status) {
        $('#template')[0].reset()
        CKEDITOR.instances.body.setData('')
        this.variables = []
      }
    },
    parseModelVariables() {
      let result = []
      for (let name in this.$attrs.model.variables) {
        result.push({
          name: name,
          entity: this.$attrs.model.variables[name][0],
          field: this.$attrs.model.variables[name][1]
        })
      }
      return result;
    },
    /**
     * Reset variable form
     */
    resetVariableForm() {
      // Reset modal form
      $('#variable form')[0].reset();
      // Set default entity value
      this.chosenEntity = 'date';
      // Set the variable is being creating
      this.variablePosition = null;
    },
    /**
     * View an edit variable form
     * @param e
     */
    variableEdit(e) {
      // Modal form
      const form = $('#variable form');
      // Editable variable row
      const parent = $(e.target).closest('tr');
      // Editable variable row position
      this.variablePosition = parseInt(parent.attr('data-pos'))
      // Editable variable data
      const variable = this.variables[this.variablePosition]
      // Set variable name to rhe form
      form.find('input[name="var_name"]').val(variable.name.replace(/^%|%$/g, ''))
      // Change the "var_field select" option list
      this.chosenEntity = variable.entity
      // Wait until the "var_field select" option list will be formed
      new Promise(resolve => {
        // Set the "var_entity select" selected value
        form.find('select[name="var_entity"]').val(variable.entity)
        resolve({})
      }).then(() => {
        // Set the "var_field select" selected value
        form.find('select[name="var_field"]').val(variable.field).change()
      })
    },
    /**
     * Remove variable
     * @param e
     */
    variableRemove(e) {
      this.variables.splice(parseInt($(e.target).closest('tr').attr('data-pos')), 1)
    },
    /**
     * Add/Edit variable
     * @param e
     */
    variableSave(e) {
      const form = $(e.target).closest('form')
      const data = {
        name: form.find('input[name="var_name"]').val().trim().replace(/^%|%$/g, ''),
        entity: form.find('select[name="var_entity"]').val(),
        field: form.find('select[name="var_field"]').val()
      }

      if (null === this.variablePosition) {
        // Add variable
        this.variables.push(data)
      } else {
        // Modify variable
        this.variables[this.variablePosition] = data
      }
      // Reset form
      this.resetVariableForm()
      // Close modal window
      Fancybox.close('#variable')
    }
  },
  mixins: [FormMixin],
  name: "EmailTemplates/Form",
  mounted() {
    $('.content-submenu').find('li[data-name="email"]').addClass('active');

    // Fancybox init
    Fancybox.bind('a[data-fancybox]', {
      hideScrollbar: false
    });

    // Init CKEDITOR
    CKEDITOR.replace('body', {
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

<style>
@import "/node_modules/@fancyapps/ui/dist/fancybox/fancybox.css";
</style>