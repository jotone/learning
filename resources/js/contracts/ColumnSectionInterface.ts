import {ColumnInterface} from './ColumnInterface';

export interface ColumnSectionInterface {
  columns: ColumnInterface[],
  icon: string,
  name: string,
  slug: string,
  page: string
}