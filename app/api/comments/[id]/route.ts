import { NextResponse } from "next/server"

// Mock data - this would normally be fetched from a database
const comments = [
  {
    id: "1",
    blogPostId: "1",
    blogPostTitle: "Getting Started with Modern E-Commerce",
    content: "This was really helpful for setting up my store. Thanks!",
    author: {
      id: "5",
      name: "Regular User",
      email: "user@example.com",
    },
    status: "approved",
    createdAt: "2023-02-16T09:30:00.000Z",
    updatedAt: "2023-02-16T10:15:00.000Z",
  },
  // ... other comments would be here
]

export async function GET(request: Request, { params }: { params: { id: string } }) {
  try {
    const id = params.id
    const comment = comments.find((c) => c.id === id)

    if (!comment) {
      return NextResponse.json({ success: false, message: "Comment not found" }, { status: 404 })
    }

    return NextResponse.json({
      success: true,
      data: comment,
      message: "Comment retrieved successfully",
    })
  } catch (error) {
    console.error("Error fetching comment:", error)
    return NextResponse.json({ success: false, message: "Failed to fetch comment" }, { status: 500 })
  }
}

export async function PATCH(request: Request, { params }: { params: { id: string } }) {
  try {
    const id = params.id
    const updates = await request.json()

    // In a real app, you would update the comment in the database
    // For demo, just return a success response

    return NextResponse.json({
      success: true,
      data: {
        id,
        ...updates,
        updatedAt: new Date().toISOString(),
      },
      message: "Comment updated successfully",
    })
  } catch (error) {
    console.error("Error updating comment:", error)
    return NextResponse.json({ success: false, message: "Failed to update comment" }, { status: 500 })
  }
}

export async function DELETE(request: Request, { params }: { params: { id: string } }) {
  try {
    const id = params.id

    // In a real app, you would delete the comment from the database
    // For demo, just return a success response

    return NextResponse.json({
      success: true,
      message: "Comment deleted successfully",
    })
  } catch (error) {
    console.error("Error deleting comment:", error)
    return NextResponse.json({ success: false, message: "Failed to delete comment" }, { status: 500 })
  }
}
