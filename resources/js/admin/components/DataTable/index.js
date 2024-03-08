export { default as BulkActions } from './BulkActions.vue';
export { default as Pagination } from './Pagination.vue';
export { default as PerPage } from './PerPage.vue';
export { default as RowActions } from './RowActions.vue';
export { default as SearchForm } from './SearchForm.vue';
export { default as StatusTooltip } from './StatusTooltip.vue';
export { default as TableHeadCol } from './TableHeadCol.vue';

export function getFilters(query) {
  return {
    page: query.page || 1,
    per_page: query.per_page ?? 25,
    order: {
      by: query.order?.by ?? 'created_at',
      dir: query.order?.dir ?? 'desc'
    },
    search: query.search ?? ''
  }
}