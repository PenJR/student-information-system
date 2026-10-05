import api from './client'

export type ResourceRecord = Record<string, unknown> & { id: number }

export interface ResourceFilters {
  search?: string
  page?: number
  per_page?: number
  sort?: string
}

export interface LaravelCollection<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

function unwrap<T>(payload: unknown): T {
  if (payload && typeof payload === 'object' && 'data' in payload) return (payload as { data: T }).data
  return payload as T
}

export async function listResource<T extends ResourceRecord>(endpoint: string, params?: ResourceFilters) {
  const response = await api.get<unknown>(endpoint, { params })
  const value = unwrap<unknown>(response.data)
  if (Array.isArray(value)) return { data: value as T[], current_page: 1, last_page: 1, per_page: value.length, total: value.length }
  return value as LaravelCollection<T>
}

export async function getResource<T>(endpoint: string, id: string | number) {
  const response = await api.get<unknown>(`${endpoint}/${id}`)
  return unwrap<T>(response.data)
}

export async function createResource<T extends ResourceRecord>(endpoint: string, payload: Record<string, unknown>) {
  const response = await api.post<unknown>(endpoint, payload)
  return unwrap<T>(response.data)
}

export async function updateResource<T extends ResourceRecord>(endpoint: string, id: string | number, payload: Record<string, unknown>) {
  const response = await api.patch<unknown>(`${endpoint}/${id}`, payload)
  return unwrap<T>(response.data)
}

export async function deleteResource(endpoint: string, id: string | number) {
  await api.delete(`${endpoint}/${id}`)
}

export async function listNested<T>(endpoint: string) {
  const response = await api.get<unknown>(endpoint)
  const value = unwrap<unknown>(response.data)
  return Array.isArray(value) ? value as T[] : (value as { data?: T[] }).data || []
}

export async function createGrade(payload: Record<string, unknown>) { return createResource<ResourceRecord>('/grades', payload) }
export async function updateGrade(id: string | number, payload: Record<string, unknown>) { return updateResource<ResourceRecord>('/grades', id, payload) }
