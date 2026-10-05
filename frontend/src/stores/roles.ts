import { defineStore } from 'pinia'
import { http } from '@/utils/http'
import { PureAbility } from '@casl/ability'
import type { RoleResource } from '@/types/role'
import { ref, computed } from 'vue'

export const useRoleStore = defineStore('role', {
  state: () => ({
    roles: [] as RoleResource[],
    role: {} as RoleResource | null,
    currentPage: ref<number>(1),
    totalPages: ref<number>(null),
    perPage: ref<number>(null),
    lastPage: ref<number>(null),
    isLoading: ref<boolean>(false),
    hasMore: ref<boolean>(true),
    meta: {}
  }),

  actions: {
    async fetchRoles({ currentPage } = this.currentPage) {
      // Prevent unnecessary API calls
      if (this.isLoading || !this.hasMore) return
    
      try {
        this.isLoading = true
    
        const { data } = await http.get('roles')
    
        // Append new roles instead of replacing
        this.roles.push(...data.data)
    
        // Update pagination state
        this.currentPage = data.current_page + 1
        this.perPage = data.per_page
        this.lastPage = data.last_page
        this.totalPages = Math.ceil(data.total / data.per_page)
    
        // Check if more pages are available
        this.hasMore = this.currentPage <= this.lastPage
      } catch (error) {
        console.error('Error fetching roles:', error)
      } finally {
        this.isLoading = false
      }
    },
    async fetchUser(id: [number, string]) {
      try {
        const response = await http.get<UserResource>(`roles/${id}`)
        this.role = response.data.data
      } catch (error) {
        console.error('Error fetching role:', error)
      }
    },

    async createRole(roleData: Omit<UserResource, 'id'>) {
      try {
        const response = await http.post<UserResource>('roles', roleData)
        this.roles.push(response.data)
      } catch (error) {
        console.error('Error creating role:', error)
      }
    },

    async updateUser(id: number, roleData: Partial<UserResource>) {
      const response = await http.put<UserResource>(`roles/${id}`, roleData)
      this.role = response.data.data
      this.roles = this.roles.map(role => role.id === id ? response.data : role)
    },

    async deleteUser(id: number) {
      try {
        await http.delete(`roles/${id}`)
        this.roles = this.roles.filter(role => role.id !== id)
      } catch (error) {
        console.error('Error deleting role:', error)
      }
    },
    async disableTwoFactor({ id }: { id?: number } = {}) {
      const response = await http.put<UserResource>(`roles/${id}/disable-two-factor`)

      return { response, resource: response.data.data }
    },
    resetUser() {
      this.role = {}
    },
  },
})