export interface Like {
  id: number
  user: {
    id: number
    name: string
    email: string
  }
  likeableType: "product" | "blog-post"
  likeableName: string
  likeableId: number
  createdAt: string
}

export async function getLikes() {
  // This would be an API call in a real application
  return [
    {
      id: 1,
      user: {
        id: 1,
        name: "John Doe",
        email: "john@example.com",
      },
      likeableType: "product",
      likeableName: "Wireless Headphones",
      likeableId: 101,
      createdAt: "2023-04-15T10:00:00Z",
    },
    {
      id: 2,
      user: {
        id: 2,
        name: "Jane Smith",
        email: "jane@example.com",
      },
      likeableType: "blog-post",
      likeableName: "Top 10 Tech Gadgets of 2023",
      likeableId: 201,
      createdAt: "2023-05-20T14:30:00Z",
    },
    {
      id: 3,
      user: {
        id: 3,
        name: "Bob Johnson",
        email: "bob@example.com",
      },
      likeableType: "product",
      likeableName: "Smart Watch Series 7",
      likeableId: 102,
      createdAt: "2023-06-10T09:15:00Z",
    },
    {
      id: 4,
      user: {
        id: 1,
        name: "John Doe",
        email: "john@example.com",
      },
      likeableType: "product",
      likeableName: "Bluetooth Speaker",
      likeableId: 103,
      createdAt: "2023-07-05T16:45:00Z",
    },
    {
      id: 5,
      user: {
        id: 4,
        name: "Alice Williams",
        email: "alice@example.com",
      },
      likeableType: "blog-post",
      likeableName: "How to Choose the Right Smartphone",
      likeableId: 202,
      createdAt: "2023-08-12T11:20:00Z",
    },
  ]
}

export async function getLikeById(id: number) {
  const likes = await getLikes()
  return likes.find((like) => like.id === id)
}

export async function getLikesByUser(userId: number) {
  const likes = await getLikes()
  return likes.filter((like) => like.user.id === userId)
}

export async function getLikesByType(type: "product" | "blog-post") {
  const likes = await getLikes()
  return likes.filter((like) => like.likeableType === type)
}
