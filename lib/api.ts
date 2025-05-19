// This file contains mock API functions for demonstration purposes
// In a real application, these would make actual API calls to your backend

// Generic types for API responses
export interface ApiResponse<T> {
  data: T
  message: string
  success: boolean
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    total: number
    currentPage: number
    lastPage: number
    perPage: number
  }
}

// Helper function to simulate API calls
async function mockApiCall<T>(data: T, delay = 1000, shouldFail = false): Promise<ApiResponse<T>> {
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      if (shouldFail) {
        reject(new Error("API call failed"))
      } else {
        resolve({
          data,
          message: "Operation successful",
          success: true,
        })
      }
    }, delay)
  })
}

// Helper function to simulate paginated API calls
async function mockPaginatedApiCall<T>(data: T[], page = 1, perPage = 10, delay = 1000): Promise<PaginatedResponse<T>> {
  return new Promise((resolve) => {
    setTimeout(() => {
      const total = data.length
      const lastPage = Math.ceil(total / perPage)
      const start = (page - 1) * perPage
      const end = start + perPage
      const paginatedData = data.slice(start, end)

      resolve({
        data: paginatedData,
        meta: {
          total,
          currentPage: page,
          lastPage,
          perPage,
        },
      })
    }, delay)
  })
}

// Export the mock API functions
export const api = {
  mockApiCall,
  mockPaginatedApiCall,
}
