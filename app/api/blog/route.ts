import { NextResponse } from "next/server"

// Mock data
const blogPosts = [
  {
    id: "1",
    title: "Getting Started with Modern E-Commerce",
    slug: "getting-started-with-modern-e-commerce",
    content:
      "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl.",
    excerpt: "A beginner's guide to setting up your e-commerce store",
    featuredImage: "/ecommerce-concept.png",
    author: {
      id: "1",
      name: "Admin User",
    },
    status: "published",
    isPublished: true,
    publishedAt: "2023-02-15T10:00:00.000Z",
    createdAt: "2023-02-10T08:30:00.000Z",
    updatedAt: "2023-02-15T10:00:00.000Z",
  },
  {
    id: "2",
    title: "10 Tips for Increasing Your Online Sales",
    slug: "10-tips-for-increasing-your-online-sales",
    content:
      "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl.",
    excerpt: "Proven strategies to boost your e-commerce conversion rates",
    featuredImage: "/sales-team-meeting.png",
    author: {
      id: "3",
      name: "Editor User",
    },
    status: "published",
    isPublished: true,
    publishedAt: "2023-02-20T14:30:00.000Z",
    createdAt: "2023-02-18T09:45:00.000Z",
    updatedAt: "2023-02-20T14:30:00.000Z",
  },
  {
    id: "3",
    title: "The Future of E-Commerce: Trends to Watch",
    slug: "the-future-of-e-commerce-trends-to-watch",
    content:
      "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl.",
    excerpt: "Emerging technologies and strategies shaping online retail",
    featuredImage: "/abstract-future.png",
    author: {
      id: "1",
      name: "Admin User",
    },
    status: "published",
    isPublished: true,
    publishedAt: "2023-03-05T11:15:00.000Z",
    createdAt: "2023-03-01T16:20:00.000Z",
    updatedAt: "2023-03-05T11:15:00.000Z",
  },
  {
    id: "4",
    title: "Optimizing Your Product Pages for Conversion",
    slug: "optimizing-your-product-pages-for-conversion",
    content:
      "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl.",
    excerpt: "Best practices for creating high-converting product pages",
    featuredImage: "/diverse-products-still-life.png",
    author: {
      id: "3",
      name: "Editor User",
    },
    status: "draft",
    isPublished: false,
    publishedAt: null,
    createdAt: "2023-03-10T13:40:00.000Z",
    updatedAt: "2023-03-10T13:40:00.000Z",
  },
  {
    id: "5",
    title: "Building Customer Loyalty in E-Commerce",
    slug: "building-customer-loyalty-in-e-commerce",
    content:
      "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet  Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl. Sed euismod, nisl vel ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl sit amet nisl.",
    excerpt: "Strategies for retaining customers and building brand loyalty",
    featuredImage: "/intertwined-hands-heart.png",
    author: {
      id: "2",
      name: "Manager User",
    },
    status: "published",
    isPublished: true,
    publishedAt: "2023-03-15T09:00:00.000Z",
    createdAt: "2023-03-12T15:30:00.000Z",
    updatedAt: "2023-03-15T09:00:00.000Z",
  },
]

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url)
    const page = Number.parseInt(searchParams.get("page") || "1")
    const limit = Number.parseInt(searchParams.get("limit") || "10")
    const status = searchParams.get("status") || ""
    const search = searchParams.get("search") || ""

    // Filter blog posts based on search term and status
    let filteredPosts = blogPosts

    if (status) {
      filteredPosts = filteredPosts.filter((post) => post.status === status)
    }

    if (search) {
      filteredPosts = filteredPosts.filter(
        (post) =>
          post.title.toLowerCase().includes(search.toLowerCase()) ||
          post.excerpt.toLowerCase().includes(search.toLowerCase()) ||
          post.content.toLowerCase().includes(search.toLowerCase()),
      )
    }

    // Calculate pagination
    const startIndex = (page - 1) * limit
    const endIndex = page * limit
    const paginatedPosts = filteredPosts.slice(startIndex, endIndex)

    // Return paginated response
    return NextResponse.json({
      success: true,
      data: paginatedPosts,
      meta: {
        total: filteredPosts.length,
        page,
        limit,
        totalPages: Math.ceil(filteredPosts.length / limit),
      },
      message: "Blog posts retrieved successfully",
    })
  } catch (error) {
    console.error("Error fetching blog posts:", error)
    return NextResponse.json({ success: false, message: "Failed to fetch blog posts" }, { status: 500 })
  }
}

export async function POST(request: Request) {
  try {
    const post = await request.json()

    // Validate required fields
    if (!post.title || !post.content || !post.excerpt) {
      return NextResponse.json({ success: false, message: "Title, content, and excerpt are required" }, { status: 400 })
    }

    // In a real app, you would save to a database
    // For demo, just return the post with an ID
    const newPost = {
      id: (blogPosts.length + 1).toString(),
      ...post,
      author: {
        id: "1",
        name: "Admin User",
      },
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
      publishedAt: post.isPublished ? new Date().toISOString() : null,
    }

    return NextResponse.json({
      success: true,
      data: newPost,
      message: "Blog post created successfully",
    })
  } catch (error) {
    console.error("Error creating blog post:", error)
    return NextResponse.json({ success: false, message: "Failed to create blog post" }, { status: 500 })
  }
}
