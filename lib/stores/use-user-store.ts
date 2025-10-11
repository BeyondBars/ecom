import { createAuthStore } from "@/lib/stores/users"
import type { User } from "@/lib/stores/users"

// This is the usable store hook
export const useUserStore = createAuthStore<User>()
