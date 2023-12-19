<template>
  <nav class="pagination-wrap">
    <ul>
      <li>
        <a
          href="options.current_page !== 1 ? `${options.path}${buildUrlParams(1)}` : '#'"
          @click.prevent="changePage"
        >
          <i class="icon double-chevron-icon"></i>
        </a>
      </li>
      <li>
        <a
          href="options.current_page !== 1 ? `${options.path}${buildUrlParams(options.current_page - 1)}` : '#'"
          @click.prevent="changePage"
        >
          <i class="icon chevron-icon"></i>
        </a>
      </li>
      <li>
        <a class="active" href="#">{{ 'options.current_page' }}</a>
      </li>
      <li><span>of {{ 'options.last_page' }}</span></li>
      <li>
        <a
          href="options.current_page !== options.last_page ? `${options.path}${buildUrlParams(options.current_page + 1)}` : '#'"
          @click.prevent="changePage"
        >
          <i class="icon mirror chevron-icon"></i>
        </a>
      </li>
      <li>
        <a
          href="options.current_page !== options.last_page ? `${options.path}${buildUrlParams(options.last_page)}` : '#'"
          @click.prevent="changePage"
        >
          <i class="icon mirror double-chevron-icon"></i>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script setup>
const changePage = () => {}
</script>