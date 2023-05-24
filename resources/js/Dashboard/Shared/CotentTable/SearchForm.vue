<template>
  <form class="search-form" action="#" name="search">
    <label>
      <input
        class="search-field"
        pattern=".{0}|.{3,}"
        type="search"
        :name="name"
        :placeholder="placeholder"
        :value="$page.props.filters.search || ''"
      >
      <button class="search-button" type="submit">
        <span></span>
      </button>
    </label>
  </form>
</template>

<script>
export default {
  name: "SearchForm",
  props: {
    name: {
      type: String,
      default: 'search'
    },
    placeholder: {
      type: String,
      default: ''
    }
  }
}
</script>