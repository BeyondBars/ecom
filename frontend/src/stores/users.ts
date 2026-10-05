import { defineStore } from 'pinia'
import { http } from '@/utils/http'
import { PureAbility } from '@casl/ability'
import type { UserResource } from '@/types/users'
import { ref, computed } from 'vue'

export const useUserStore = defineStore('user', {
  state: () => ({
    users: [] as UserResource[],  // Array of users
    user: {} as UserResource | null, // Single user (nullable),
    currentPage: ref<number>(1), // Track current page
    totalPages: ref<number>(null), // Store total pages
    perPage: ref<number>(null),
    lastPage: ref<number>(null),
    isLoading: ref<boolean>(false),
    hasMore: ref<boolean>(true),
    meta: {}, // Items per page
  }),

  getters: {
    ability: () => new PureAbility([]),
    can: (state) => (ability) => state.ability.can(ability),
    canAny: (state) => (...abilities) => abilities.some((ability) => state.ability.can(ability)),
    cannot: (state) => (ability) => !state.ability.can(ability),
    isA: (state) => (role) => state.user.role?.name === role,
    isNotA: (state) => (role) => state.user.role?.name !== role,
  },

  actions: {
    async fetchUsers({ currentPage } = this.currentPage) {
      // Prevent unnecessary API calls
      if (this.isLoading || !this.hasMore) return
    
      try {
        this.isLoading = true
    
        const { data } = await http.get(`users?page=${currentPage}`)
    
        // Append new users instead of replacing
        this.users.push(...data.data)
    
        // Update pagination state
        this.currentPage = data.current_page + 1
        this.perPage = data.per_page
        this.lastPage = data.last_page
        this.totalPages = Math.ceil(data.total / data.per_page)
    
        // Check if more pages are available
        this.hasMore = this.currentPage <= this.lastPage
      } catch (error) {
        console.error('Error fetching users:', error)
      } finally {
        this.isLoading = false
      }
    },
    async fetchUser(id: [number, string]) {
      try {
        const response = await http.get<UserResource>(`users/${id}`)
        this.user = response.data.data
      } catch (error) {
        console.error('Error fetching user:', error)
      }
    },

    async createUser(userData: Omit<UserResource, 'id'>) {
      try {
        const response = await http.post<UserResource>('users', userData)
        this.users.push(response.data)
      } catch (error) {
        console.error('Error creating user:', error)
      }
    },

    async updateUser(id: number, userData: Partial<UserResource>) {
      const response = await http.put<UserResource>(`users/${id}`, userData)
      this.user = response.data.data
      this.users = this.users.map(user => user.id === id ? response.data : user)
    },

    async deleteUser(id: number) {
      try {
        await http.delete(`users/${id}`)
        this.users = this.users.filter(user => user.id !== id)
      } catch (error) {
        console.error('Error deleting user:', error)
      }
    },
    async disableTwoFactor({ id }: { id?: number } = {}) {
      const response = await http.put<UserResource>(`users/${id}/disable-two-factor`)

      return { response, resource: response.data.data }
    },
    resetUser() {
      this.user = {}
    },
  },
})