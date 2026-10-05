import { createContext, useContext, useEffect, useState, type ReactNode } from 'react'
import { currentUser, login as loginRequest, logout as logoutRequest } from '../services/api/authApi'
import type { User } from '../types/api'

interface AuthContextValue {
  user: User | null
  loading: boolean
  login: (email: string, password: string) => Promise<void>
  logout: () => Promise<void>
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const token = localStorage.getItem('student_api_token')
    const handleExpired = () => setUser(null)
    window.addEventListener('auth:expired', handleExpired)
    if (!token) {
      setLoading(false)
      return () => window.removeEventListener('auth:expired', handleExpired)
    }
    currentUser().then(setUser).catch(() => setUser(null)).finally(() => setLoading(false))
    return () => window.removeEventListener('auth:expired', handleExpired)
  }, [])

  async function login(email: string, password: string) {
    const result = await loginRequest(email, password)
    localStorage.setItem('student_api_token', result.token)
    setUser(result.user)
  }

  async function logout() {
    try { await logoutRequest() } finally {
      localStorage.removeItem('student_api_token')
      setUser(null)
    }
  }

  return <AuthContext.Provider value={{ user, loading, login, logout }}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const context = useContext(AuthContext)
  if (!context) throw new Error('useAuth must be used inside AuthProvider')
  return context
}
