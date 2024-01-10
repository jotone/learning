import {nextTick} from "vue";

export class Notification {
  /**
   * Get notifications
   * @return {Array}
   */
  static async get()  {
    return await nextTick(() => {
      const storageData = localStorage.getItem('notifications');
      localStorage.removeItem('notifications')
      return null !== storageData ? JSON.parse(storageData) : []
    })
  }

  /**
   * Set notification message
   * @param {string} message
   * @param {string} type
   */
  static set(message, type) {
    const storageData = localStorage.getItem('notifications');
    let messages = null !== storageData ? JSON.parse(storageData) : [];

    messages.push({text: message, type: type})

    localStorage.setItem('notifications', JSON.stringify(messages))
  }

  static danger(message) {
    this.set(message, 'danger')
  }

  static success(message) {
    this.set(message, 'success')
  }

  static warning(message) {
    this.set(message, 'warning')
  }
}