"use client"
import axios, { InternalAxiosRequestConfig } from "axios"
import { useCsrfStore } from "@/lib/stores/use-csrf-store"

axios.defaults.baseURL = process.env.NEXT_PUBLIC_API_BASE_URL || "http://localhost:8000"
axios.defaults.withCredentials = true
axios.defaults.headers['Accept'] = 'application/json'

// Create axios instance with base configuration
const apiClient = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_BASE_URL || "http://localhost:8000",
  withCredentials: true
})
type CustomRequestConfig = InternalAxiosRequestConfig & {
  csrfPreflight?: boolean
}
// const { csrf } = useCsrfStore()
// Request interceptor to add auth token
apiClient.interceptors.request.use(
  async (config: InternalAxiosRequestConfig) => {
    const customConfig = config as CustomRequestConfig
    const method = customConfig.method?.toLowerCase() ?? ""

    if (customConfig.data === undefined && ["post", "put"].includes(method)) {
      customConfig.data = {}
    }
    // Attach XSRF-TOKEN header if available
    if (typeof document !== "undefined") {
      const xsrfToken = document.cookie
        .split("; ")
        .find((row) => row.startsWith("XSRF-TOKEN="))
        ?.split("=")[1];

      if (xsrfToken) {
        customConfig.headers["X-XSRF-TOKEN"] = decodeURIComponent(xsrfToken)
      }
    }

    if (
      ["post", "put", "delete"].includes(method) &&
      useCsrfStore.getState().csrf === false &&
      customConfig.csrfPreflight === undefined
    ) {
      await apiClient.get("sanctum/csrf-cookie")

      useCsrfStore.setState({ csrf: true })
    }

    return customConfig
  },
  (error) => Promise.reject(error)
)

// Response interceptor to handle errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access
      if (typeof window !== "undefined") {
        localStorage.removeItem("auth_token")
        localStorage.removeItem("user_data")
        window.location.href = "/login"
      }
    }
    return Promise.reject(error)
  },
)

export default apiClient

// API endpoints
export const API_ENDPOINTS = {
  // Auth endpoints
  LOGIN: "/auth",
  REGISTER: "/auth/register",
  LOGOUT: "/auth",
  FORGOT_PASSWORD: "/auth/forgot-password",
  RESET_PASSWORD: "/auth/reset-password",
  VERIFY_TOKEN: "/auth/verify-token",
  SETTINGS: "/auth/verify-token",
  VERIFICATION_RESEND: "/auth/verification/resend",

  // Blog endpoints
  BLOG_POSTS: "/blogs",
  BLOG_POST_BY_SLUG: (slug: string) => `/blog/posts/${slug}`,
  BLOG_POST_BY_ID: (id: string) => `/blog/posts/id/${id}`,
  BLOG_POST_BY_CATEGORY: (id: string) => `/blog/posts/category/${id}`,

  // Categories endpoints
  CATEGORIES: "/blog/categories",
  CATEGORY_BY_ID: (id: string) => `/blog/categories/${id}`,
  CATEGORY_BY_SLUG: (slug: string) => `/blog/categories/${slug}`,

  // Tags endpoints
  TAGS: "/blog/tags",
  TAG_BY_ID: (id: string) => `/blog/tags/${id}`,
  TAG_BY_SLUG: (slug: string) => `/blog/tags/${slug}`,

  // Comments endpoints
  COMMENTS: "/blog/comments",
  COMMENTS_BY_POST: (postId: string) => `/blog/comments/post/${postId}`,
  MODERATE_COMMENT: "/blog/comments/moderate",

  // User management endpoints
  USERS: "/users",
  USER_ROLES: (userId: string) => `/users/${userId}/roles`,

  // Contact endpoint
  CONTACT: "/contact",

  // Likes and shares
  LIKES: "/blog/likes",
  SHARES: "/blog/shares",
}

export const FRONTEND_API_ENDPOINTS = {
  // Auth endpoints
  REGISTER: "/frontend/register",

  // Blog endpoints
  BLOG_POSTS: "/frontend/blogs",
  BLOG_POST_BY_SLUG: (slug: string) => `/frontend/blogs/${slug}`,
  BLOG_POST_BY_ID: (id: string) => `/frontend/blogs/${id}`,
  BLOG_POST_BY_CATEGORY: (id: string) => `/frontend/category/${id}/blogs`,

  // Comments endpoints
  COMMENTS_BY_POST: (blogId: string | number) => `/frontend/blogs/${blogId}/comments`
}

// Type definitions
export interface BlogPost {
  id: string
  title: string
  slug: string
  content: string
  excerpt: string
  featured_image: string
  author: string
  date: string
  read_time: string
  tags: string[]
  category: string
  featured: boolean
  published: boolean
  category_id?: string
}

export interface Category {
  id: string
  name: string
  slug: string
  description?: string
}

export interface Tag {
  id: string
  name: string
  slug: string
  description?: string
}

export interface Comment {
  id: string
  post_id: string
  user_name: string
  user_email: string
  content: string
  parent_id?: string
  is_approved: boolean
  created_at: string
}

export interface User {
  id: string
  email: string
  name?: string
  created_at: string
  last_sign_in_at?: string
  roles: string[]
}

export interface AuthResponse {
  data: User
}
