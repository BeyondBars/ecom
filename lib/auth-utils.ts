import apiClient, { API_ENDPOINTS, FRONTEND_API_ENDPOINTS, type User, type AuthResponse } from "./api-client"
export type UserRole = "admin" | "blogger" | "user"

// Cache for user roles to improve performance
const roleCache = new Map<string, { roles: UserRole[]; timestamp: number }>()
const CACHE_TTL = 60000 // 1 minute cache

interface DecodedToken {
  role: string
  [key: string]: any
}
export async function getUserRoles(userId?: string): Promise<UserRole[]> {
  if (!userId) return []

  // Check cache first
  const now = Date.now()
  const cached = roleCache.get(userId)
  if (cached && now - cached.timestamp < CACHE_TTL) {
    return cached.roles
  }

  try {
    const response = await apiClient.get(API_ENDPOINTS.USER_ROLES(userId))
    const roles = response.data.roles || []

    // Update cache
    roleCache.set(userId, { roles, timestamp: now })

    return roles
  } catch (error) {
    console.error("Error getting user roles:", error)
    return []
  }
}

export async function isAdmin(userId?: string): Promise<boolean> {
  if (!userId) return false
  const roles = await getUserRoles(userId)
  return roles.includes("admin")
}

export async function isBlogger(userId?: string): Promise<boolean> {
  if (!userId) return false
  const roles = await getUserRoles(userId)
  return roles.includes("blogger") || roles.includes("admin")
}

export async function assignRole(userId: string, role: UserRole): Promise<void> {
  try {
    await apiClient.post(API_ENDPOINTS.USER_ROLES(userId), { role, action: "assign" })

    // Invalidate cache
    roleCache.delete(userId)
  } catch (error) {
    console.error(`Error assigning role "${role}" to user ${userId}:`, error)
    throw error
  }
}

export async function removeRole(userId: string, role: UserRole): Promise<void> {
  try {
    await apiClient.post(API_ENDPOINTS.USER_ROLES(userId), { role, action: "remove" })

    // Invalidate cache
    roleCache.delete(userId)
  } catch (error) {
    console.error(`Error removing role "${role}" from user ${userId}:`, error)
    throw error
  }
}

// Auth helper functions
export function getAuthToken(): string | null {
  if (typeof window === "undefined") return null
  return localStorage.getItem("auth_token")
}

export function setAuthToken(token: string): void {
  if (typeof window === "undefined") return
  localStorage.setItem("auth_token", token)
}

export function removeAuthToken(): void {
  if (typeof window === "undefined") return
  localStorage.removeItem("auth_token")
  localStorage.removeItem("user_data")
}

export function getCurrentUser(): User | null {
  if (typeof window === "undefined") return null
  const userData = localStorage.getItem("user_data")
  if (!userData || userData === "undefined") return null
  
  return userData ? JSON.parse(userData) : null
}

export function setCurrentUser(user: User): void {
  if (typeof window === "undefined") return
  localStorage.setItem("user_data", JSON.stringify(user))
}

export async function login(email: string, password: string): Promise<AuthResponse> {
  try {
    const response = await apiClient.post(API_ENDPOINTS.LOGIN, { email, password })
    const authData: AuthResponse = response.data
    console.log("Login response:", authData)
    setCurrentUser(authData.data)

    return authData
  } catch (error) {
    console.error("Login error:", error)
    throw error
  }
}

export async function register( email: string, password: string, confirmPassword: string, agreedToTerms: boolean, firstName?: string, lastName?: string,): Promise<AuthResponse> {
  try {
    const response = await apiClient.post(FRONTEND_API_ENDPOINTS.REGISTER, { email, password, password_confirmation: confirmPassword, first_name: firstName, last_name: lastName, agree_terms: agreedToTerms })
    const authData: AuthResponse = response.data

    setCurrentUser(authData.data)

    return authData
  } catch (error) {
    console.error("Register error:", error)
    throw error
  }
}

export async function logout(): Promise<void> {
  try {
    await apiClient.delete(API_ENDPOINTS.LOGOUT)
  } catch (error) {
    console.error("Logout error:", error)
  } finally {
    removeAuthToken()
  }
}

export async function verifyToken(): Promise<User | null> {
  try {
    const token = getAuthToken()
    if (!token) return null

    const response = await apiClient.get(API_ENDPOINTS.VERIFY_TOKEN)
    const user: User = response.data.user

    setCurrentUser(user)
    return user
  } catch (error) {
    console.error("Token verification error:", error)
    removeAuthToken()
    return null
  }
}

export const getLikedPosts = (): string[] => {
  if (typeof window === "undefined") return []

  const likedStr = localStorage.getItem("likedPosts")
  if (!likedStr) return []

  try {
    return JSON.parse(likedStr)
  } catch {
    return []
  }
}

export const toggleLike = (postId: string): boolean => {
  const liked = getLikedPosts()
  const isLiked = liked.includes(postId)

  if (isLiked) {
    const filtered = liked.filter((id) => id !== postId)
    localStorage.setItem("likedPosts", JSON.stringify(filtered))
    return false
  } else {
    liked.push(postId)
    localStorage.setItem("likedPosts", JSON.stringify(liked))
    return true
  }
}
