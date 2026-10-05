export type ResourceKind = 'programs' | 'courses' | 'academic-terms'

export interface DomainRecord { id: number; [key: string]: unknown }

export interface AcademicRecord {
  student?: DomainRecord
  terms?: Array<{ id?: number; name?: string; academic_year?: string; semester?: string; courses?: DomainRecord[]; grades?: DomainRecord[] }>
  [key: string]: unknown
}
