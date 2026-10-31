/** Shapes of the shop API resources (api/app/Http/Resources). */

export type ProductStatus = 'active' | 'draft' | 'archived'

export type Category = {
  id: number
  parent_id: number | null
  name: string
  slug: string
  description: string | null
  image_url: string | null
  is_active: boolean
  sort_order: number
  products_count?: number
  parent?: Category | null
}

export type Brand = {
  id: number
  name: string
  slug: string
  description: string | null
  website: string | null
  logo_url: string | null
  is_active: boolean
  products_count?: number
}

export type ProductOption = { name: string; values: string[] }
export type ProductSpecification = { name: string; value: string }
export type ProductImage = { id?: number; url: string; alt: string | null }

export type Product = {
  id: number
  name: string
  slug: string
  sku: string
  description: string | null
  price: number
  sale_price: number | null
  current_price: number
  cost: number | null
  stock: number
  in_stock: boolean
  status: ProductStatus
  is_featured: boolean
  options: ProductOption[]
  specifications: ProductSpecification[]
  rating: number
  review_count: number
  images: ProductImage[]
  category?: Category
  brand?: Brand | null
  created_at: string
  updated_at: string
}

export type OrderStatus = 'pending' | 'processing' | 'shipped' | 'delivered' | 'cancelled' | 'returned'
export type PaymentStatus = 'pending' | 'paid' | 'failed' | 'refund_pending' | 'refunded'
export type ReturnStatus = 'requested' | 'approved' | 'rejected' | 'completed'

export type ShippingAddress = {
  first_name: string
  last_name: string
  phone: string
  line1: string
  line2?: string | null
  city: string
  state: string
  postal_code: string
  country: string
}

export type OrderItem = {
  id: number
  product_id: number | null
  name: string
  sku: string | null
  image_url: string | null
  options: Record<string, string>
  price: number
  quantity: number
  total: number
}

export type OrderEvent = {
  type: string
  note: string | null
  is_internal: boolean
  by: string | null
  created_at: string
}

export type Order = {
  id: number
  number: string
  email: string
  status: OrderStatus
  payment_method: 'razorpay' | 'cod'
  payment_status: PaymentStatus
  payment_reference: string | null
  paid_at: string | null
  shipping_method: 'standard' | 'express'
  shipping_address: ShippingAddress
  coupon_code: string | null
  subtotal: number
  discount: number
  shipping: number
  total: number
  currency: string
  courier: string | null
  tracking_number: string | null
  shipped_at: string | null
  delivered_at: string | null
  cancelled_at: string | null
  cancel_reason: string | null
  return_status: ReturnStatus | null
  return_reason: string | null
  return_requested_at: string | null
  customer_note: string | null
  admin_note?: string | null
  items_count?: number
  items?: OrderItem[]
  events?: OrderEvent[]
  customer?: { id: number; name: string; email: string; mobile: string | null } | null
  next_statuses?: OrderStatus[]
  created_at: string
}

export type CouponType = 'percent' | 'fixed' | 'free_shipping'

export type Coupon = {
  id: number
  code: string
  description: string | null
  type: CouponType
  value: number
  min_subtotal: number
  max_discount: number | null
  starts_at: string | null
  expires_at: string | null
  usage_limit: number | null
  usage_limit_per_user: number | null
  used_count: number
  is_active: boolean
}

export type ModerationStatus = 'pending' | 'approved' | 'rejected'

export type Review = {
  id: number
  product_id: number
  rating: number
  title: string | null
  comment: string
  status: ModerationStatus
  verified_purchase: boolean
  is_featured: boolean
  author: string
  author_email?: string
  product?: { id: number; name: string; slug: string }
  created_at: string
}

export type Banner = {
  id: number
  title: string
  subtitle: string | null
  image_url: string
  link_url: string | null
  button_text: string | null
  placement: 'home_hero' | 'home_promo' | 'category_top'
  sort_order: number
  is_active: boolean
  starts_at: string | null
  ends_at: string | null
}

export type BlogPost = {
  id: number
  title: string
  slug: string
  excerpt: string | null
  content: string
  cover_image_url: string | null
  status: 'draft' | 'published'
  published_at: string | null
  author: string | null
  comments_count?: number
  created_at: string
}

export type BlogComment = {
  id: number
  content: string
  status: ModerationStatus
  author: string
  author_email?: string
  post?: { id: number; title: string; slug: string }
  created_at: string
}

export type ContactMessage = {
  id: number
  name: string
  email: string
  phone: string | null
  subject: string
  message: string
  status: 'new' | 'read' | 'replied' | 'archived'
  created_at: string
}

export type ShopSettings = {
  store_name: string
  support_email: string
  support_phone: string | null
  store_address: string | null
  announcement: string | null
  free_shipping_threshold: number
  standard_shipping_fee: number
  express_shipping_fee: number
  cod_enabled: boolean
  cod_max_order_total: number | null
  return_window_days: number
  instagram_url: string | null
  facebook_url: string | null
  youtube_url: string | null
  twitter_url: string | null
}

export type DashboardStats = {
  period_days: number
  revenue: number
  revenue_previous: number
  orders: number
  orders_previous: number
  average_order_value: number
  new_customers: number
  series: { date: string; revenue: number; orders: number }[]
  top_products: { product_id: number | null; name: string; units: number; revenue: number }[]
  to_do: { to_ship: number; returns: number; refunds: number; reviews: number; low_stock: number }
  recent_orders: Order[]
}
