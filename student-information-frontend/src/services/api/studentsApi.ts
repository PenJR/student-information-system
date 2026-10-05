import api from './client'
import type { Paginated, Program, Student } from '../../types/api'

export interface StudentFilters {
  search?: string
  program_id?: string
  year_level?: string
  status?: string
  sort?: string
  page?: number
  per_page?: number
}

export async function listStudents(filters: StudentFilters) {
  const response = await api.get<{ data: Paginated<Student> }>('/students', { params: filters })
  return response.data.data
}

export async function listPrograms() {
  const response = await api.get<{ data: Paginated<Program> }>('/programs', { params: { per_page: 100 } })
  return response.data.data.data
}
