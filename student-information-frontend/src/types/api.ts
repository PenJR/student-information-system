export type Role = 'ADMINISTRATOR' | 'STAFF' | 'INSTRUCTOR' | 'STUDENT'

export interface User {
  id: number
  name: string
  email: string
  role: Role
  status: string
}

export interface Program {
  id: number
  code: string
  name: string
  status: string
}

export interface Student {
  id: number
  student_number: string
  first_name: string
  middle_name?: string | null
  last_name: string
  suffix?: string | null
  email?: string | null
  year_level: number
  status: string
  program?: Program
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface Paginated<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}
