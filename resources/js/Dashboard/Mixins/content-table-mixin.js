import ContentTableHead from "../Shared/CotentTable/ContentTableHead.vue";
import Confirmation from "../Shared/Confirmation.vue";
import Layout from "../Shared/Layout.vue";
import Pagination from "../Shared/CotentTable/Pagination.vue";
import SearchForm from "../Shared/CotentTable/SearchForm.vue";
import {Link} from "@inertiajs/vue3";
import {showNotification, XHRErrorHandle} from "../../libs/notifications";
import axios from "axios";

export const ContentTableMixin = {
  components: {ContentTableHead, Confirmation, Layout, Link, Pagination, SearchForm},
  data() {
    return {
      removalName: '',
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
      for (let field in this.$page.props.filters) {
        const value = this.$page.props.filters[field]
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
     * Get collection of items
     *
     * @param url
     * @returns {Promise<axios.AxiosResponse<any>>}
     */
    getCollection(url = null) {
      if (null === url) {
        url = this.url + this.filtersToUri()
      }

      axios.interceptors.request.use(config => {
        $('.preloader').show()
        return config;
      });
      return axios.get(url).then(response => {
        if (200 === response.status) {
          this.setPaginationOptions(response.data.meta)
          this.collection = response.data.data
        }
      })
        .catch(error => XHRErrorHandle(error))
    },
    /**
     * Remove resource entity and refreshthe content list
     * @param obj
     * @param translation
     */
    remove(obj, translation) {
      axios.interceptors.request.use(config => {
        $('.preloader').show()
        return config;
      });
      // Check if link has href attribute && // Open the Confirmation popup
      typeof obj.attr('href') !== 'undefined' && this.$refs.confirmation.open().then(
        // If user pressed OK && // Send DELETE resource request
        res => res && axios.delete(obj.attr('href')).then(
          // Check the "204 No Content" response && // Refresh page content
          response => 204 === response.status && this.getCollection().then(() => showNotification([{
            type: "warning",
            text: [this.__(translation, this.removalName)]
          }]))
        )
      )
    },
    /**
     * Figure out pagination parameters
     * @param meta
     */
    setPaginationOptions(meta) {
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
  },
  provide() {
    return {
      filtersToUri: this.filtersToUri,
      getCollection: this.getCollection,
      removeEntity: this.removeEntity
    }
  },
  mounted() {
    this.getCollection()
  }
}