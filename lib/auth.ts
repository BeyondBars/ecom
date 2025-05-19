// This is a mock implementation for demonstration purposes
// In a real application, you would use a proper authentication library like NextAuth.js

import { jwtDecode } from "jwt-decode"

export interface User {
  id: string
  name: string
  email: string
  role: string
}

export async function login(email: string, password: string): Promise<User> {
  // In a real app, this would make an API call to your backend
  return new Promise((resolve, reject) => {
    // Simulate API call
    setTimeout(() => {
      // For demo purposes, accept any email/password with basic validation
      if (!email || !password) {
        reject(new Error("Email and password are required"))
        return
      }

      if (password.length < 8) {
        reject(new Error("Password must be at least 8 characters"))
        return
      }

      // Create a mock user
      const user: User = {
        id: "1",
        name: "Admin User",
        email,
        role: "admin",
      }

      // Store in localStorage (in a real app, you'd store a JWT token)
      const token = generateMockToken(user)
      localStorage.setItem("auth_token", token)

      resolve(user)
    }, 1000) // Simulate network delay
  })
}

export async function register(name: string, email: string, password: string): Promise<User> {
  // In a real app, this would make an API call to your backend
  return new Promise((resolve, reject) => {
    // Simulate API call
    setTimeout(() => {
      // For demo purposes, accept any email/password with basic validation
      if (!email || !password || !name) {
        reject(new Error("Name, email and password are required"))
        return
      }

      if (password.length < 8) {
        reject(new Error("Password must be at least 8 characters"))
        return
      }

      // Create a mock user
      const user: User = {
        id: "2",
        name: name,
        email,
        role: "customer",
      }

      resolve(user)
    }, 1000) // Simulate network delay
  })
}

export async function forgotPassword(email: string): Promise<void> {
  // In a real app, this would make an API call to your backend
  return new Promise((resolve, reject) => {
    // Simulate API call
    setTimeout(() => {
      if (!email) {
        reject(new Error("Email is required"))
        return
      }

      resolve()
    }, 1000) // Simulate network delay
  })
}

export async function resetPassword(token: string, password: string): Promise<void> {
  // In a real app, this would make an API call to your backend
  return new Promise((resolve, reject) => {
    // Simulate API call
    setTimeout(() => {
      if (!token || !password) {
        reject(new Error("Token and password are required"))
        return
      }

      if (password.length < 8) {
        reject(new Error("Password must be at least 8 characters"))
        return
      }

      resolve()
    }, 1000) // Simulate network delay
  })
}

export async function logout(): Promise<void> {
  return new Promise((resolve) => {
    setTimeout(() => {
      localStorage.removeItem("auth_token")
      resolve()
    }, 500)
  })
}

export function getCurrentUser(): User | null {
  const token = localStorage.getItem("auth_token")
  if (!token) return null

  try {
    // In a real app, you would verify the token on the server
    const decoded = jwtDecode<User & { exp: number }>(token)

    // Check if token is expired
    const currentTime = Date.now() / 1000
    if (decoded.exp < currentTime) {
      localStorage.removeItem("auth_token")
      return null
    }

    return {
      id: decoded.id,
      name: decoded.name,
      email: decoded.email,
      role: decoded.role,
    }
  } catch (error) {
    localStorage.removeItem("auth_token")
    return null
  }
}

export function isAuthenticated(): boolean {
  return getCurrentUser() !== null
}

// Helper function to generate a mock JWT token
function generateMockToken(user: User): string {
  // This is NOT a secure way to generate tokens
  // In a real app, you would use a proper JWT library
  const header = btoa(JSON.stringify({ alg: "HS256", typ: "JWT" }))
  const payload = btoa(
    JSON.stringify({
      ...user,
      exp: Math.floor(Date.now() / 1000) + 60 * 60, // 1 hour expiration
    }),
  )
  const signature = btoa("fake-signature")

  return `${header}.${payload}.${signature}`
}
