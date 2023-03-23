import SaveButton from "../Shared/Form/SaveButton.vue";
import { showNotification, XHRErrorHandle } from "../../libs/notifications";

export const FormMixin = {
  components: {SaveButton},
  methods: {
    /**
     * Send XHR request
     *
     * @param props
     */
    request(props) {
      if (!props.hasOwnProperty('url')) {
        throw new RangeError('Request action method is not set')
      }
      if (!props.hasOwnProperty('method')) {
        props.method = 'get';
      }

      $.axios.interceptors.request.use(config => {
        if (props.hasOwnProperty('beforeRequest')  && typeof props.beforeRequest === 'function') {
          props.beforeRequest()
        } else {
          $('.preloader').show()
        }

        return config;
      });

      $.axios[props.method](props.url, props.data ?? [], {
        headers: {
          "content-type": "multipart/form-data",
          "accept": "application/json"
        }
      })
        .then(response => {
          $('.preloader').hide()

          if (props.hasOwnProperty('onSuccess') && typeof props.onSuccess === 'function') {
            props.onSuccess(response)
          }

          if (!props.hasOwnProperty('preventNotification')) {
            showNotification({
              type: 'success',
              text: [props.saveMsg || this.messages.saved]
            })
          }
        })
        .catch(error => XHRErrorHandle(error, props))
    },
    /**
     * Get form data
     *
     * @param form
     * @returns {FormData}
     */
    serializeForm(form) {
      let formData = new FormData(form[0])

      if (typeof form.attr('id') !== 'undefined') {
        const formID = form.attr('id')
        $('#app').find(`[form="${formID}"]`).each(function () {
          const tag = $(this).prop('tagName').toLowerCase()
          const name = $(this).attr('name')
          if (typeof name !== 'undefined') {
            switch (tag) {
              case 'input':
                const type = $(this).attr('type')
                if (typeof type === 'undefined') {
                  formData.append(name, $(this).val())
                } else {
                  switch (type.toLowerCase()) {
                    case 'checkbox':
                      formData.append(name, $(this).prop('checked'))
                      break;
                    case 'radio':
                      formData.append(name, $(`${tag}[name="${name}"]:checked`).val())
                      break;
                    case 'hidden':
                    case 'number':
                    case 'text':
                      formData.append(name, $(this).val())
                      break;
                    case 'file':
                      formData.append(name, $(this).prop('files'))
                      break;
                  }
                }
                break;
              case 'select':
              case 'textarea':
                formData.append(name, $(this).val())
                break;
              default:
                console.log(name, $(this).val())
            }
          }
        })
      }

      return formData;
    },
    /**
     * @param e
     */
    submit(e) {
      const form = $(e.target).closest('form')
      if (typeof form.attr('action') === 'undefined') {
        throw new ReferenceError('Form action attribute is not declared.')
      }

      const body = {
        url: form.attr('action'),
        method: typeof form.attr('method') === 'undefined' ? 'get' : form.attr('method').toLowerCase(),
        data: this.serializeForm(form)
      };

      if (typeof form.attr('data-save-message') !== 'undefined') {
        body.saveMsg = form.attr('data-save-message')
      }

      if (typeof form.attr('data-success-callback') !== 'undefined') {
        body.onSuccess = this[form.attr('data-success-callback')]
      }

      this.request(body)
    }
  }
}