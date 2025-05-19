import { NextResponse } from "next/server"

// Mock data
const users = [
  {
    id: "1",
    name: "Admin User",
    email: "admin@example.com",
    role: "admin",
    isActive: true,
    createdAt: "2023-01-01T00:00:00.000Z",
    updatedAt: "2023-01-01T00:00:00.000Z",
  },
  {
    id: "2",
    name: "Manager User",
    email: "manager@example.com",
    role: "manager",
    isActive: true,
    createdAt: "2023-01-02T00:00:00.000Z",
    updatedAt: "2023-01-02T00:00:00.000Z",
  },
  {
    id: "3",
    name: "Editor User",
    email: "editor@example.com",
    role: "editor",
    isActive: true,
    createdAt: "2023-01-03T00:00:00.000Z",
    updatedAt: "2023-01-03T00:00:00.000Z",
  },
  {
    id: "4",
    name: "Customer Support",
    email: "support@example.com",
    role: "support",
    isActive: true,
    createdAt: "2023-01-04T00:00:00.000Z",
    updatedAt: "2023-01-04T00:00:00.000Z",
  },
  {
    id: "5",
    name: "Regular User",
    email: "user@example.com",
    role: "user",
    isActive: false,
    createdAt: "2023-01-05T00:00:00.000Z",
    updatedAt: "2023-01-05T00:00:00.000Z",
  },
]

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url)
    const page = Number.parseInt(searchParams.get("page") || "1")
    const limit = Number.parseInt(searchParams.get("limit") || "10")
    const role = searchParams.get("role") || ""
    const search = searchParams.get("search") || ""
    const status = searchParams.get("status") || ""

    // Filter users based on search term, role, and status
    let filteredUsers = users

    if (role) {
      filteredUsers = filteredUsers.filter((user) => user.role === role)
    }

    if (status === "active") {
      filteredUsers = filteredUsers.filter((user) => user.isActive === true)
    } else if (status === "inactive") {
      filteredUsers = filteredUsers.filter((user) => user.isActive === false)
    }

    if (search) {
      filteredUsers = filteredUsers.filter(
        (user) =>
          user.name.toLowerCase().includes(search.toLowerCase()) ||
          user.email.toLowerCase().includes(search.toLowerCase()) ||
          user.role.toLowerCase().includes(search.toLowerCase()),
      )
    }

    // Calculate pagination
    const startIndex = (page - 1) * limit
    const endIndex = page * limit
    const paginatedUsers = filteredUsers.slice(startIndex, endIndex)

    // Return paginated response
    return NextResponse.json({
      success: true,
      data: paginatedUsers,
      meta: {
        total: filteredUsers.length,
        page,
        limit,
        totalPages: Math.ceil(filteredUsers.length / limit),
      },
      message: "Users retrieved successfully",
    })
  } catch (error) {
    console.error("Error fetching users:", error)
    return NextResponse.json({ success: false, message: "Failed to fetch users" }, { status: 500 })
  }
}

export async function POST(request: Request) {
  try {
    const user = await request.json()

    // Validate required fields
    if (!user.name || !user.email || !user.role) {
      return NextResponse.json({ success: false, message: "Name, email, and role are required" }, { status: 400 })
    }

    // Check if email already exists
    const emailExists = users.some((existingUser) => existingUser.email === user.email)

    if (emailExists) {
      return NextResponse.json({ success: false, message: "Email already exists" }, { status: 400 })
    }

    // In a real app, you would save to a database
    // For demo, just return the user with an ID
    const newUser = {
      id: (users.length + 1).toString(),
      ...user,
      isActive: user.isActive ?? true,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    }

    return NextResponse.json({
      success: true,
      data: newUser,
      message: "User created successfully",
    })
  } catch (error) {
    console.error("Error creating user:", error)
    return NextResponse.json({ success: false, message: "Failed to create user" }, { status: 500 })
  }
}
