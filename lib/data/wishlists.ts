export interface WishlistUser {
  id: number
  name: string
  email: string
}

export interface Wishlist {
  id: number
  name: string
  description?: string
  is_public: boolean
  user_id: number
  created_at: string
  updated_at: string
  items_count: number
  user: WishlistUser
}

export interface WishlistItem {
  id: number
  wishlist_id: number
  product_id: number
  notes?: string
  priority: number
  created_at: string
  updated_at: string
  product: {
    id: number
    name: string
    price: number
    image?: string
  }
}

// Mock data for wishlist items
export const WISHLIST_ITEMS_DATA: WishlistItem[] = [
  {
    id: 1,
    wishlist_id: 1,
    product_id: 101,
    notes: "Want to buy this next month",
    priority: 5,
    created_at: "2023-01-15T08:30:00.000Z",
    updated_at: "2023-01-15T08:30:00.000Z",
    product: {
      id: 101,
      name: "Smartphone X",
      price: 999.99,
      image: "/modern-smartphone.png",
    },
  },
  {
    id: 2,
    wishlist_id: 1,
    product_id: 102,
    notes: "Birthday gift idea",
    priority: 3,
    created_at: "2023-01-16T10:45:00.000Z",
    updated_at: "2023-01-16T10:45:00.000Z",
    product: {
      id: 102,
      name: "Wireless Headphones",
      price: 199.99,
      image: "/diverse-people-listening-headphones.png",
    },
  },
  {
    id: 3,
    wishlist_id: 1,
    product_id: 103,
    priority: 1,
    created_at: "2023-01-17T14:20:00.000Z",
    updated_at: "2023-01-17T14:20:00.000Z",
    product: {
      id: 103,
      name: "Tablet Pro",
      price: 799.99,
      image: "/silver-ipad-on-wooden-desk.png",
    },
  },
]

// Helper functions for API calls
export async function getWishlists() {
  // In a real app, this would be an API call
  return Promise.resolve({ data: [] })
}

export async function getWishlist(id: number) {
  // In a real app, this would be an API call
  return Promise.resolve({ data: {} })
}

export async function createWishlist(data: Partial<Wishlist>) {
  // In a real app, this would be an API call
  return Promise.resolve({ data: {} })
}

export async function updateWishlist(id: number, data: Partial<Wishlist>) {
  // In a real app, this would be an API call
  return Promise.resolve({ data: {} })
}

export async function deleteWishlist(id: number) {
  // In a real app, this would be an API call
  return Promise.resolve({ success: true })
}

export async function getWishlistItems(wishlistId: number) {
  // In a real app, this would be an API call
  return Promise.resolve({ data: [] })
}

export async function addWishlistItem(
  wishlistId: number,
  productId: number,
  data: { notes?: string; priority?: number },
) {
  // In a real app, this would be an API call
  return Promise.resolve({ data: {} })
}

export async function updateWishlistItem(
  wishlistId: number,
  productId: number,
  data: { notes?: string; priority?: number },
) {
  // In a real app, this would be an API call
  return Promise.resolve({ data: {} })
}

export async function removeWishlistItem(wishlistId: number, productId: number) {
  // In a real app, this would be an API call
  return Promise.resolve({ success: true })
}
