export function showNotification(messages) {
  if (!Array.isArray(messages)) {
    messages = [messages]
  }
  for (let i = 0, n = messages.length; i < n; i++) {
    const msg = messages[i]

    let message = ''
    for (let text of msg.text) {
      const caption = msg.hasOwnProperty('caption') && msg.caption.length
        ? `<div class="caption">${msg.caption}</div>`
        : '';
      message += `<li class="${msg.type}">${caption}<div class="text">${text}</div></li>`
    }

    $('.notifications-wrap ul').append(message)
    $('.notifications-wrap ul li').fadeIn(250).delay(3000).fadeOut(125, function () {
      $(this).remove()
    })
  }
}

export const XHRErrorHandle = (error, props = {}) => {
  $('.preloader').hide()

  if (props.hasOwnProperty('onError') && typeof props.onSuccess === 'onError') {
    props.onError()
  }
  if (!props.hasOwnProperty('preventNotification')) {
    // Default message body
    let message = {
      type: 'error',
      caption: error.name
    }
    // Check the response property exists
    if (error.hasOwnProperty('response')) {
      message.caption = error.request.statusText;
      message.text = Object.keys(error.response.data.errors).map(key => error.response.data.errors[key]).flat(2)
      // Check the request property exists
    } else if (error.hasOwnProperty('request')) {
      let errors = JSON.parse(error.request.responseText)
      message.caption = error.request.statusText;
      message.text = Object.keys(errors).map(key => errors[key]).flat(2)
      // Default error handler
    } else if (error.hasOwnProperty('message')) {
      message.text = [error.message]
    } else {
      console.error(error)
    }

    showNotification(message)
  }
}