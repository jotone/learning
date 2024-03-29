export interface ResponseInterface {
  current_page: number,
  data: Array<object>,
  has_more_pages: boolean,
  last_page: number,
  per_page: number,
  total: number
}