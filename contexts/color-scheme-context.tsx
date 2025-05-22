"use client"

import type React from "react"

import { createContext, useContext, useEffect, useState } from "react"

type ColorScheme = "default" | "ocean" | "forest" | "sunset" | "midnight" | "lavender"

interface ColorSchemeContextType {
  colorScheme: ColorScheme
  setColorScheme: (scheme: ColorScheme) => void
}

const ColorSchemeContext = createContext<ColorSchemeContextType | undefined>(undefined)

export function ColorSchemeProvider({ children }: { children: React.ReactNode }) {
  const [colorScheme, setColorScheme] = useState<ColorScheme>("default")

  // Load saved color scheme on mount
  useEffect(() => {
    const savedScheme = localStorage.getItem("color-scheme") as ColorScheme | null
    if (savedScheme) {
      setColorScheme(savedScheme)
    }
  }, [])

  // Apply color scheme to document root
  useEffect(() => {
    const root = document.documentElement

    // Remove all previous scheme classes
    root.classList.remove(
      "scheme-default",
      "scheme-ocean",
      "scheme-forest",
      "scheme-sunset",
      "scheme-midnight",
      "scheme-lavender",
    )

    // Add the new scheme class
    root.classList.add(`scheme-${colorScheme}`)

    // Save to localStorage
    localStorage.setItem("color-scheme", colorScheme)

    // Optional: Save to backend
    fetch("/api/settings/color-scheme", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ colorScheme }),
    }).catch((error) => console.error("Failed to save color scheme:", error))
  }, [colorScheme])

  return <ColorSchemeContext.Provider value={{ colorScheme, setColorScheme }}>{children}</ColorSchemeContext.Provider>
}

export function useColorScheme() {
  const context = useContext(ColorSchemeContext)
  if (context === undefined) {
    throw new Error("useColorScheme must be used within a ColorSchemeProvider")
  }
  return context
}
