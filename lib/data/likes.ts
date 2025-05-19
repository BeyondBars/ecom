export interface LikeableItem {
  id: number
  name: string
  type: string
}

export interface LikeUser {
  id: number
  name: string
  email: string
}

export interface Like {
  id: number
  user_id: number
  likeable_id: number
  likeable_type: string
  created_at: string
  updated_at: string
  user: LikeUser
  likeable: LikeableItem
}

// Mock data for likes
export const LIKES_DATA: Like[] = [
  {
    id: 1,
    user_id: 1,
    likeable_id: 101,
    likeable_type: "App\\Models\\Product",
    created_at: "2023-01-15T08:30:00.000Z",
    updated_at: "2023-01-15T08:30:00.000Z",
    user: {
      id: 1,
      name: "John Doe",
      email: "john@example.com",
    },
    likeable: {
      id: 101,
      name: "Smartphone X",
      type: "product",
    },
  },
  {
    id: 2,
    user_id: 1,
    likeable_id: 201,
    likeable_type: "App\\Models\\BlogPost",
    created_at: "2023-02-20T10:15:00.000Z",
    updated_at: "2023-02-20T10:15:00.000Z",
    user: {
      id: 1,
      name: "John Doe",
      email: "john@example.com",
    },
    likeable: {
      id: 201,
      name: "Top 10 Tech Trends",
      type: "blog_post",
    },
  },
  {
    id: 3,
    user_id: 2,
    likeable_id: 102,
    likeable_type: "App\\Models\\Product",
    created_at: "2023-03-10T14:45:00.000Z",
    updated_at: "2023-03-10T14:45:00.000Z",
    user: {
      id: 2,
      name: "Jane Smith",
      email: "jane@example.com",
    },
    likeable: {
      id: 102,
      name: "Wireless Headphones",
      type: "product",
    },
  },
]

// Helper functions for API calls
export async function getLikes(params?: { user_id?: number; likeable_type?: string }) {
  // In a real app, this would be an API call
  return Promise.resolve({ data: [] })
}

export async function toggleProductLike(productId: number) {
  // In a real app, this would be an API call
  return Promise.resolve({ liked: true })
}

export async function toggleBlogPostLike(blogPostId: number) {
  // In a real app, this would be an API call
  return Promise.resolve({ liked: true })
}

export async function getProductLikesCount(productId: number) {
  // In a real app, this would be an API call
  return Promise.resolve({ count: 0 })
}

export async function getBlogPostLikesCount(blogPostId: number) {
  // In a real app, this would be an API call
  return Promise.resolve({ count: 0 })
}

export async function checkProductLike(productId: number) {
  // In a real app, this would be an API call
  return Promise.resolve({ liked: false })
}

export async function checkBlogPostLike(blogPostId: number) {
  // In a real app, this would be an API call
  return Promise.resolve({ liked: false })
}
