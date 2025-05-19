export interface Wishlist {
  id: number
  name: string
  description?: string
  user: {
    id: number
    name: string
    email: string
  }
  itemsCount: number
  isPublic: boolean
  createdAt: string
}

export interface WishlistItem {
  id: number
  wishlistId: number
  productId: number
  product: {
    id: number
    name: string
    price: number
    image: string
  }
  notes?: string
  priority: number
  createdAt: string
}

export async function getWishlists() {
  // This would be an API call in a real application
  return [
    {
      id: 1,
      name: "Summer Wishlist",
      user: {
        id: 1,
        name: "John Doe",
        email: "john@example.com",
      },
      itemsCount: 5,
      isPublic: true,
      createdAt: "2023-04-15T10:00:00Z",
    },
    {
      id: 2,
      name: "Birthday Ideas",
      user: {
        id: 2,
        name: "Jane Smith",
        email: "jane@example.com",
      },
      itemsCount: 12,
      isPublic: false,
      createdAt: "2023-05-20T14:30:00Z",
    },
    {
      id: 3,
      name: "Tech Gadgets",
      user: {
        id: 3,
        name: "Bob Johnson",
        email: "bob@example.com",
      },
      itemsCount: 8,
      isPublic: true,
      createdAt: "2023-06-10T09:15:00Z",
    },
    {
      id: 4,
      name: "Home Decor",
      user: {
        id: 1,
        name: "John Doe",
        email: "john@example.com",
      },
      itemsCount: 15,
      isPublic: false,
      createdAt: "2023-07-05T16:45:00Z",
    },
    {
      id: 5,
      name: "Gift Ideas",
      user: {
        id: 4,
        name: "Alice Williams",
        email: "alice@example.com",
      },
      itemsCount: 7,
      isPublic: true,
      createdAt: "2023-08-12T11:20:00Z",
    },
  ]
}

export async function getWishlistById(id: number) {
  const wishlists = await getWishlists()
  return wishlists.find((wishlist) => wishlist.id === id)
}

export async function getWishlistItems(wishlistId: number) {
  // This would be an API call in a real application
  return [
    {
      id: 1,
      wishlistId: wishlistId,
      productId: 101,
      product: {
        id: 101,
        name: "Wireless Headphones",
        price: 99.99,
        image: "/placeholder.svg",
      },
      notes: "I want these in black color",
      priority: 8,
      createdAt: "2023-04-15T10:00:00Z",
    },
    {
      id: 2,
      wishlistId: wishlistId,
      productId: 102,
      product: {
        id: 102,
        name: "Smart Watch Series 7",
        price: 299.99,
        image: "/placeholder.svg",
      },
      notes: "The silver one looks nice",
      priority: 5,
      createdAt: "2023-04-16T11:30:00Z",
    },
    {
      id: 3,
      wishlistId: wishlistId,
      productId: 103,
      product: {
        id: 103,
        name: "Bluetooth Speaker",
        price: 79.99,
        image: "/placeholder.svg",
      },
      priority: 3,
      createdAt: "2023-04-17T14:45:00Z",
    },
  ]
}
