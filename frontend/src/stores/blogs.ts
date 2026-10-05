import { defineStore } from 'pinia'
import { http } from '@/utils/http'
import type { Blog } from '@/types/blog'
import type { Paginated } from '@/types/user'

export type BlogFilters = {
  /** Page to load; page 1 replaces the list, later pages are appended. */
  page?: number
  search?: string
  status?: string
}

export const useBlogStore = defineStore('blogs', {
  state: () => ({
    blogs: [] as Blog[],
    currentBlog: null as Blog | null,
    currentPage: 1,
    totalPages: null as number | null,
    perPage: null as number | null,
    lastPage: null as number | null,
    isLoading: false,
    hasMore: true,
    error: null as string | null,
  }),

  actions: {
    async fetchBlogs(filters: BlogFilters = {}) {
      const { search, status } = filters
      const page = filters.page ?? this.currentPage
      const firstPage = page === 1

      // Prevent unnecessary API calls
      if (this.isLoading || (!firstPage && !this.hasMore)) return

      try {
        this.isLoading = true
        this.error = null
        const { data } = await http.get<Paginated<Blog>>('blogs', {
          params: { page, search: search || undefined, status: status || undefined },
        })

        // A first page (new search / filter) replaces the list; later pages are appended.
        if (firstPage) {
          this.blogs = data.data
        } else {
          this.blogs.push(...data.data)
        }

        // Update pagination state
        this.currentPage = data.meta.current_page + 1
        this.perPage = data.meta.per_page
        this.lastPage = data.meta.last_page
        this.totalPages = Math.ceil(data.meta.total / data.meta.per_page)

        // Check if more pages are available
        this.hasMore = data.meta.current_page < data.meta.last_page
      } catch (e) {
        this.error = 'Failed to fetch blogs'
        console.error(e)
      } finally {
        this.isLoading = false
      }
    },

    async fetchBlog(id: number) {
      try {
        this.isLoading = true
        this.error = null
        const response = await http.get(`blogs/${id}`)
        this.currentBlog = response.data.data
        return response.data.data
      } catch (e) {
        console.error('API error:', e)
        this.error = 'Failed to fetch blog'
        throw e
      } finally {
        this.isLoading = false
      }
    },

    async createBlog(blogData: Partial<Blog>) {
      try {
        this.isLoading = true
        this.error = null
        const response = await http.post('blogs', blogData)
        this.blogs.unshift(response.data.data)
        return response.data.data
      } catch (e) {
        this.error = 'Failed to create blog'
        console.error(e)
        throw e
      } finally {
        this.isLoading = false
      }
    },

    async updateBlog(id: number, blogData: Partial<Blog>) {
      try {
        this.isLoading = true
        this.error = null
        const response = await http.put(`blogs/${id}`, blogData)
        const index = this.blogs.findIndex(blog => blog.id === id)
        if (index !== -1) {
          this.blogs[index] = response.data.data
        }
        if (this.currentBlog?.id === id) {
          this.currentBlog = response.data.data
        }
        return response.data.data
      } catch (e) {
        this.error = 'Failed to update blog'
        console.error(e)
        throw e
      } finally {
        this.isLoading = false
      }
    },

    async deleteBlog(id: number) {
      try {
        this.isLoading = true
        this.error = null
        await http.delete(`blogs/${id}`)
        this.blogs = this.blogs.filter(blog => blog.id !== id)
        if (this.currentBlog?.id === id) {
          this.currentBlog = null
        }
      } catch (e) {
        this.error = 'Failed to delete blog'
        console.error(e)
        throw e
      } finally {
        this.isLoading = false
      }
    },

    resetBlog() {
      this.currentBlog = null
    }
  }
}) 