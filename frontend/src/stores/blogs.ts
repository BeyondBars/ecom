import { defineStore } from 'pinia'
import { http } from '@/utils/http'
import type { Blog } from '@/types/blog'

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
    async fetchBlogs({ currentPage } = this.currentPage) {
      // Prevent unnecessary API calls
      if (this.isLoading || !this.hasMore) return

      try {
        this.isLoading = true
        this.error = null
        const { data } = await http.get(`blogs?page=${currentPage || 1}`)

        // Append new blogs instead of replacing
        this.blogs.push(...data.data)

        // Update pagination state
        this.currentPage = data.current_page + 1
        this.perPage = data.per_page
        this.lastPage = data.last_page
        this.totalPages = Math.ceil(data.total / data.per_page)
    
        // // Check if more pages are available
        this.hasMore = this.currentPage <= this.lastPage
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
        console.log('Making API request to:', `blogs/${id}`)
        const response = await http.get(`blogs/${id}`)
        console.log('API response:', response)
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