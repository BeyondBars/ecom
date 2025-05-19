import { NextResponse } from "next/server"

export async function POST(request: Request) {
  try {
    const { email, password } = await request.json()

    // Validate input
    if (!email || !password) {
      return NextResponse.json({ success: false, message: "Email and password are required" }, { status: 400 })
    }

    // In a real app, you would validate credentials against your database
    // This is a mock implementation for demonstration purposes
    if (email === "admin@example.com" && password === "password123") {
      // Create a mock token (in a real app, use a proper JWT library)
      const token = "mock-jwt-token"

      return NextResponse.json({
        success: true,
        data: {
          user: {
            id: "1",
            name: "Admin User",
            email: "admin@example.com",
            role: "admin",
          },
          token,
        },
        message: "Login successful",
      })
    }

    // Invalid credentials
    return NextResponse.json({ success: false, message: "Invalid email or password" }, { status: 401 })
  } catch (error) {
    console.error("Login error:", error)
    return NextResponse.json({ success: false, message: "An error occurred during login" }, { status: 500 })
  }
}
