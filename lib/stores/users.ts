import { PureAbility } from '@casl/ability'
import apiClient, { AuthResponse, FRONTEND_API_ENDPOINTS } from '@/lib/api-client'
import { create } from "zustand"

export type Role = {
  name: string
}

export type User = {
  id?: number | string
  name?: string
  email?: string
  role?: Role
  [key: string]: any
}

type TwoFactorSecretPayload = {
  provisioning_uri: string
  secret: string
}

export interface AuthSlice {
  user: User | {}
  attempted: boolean
  twoFactorProvisioningUri: string | null
  twoFactorSecret: string | null
  retailerLocations: any[]
  retailerLocation: any | null
  portalSwitcherOptions: any[]
  ability: PureAbility
  can: (ability: any) => boolean
  cannot: (ability: any) => boolean
  canAny: (...abilities: any[]) => boolean
  isA: (role: string) => boolean
  isNotA: (role: string) => boolean
  setUser: (user: User) => void
  setAttempted: (value: boolean) => void
  login: (payload: any) => Promise<any>
  register: (payload: any) => Promise<any>
  authorize: () => Promise<any>
  logout: () => Promise<void>
  forgotPassword: (payload: any) => Promise<any>
  resetPassword: (payload: any) => Promise<any>
  updateTwoFactor: ({ id, payload }: { id?: string; payload: any }) => Promise<any>
  emailVerification: (payload: any) => Promise<void>
  sendTwoFactorEmailCode: () => Promise<any>
  sendTwoFactorSmsCode: () => Promise<any>
  verifyTwoFactorCode: ({ code, method, secret }: any) => Promise<any>
  invalidateTwoFactorAppToken: () => Promise<void>
  getTwoFactorSecret: () => Promise<void>
  getUser: () => Promise<void>
  resetUser: () => void
}

export function createAuthStore() {
  return create<AuthSlice>((set, get) => ({
    user: {},
    attempted: false,
    twoFactorProvisioningUri: null,
    twoFactorSecret: null,
    retailerLocations: [],
    retailerLocation: null,
    portalSwitcherOptions: [],

    ability: new PureAbility([]),

    can: (ability) => get().ability.can(ability),
    cannot: (ability) => !get().ability.can(ability),
    canAny: (...abilities) => abilities.some((a) => get().can(a)),
    isA: (role) => get().user?.role?.name === role,
    isNotA: (role) => get().user?.role?.name !== role,

    setUser: (user) => set({ user }),
    setAttempted: (value) => set({ attempted: value }),

    login: async (payload) => {
      console.log("Logging in with payload:", payload)
      get().setAttempted(false)
      return apiClient.post("/auth", payload)
    },
    register: async ({ email, password, confirmPassword, agreedToTerms, firstName, lastName }) => {
      try {
        await apiClient.post(FRONTEND_API_ENDPOINTS.REGISTER, { email, password, password_confirmation: confirmPassword, first_name: firstName, last_name: lastName, agree_terms: agreedToTerms })
        get().setAttempted(false)
      } catch (error) {
        console.error("Register error:", error)
        throw error
      }
    },
    authorize: async () => {
      get().setAttempted(true)
      const response = await apiClient.get("/user", { authorizePreflight: true })
      get().setUser(response.data.data)
      return response
    },

    logout: async () => {
      get().setAttempted(false)
      await apiClient.delete("/auth")
      set({ user: {} })
    },

    forgotPassword: async (payload) => {
      return apiClient.post("/password/forgot", payload)
    },

    resetPassword: async (payload) => {
      return apiClient.post("/password/reset", payload)
    },

    updateTwoFactor: async ({ id, payload }) => {
      const userId = id || get().user?.id
      const response = await apiClient.put(`/users/${userId}/two-factor`, payload)
      get().setUser(response.data.data)
      return response.data.data
    },
    emailVerification: async (payload) => {
      await apiClient.post("/auth/verification", payload)
    },
    sendTwoFactorEmailCode: async () => apiClient.post("/auth/two-factor/email"),
    sendTwoFactorSmsCode: async () => apiClient.post("/auth/two-factor/sms"),
    verifyTwoFactorCode: async ({ code, method, secret }) =>
      apiClient.post("/auth/two-factor/verify", { code, method, secret }),

    invalidateTwoFactorAppToken: async () => {
      await apiClient.put("/auth/two-factor/invalidate")
      const user = get().user
      get().setUser({
        ...user,
        two_factor_method_enabled: null,
        two_factor_secret: null,
      })
    },

    getTwoFactorSecret: async () => {
      const response = await apiClient.get("/auth/two-factor/secret")
      const payload: TwoFactorSecretPayload = response.data.data
      set({
        twoFactorProvisioningUri: payload.provisioning_uri,
        twoFactorSecret: payload.secret,
      })
    },
    getUser: async () => {
      const response = await apiClient.get("/auth/two-factor/secret", { userPreflight: true })
      set({
        user: response.data.data,
      })
    },
    resetUser: () => set({ user: {} }),
  }))
}
