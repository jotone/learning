import ContentTableHead from "../Layouts/ContentTable/ContentTableHead.vue";
import DefaultLayout from "../Layouts/DefaultLayout.vue";
import {Link} from "@inertiajs/vue3";
import Pagination from "../Layouts/ContentTable/Pagination.vue";
import SearchForm from "../Layouts/ContentTable/SearchForm.vue";
import TopMenu from "../Layouts/TopMenu.vue";

export const ContentTableMixin = {
  components: {ContentTableHead, DefaultLayout, Link, Pagination, SearchForm, TopMenu},
  data() {
    return {
      collection: [],
      pagination: {},
      url: ''
    }
  },
  methods: {
    /**
     * Convert filters to URI string
     * @param uri
     * @returns {string}
     */
    filtersToUri(uri = '?') {
      for (let field in this.$attrs.filters) {
        const value = this.$attrs.filters[field]
        // Nested params
        if (typeof value === 'object') {
          for (let option in value) {
            uri += `${field}[${option}]=${encodeURIComponent(value[option])}&`
          }
        } else if (!!value) {
          // Common params
          uri += `${field}=${encodeURIComponent(value)}&`
        }
      }
      return uri.slice(0, -1)
    },
    /**
     * Get collection items
     *
     * @param url
     * @returns {Promise<axios.AxiosResponse<any>>}
     */
    getCollection(url = null) {
      if (null === url) {
        url = this.url + this.filtersToUri()
      }
      return $.axios.get(url)
        .then(response => {
          if (200 === response.status) {
            this.setPaginationOptions(response.data.meta)
            this.collection = response.data.data
          }
        })
    },
    /**
     * Figure out pagination parameters
     * @param meta
     */
    setPaginationOptions(meta)
    {
      this.pagination = meta;

      // Pagination first item
      this.pagination.start = this.pagination.current_page - 6;
      // Pagination last item
      this.pagination.finish = this.pagination.current_page + 6;

      // Check there are few pages
      if (this.pagination.finish > this.pagination.last_page) {
        this.pagination.start = this.pagination.last_page - 12;
      }

      // First item cannot be less than 1
      if (this.pagination.start < 1) {
        this.pagination.finish += Math.abs(this.pagination.start) + 1;
        // Set first item as 1
        this.pagination.start = 1
      }
      // Last item cannot be greater than total pages number
      if (this.pagination.finish > this.pagination.last_page) {
        this.pagination.finish = this.pagination.last_page;
      }
    }
  }
}