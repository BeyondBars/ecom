import type React from "react"
import type { Metadata } from "next"
import { Inter } from "next/font/google"
import "./globals.css"
import { ThemeProvider } from "@/components/theme-provider"
import { Toaster } from "@/components/ui/toaster"
import { ColorSchemeProvider } from "@/contexts/color-scheme-context"

const inter = Inter({ subsets: ["latin"] })

export const metadata: Metadata = {
  title: "Modern eCommerce Admin",
  description: "A modern eCommerce admin panel",
    generator: 'v0.dev'
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  return (
    <html lang="en">
      <body className={inter.className}>
        <ThemeProvider attribute="class" defaultTheme="light" enableSystem>
          <ColorSchemeProvider>
            {children}
            <Toaster />
          </ColorSchemeProvider>
        </ThemeProvider>
      </body>
    </html>
  )
}
