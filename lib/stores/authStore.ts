import { create } from 'zustand'
import { jwtDecode } from 'jwt-decode'
import type { LoginCredentials, LoginResponse } from '@/types/auth'
import type { User } from "@/lib/stores/users"
import apiService from '@/lib/apiService'

interface AuthState {
  user: User | null
  authenticationAttempted: boolean
  csrf: boolean
  login: (credentials: LoginCredentials) => Promise<void>
  logout: () => void
  getCurrentUser: () => Promise<User | null>
  isAuthenticated: () => boolean
  csrfPreflight: () => Promise<void>
}

export const useAuthStore = create<AuthState>((set, get) => ({
  user: null,
  authenticationAttempted: false,
  csrf: false,

  login: async (credentials) => {
    await apiService.post<LoginResponse>('/auth', credentials)
  },

  logout: async () => {
    await apiService.delete<LoginResponse>('/logout')
    set({ csrf: false, user: null })
    await get().csrfPreflight()

    window.location.href = '/login'
  },

  getCurrentUser: async () => {
    set({ authenticationAttempted: false })
    const response = await apiService.get('/user', { userPreflight: true })
    set({ user: response.data.data })
    return get().user
  },

  isAuthenticated: () => {
    return !!get().getCurrentUser()
  },

  csrfPreflight: async () => {
    await apiService.get('/sanctum/csrf-cookie', { withCredentials: true })
    set({ csrf: true })
  },
}))
