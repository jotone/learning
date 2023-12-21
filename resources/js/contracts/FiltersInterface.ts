export interface FiltersInterface {
  page: number,
  per_page: number,
  order: {
    by: string,
    dir: string
  },
  search: string
}