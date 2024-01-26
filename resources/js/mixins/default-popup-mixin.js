export const DefaultPopupMixin = {
  data() {
    return {
      active: false,
      items: [],
      resolver: () => {}
    }
  },
  methods: {
    /**
     * Close the modal window
     * @param {event} e
     */
    close(e) {
      if (e.target.classList.contains('overlay') || null !== e.target.closest('.close-popup')) {
        this.active = false
        this.resolver(false)
      }
    },
    /**
     * Click button handler
     */
    handle() {
      this.resolver(this.items)
      this.active = false;
    },
  }
}