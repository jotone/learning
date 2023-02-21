<template>
  <form class="search-form" action="#" name="search">
    <label>
      <input
        class="search-field"
        name="search"
        placeholder="Search for users…"
        pattern=".{0}|.{3,}"
        type="search"
        value=""
      >
      <button class="search-button" type="submit">
        <span></span>
      </button>
    </label>
  </form>
</template>

<script>
export default {
  name: "SearchForm"
}
</script>