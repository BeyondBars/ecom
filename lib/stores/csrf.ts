// store/csrfStore.ts
import { create } from "zustand"

export interface CsrfSlice {
  csrf: boolean
  setCsrf: (value: boolean) => void
}

// ✅ Function that returns the actual store hook
export function createCsrfStore() {
  return create<CsrfSlice>((set) => ({
    csrf: false,
    setCsrf: (value: boolean) => set({ csrf: value }),
  }))
}
