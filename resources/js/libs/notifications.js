export function showNotification(messages) {
  if (!Array.isArray(messages)) {
    messages = [messages]
  }
  for (let i = 0, n = messages.length; i < n; i++) {
    const msg = messages[i]

    let message = ''
    for (let text of msg.text) {
      const caption = msg.hasOwnProperty('caption') && msg.caption.length
        ? `<div className="caption">${msg.caption}</div>`
        : '';
      message += `<li class="${msg.type}">${caption}<div class="text">${text}</div></li>`
    }

    $('.notifications-wrap ul').append(message)
    $('.notifications-wrap ul li').fadeIn(250).delay(3000).fadeOut(125, function () {
      $(this).remove()
    })
  }
}