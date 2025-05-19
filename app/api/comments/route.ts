import { NextResponse } from "next/server"

// Mock data
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
  {
    id: "2",
    blogPostId: "1",
    blogPostTitle: "Getting Started with Modern E-Commerce",
    content: "Could you provide more details about payment gateways?",
    author: {
      id: "6",
      name: "Sarah Johnson",
      email: "sarah.j@example.com",
    },
    status: "approved",
    createdAt: "2023-02-17T14:20:00.000Z",
    updatedAt: "2023-02-17T15:00:00.000Z",
  },
  {
    id: "3",
    blogPostId: "2",
    blogPostTitle: "10 Tips for Increasing Your Online Sales",
    content: "I implemented tip #3 and saw a 15% increase in conversions!",
    author: {
      id: "7",
      name: "David Wilson",
      email: "david.w@example.com",
    },
    status: "approved",
    createdAt: "2023-02-21T11:45:00.000Z",
    updatedAt: "2023-02-21T12:30:00.000Z",
  },
  {
    id: "4",
    blogPostId: "3",
    blogPostTitle: "The Future of E-Commerce: Trends to Watch",
    content: "I think AI will have an even bigger impact than you mentioned.",
    author: {
      id: "8",
      name: "Lisa Chen",
      email: "lisa.c@example.com",
    },
    status: "pending",
    createdAt: "2023-03-06T16:10:00.000Z",
    updatedAt: "2023-03-06T16:10:00.000Z",
  },
  {
    id: "5",
    blogPostId: "3",
    blogPostTitle: "The Future of E-Commerce: Trends to Watch",
    content: "This article contains misleading information about blockchain.",
    author: {
      id: "9",
      name: "Anonymous User",
      email: "anon@example.com",
    },
    status: "rejected",
    createdAt: "2023-03-07T08:55:00.000Z",
    updatedAt: "2023-03-07T10:20:00.000Z",
  },
  {
    id: "6",
    blogPostId: "5",
    blogPostTitle: "Building Customer Loyalty in E-Commerce",
    content: "The loyalty program suggestions are spot on!",
    author: {
      id: "10",
      name: "Mark Thompson",
      email: "mark.t@example.com",
    },
    status: "approved",
    createdAt: "2023-03-16T13:25:00.000Z",
    updatedAt: "2023-03-16T14:00:00.000Z",
  },
]

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url)
    const page = Number.parseInt(searchParams.get("page") || "1")
    const limit = Number.parseInt(searchParams.get("limit") || "10")
    const status = searchParams.get("status") || ""
    const search = searchParams.get("search") || ""
    const blogPostId = searchParams.get("blogPostId") || ""

    // Filter comments based on search term, status, and blog post ID
    let filteredComments = comments

    if (status) {
      filteredComments = filteredComments.filter((comment) => comment.status === status)
    }

    if (blogPostId) {
      filteredComments = filteredComments.filter((comment) => comment.blogPostId === blogPostId)
    }

    if (search) {
      filteredComments = filteredComments.filter(
        (comment) =>
          comment.content.toLowerCase().includes(search.toLowerCase()) ||
          comment.author.name.toLowerCase().includes(search.toLowerCase()) ||
          comment.author.email.toLowerCase().includes(search.toLowerCase()) ||
          comment.blogPostTitle.toLowerCase().includes(search.toLowerCase()),
      )
    }

    // Calculate pagination
    const startIndex = (page - 1) * limit
    const endIndex = page * limit
    const paginatedComments = filteredComments.slice(startIndex, endIndex)

    // Return paginated response
    return NextResponse.json({
      success: true,
      data: paginatedComments,
      meta: {
        total: filteredComments.length,
        page,
        limit,
        totalPages: Math.ceil(filteredComments.length / limit),
      },
      message: "Comments retrieved successfully",
    })
  } catch (error) {
    console.error("Error fetching comments:", error)
    return NextResponse.json({ success: false, message: "Failed to fetch comments" }, { status: 500 })
  }
}

export async function POST(request: Request) {
  try {
    const comment = await request.json()

    // Validate required fields
    if (!comment.blogPostId || !comment.content || !comment.author) {
      return NextResponse.json(
        { success: false, message: "Blog post ID, content, and author are required" },
        { status: 400 },
      )
    }

    // In a real app, you would save to a database
    // For demo, just return the comment with an ID
    const newComment = {
      id: (comments.length + 1).toString(),
      ...comment,
      status: "pending", // New comments are pending by default
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    }

    return NextResponse.json({
      success: true,
      data: newComment,
      message: "Comment created successfully",
    })
  } catch (error) {
    console.error("Error creating comment:", error)
    return NextResponse.json({ success: false, message: "Failed to create comment" }, { status: 500 })
  }
}
