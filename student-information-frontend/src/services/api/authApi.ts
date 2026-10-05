import api from './client'
import type { User } from '../../types/api'

interface AuthResponse {
  data: { user: User; token: string }
}

export async function login(email: string, password: string) {
  const response = await api.post<AuthResponse>('/auth/login', { email, password })
  return response.data.data
}

export async function currentUser() {
  const response = await api.get<{ data: User }>('/auth/me')
  return response.data.data
}

export async function logout() {
  await api.post('/auth/logout')
}
