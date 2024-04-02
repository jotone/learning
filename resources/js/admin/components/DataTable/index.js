export { default as BulkActions } from './BulkActions.vue';
export { default as ColumnSelector } from './ColumnSelector.vue';
export { default as FilterDropdown } from './FilterDropdown.vue';
export { default as Pagination } from './Pagination.vue';
export { default as PerPage } from './PerPage.vue';
export { default as RowActions } from './RowActions.vue';
export { default as SearchForm } from './SearchForm.vue';
export { default as StatusTooltip } from './StatusTooltip.vue';
export { default as TableHeadCol } from './TableHeadCol.vue';

/**
 * Build filters object
 * @param query
 * @returns {{per_page: (any|number), search: string, page: number, order: {by: (any|string), dir: (*|string)}}}
 */
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