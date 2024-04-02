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
      if (e.target.classList.contains('overlay') || null !== e.target.closest('.default-popup__close')) {
        if (typeof this.reset === 'function') {
          this.reset();
        }
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
      if (typeof this.reset === 'function') {
        this.reset();
      }
    }
  }
}