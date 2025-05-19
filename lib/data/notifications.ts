export interface Notification {
  id: number
  userId: number
  title: string
  message: string
  type: "info" | "success" | "warning" | "error"
  icon?: string
  link?: string
  isRead: boolean
  readAt?: string
  createdAt: string
  updatedAt: string
}

export const notifications: Notification[] = [
  {
    id: 1,
    userId: 1,
    title: "New Order Received",
    message: "You have received a new order #1234 for $129.99",
    type: "info",
    icon: "shopping-cart",
    link: "/orders/1234",
    isRead: false,
    createdAt: "2023-05-18T10:30:00Z",
    updatedAt: "2023-05-18T10:30:00Z",
  },
  {
    id: 2,
    userId: 1,
    title: "Payment Successful",
    message: "Payment for order #1234 has been successfully processed",
    type: "success",
    icon: "credit-card",
    link: "/orders/1234",
    isRead: false,
    createdAt: "2023-05-18T10:35:00Z",
    updatedAt: "2023-05-18T10:35:00Z",
  },
  {
    id: 3,
    userId: 1,
    title: "Low Stock Alert",
    message: 'Product "Wireless Headphones" is running low on stock (5 remaining)',
    type: "warning",
    icon: "alert-triangle",
    link: "/products/12",
    isRead: true,
    readAt: "2023-05-18T11:20:00Z",
    createdAt: "2023-05-18T11:00:00Z",
    updatedAt: "2023-05-18T11:20:00Z",
  },
  {
    id: 4,
    userId: 1,
    title: "Order Shipment Delayed",
    message: "Shipment for order #1230 has been delayed due to weather conditions",
    type: "error",
    icon: "truck",
    link: "/orders/1230",
    isRead: true,
    readAt: "2023-05-18T14:10:00Z",
    createdAt: "2023-05-18T13:45:00Z",
    updatedAt: "2023-05-18T14:10:00Z",
  },
  {
    id: 5,
    userId: 1,
    title: "New Comment on Blog Post",
    message: 'User John Doe commented on your blog post "Top 10 Tech Trends"',
    type: "info",
    icon: "message-square",
    link: "/blog/15/comments",
    isRead: false,
    createdAt: "2023-05-19T09:15:00Z",
    updatedAt: "2023-05-19T09:15:00Z",
  },
  {
    id: 6,
    userId: 1,
    title: "Product Review Submitted",
    message: 'A new 5-star review has been submitted for "Smartphone X"',
    type: "success",
    icon: "star",
    link: "/products/8/reviews",
    isRead: false,
    createdAt: "2023-05-19T11:30:00Z",
    updatedAt: "2023-05-19T11:30:00Z",
  },
  {
    id: 7,
    userId: 1,
    title: "System Maintenance",
    message: "The system will be down for maintenance on May 25th from 2:00 AM to 4:00 AM UTC",
    type: "warning",
    icon: "tool",
    isRead: false,
    createdAt: "2023-05-20T08:00:00Z",
    updatedAt: "2023-05-20T08:00:00Z",
  },
  {
    id: 8,
    userId: 1,
    title: "Failed Login Attempt",
    message: "There was a failed login attempt to your account from an unknown IP address",
    type: "error",
    icon: "shield-off",
    isRead: false,
    createdAt: "2023-05-20T15:45:00Z",
    updatedAt: "2023-05-20T15:45:00Z",
  },
  {
    id: 9,
    userId: 1,
    title: "New Feature Available",
    message: "Check out our new analytics dashboard with improved reporting features",
    type: "info",
    icon: "bar-chart-2",
    link: "/dashboard/analytics",
    isRead: true,
    readAt: "2023-05-21T10:15:00Z",
    createdAt: "2023-05-21T09:30:00Z",
    updatedAt: "2023-05-21T10:15:00Z",
  },
  {
    id: 10,
    userId: 1,
    title: "Discount Code Created",
    message: 'You have created a new discount code "SUMMER25" for 25% off',
    type: "success",
    icon: "tag",
    link: "/marketing/discounts",
    isRead: true,
    readAt: "2023-05-21T14:20:00Z",
    createdAt: "2023-05-21T14:00:00Z",
    updatedAt: "2023-05-21T14:20:00Z",
  },
]

export async function getNotifications(params: {
  page?: number
  perPage?: number
  type?: string
  isRead?: boolean
}) {
  const { page = 1, perPage = 10, type, isRead } = params

  // Filter notifications based on parameters
  let filteredNotifications = [...notifications]

  if (type) {
    filteredNotifications = filteredNotifications.filter((notification) => notification.type === type)
  }

  if (isRead !== undefined) {
    filteredNotifications = filteredNotifications.filter((notification) => notification.isRead === isRead)
  }

  // Sort by created date, newest first
  filteredNotifications.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())

  // Paginate
  const start = (page - 1) * perPage
  const end = start + perPage
  const paginatedNotifications = filteredNotifications.slice(start, end)

  return {
    data: paginatedNotifications,
    meta: {
      total: filteredNotifications.length,
      page,
      perPage,
      lastPage: Math.ceil(filteredNotifications.length / perPage),
    },
  }
}

export async function getNotification(id: number) {
  const notification = notifications.find((n) => n.id === id)

  if (!notification) {
    throw new Error(`Notification with ID ${id} not found`)
  }

  return notification
}

export async function markAsRead(id: number) {
  const notification = notifications.find((n) => n.id === id)

  if (!notification) {
    throw new Error(`Notification with ID ${id} not found`)
  }

  notification.isRead = true
  notification.readAt = new Date().toISOString()
  notification.updatedAt = new Date().toISOString()

  return notification
}

export async function markAsUnread(id: number) {
  const notification = notifications.find((n) => n.id === id)

  if (!notification) {
    throw new Error(`Notification with ID ${id} not found`)
  }

  notification.isRead = false
  notification.readAt = undefined
  notification.updatedAt = new Date().toISOString()

  return notification
}

export async function markAllAsRead() {
  notifications.forEach((notification) => {
    notification.isRead = true
    notification.readAt = new Date().toISOString()
    notification.updatedAt = new Date().toISOString()
  })

  return { success: true }
}

export async function getUnreadCount() {
  const count = notifications.filter((n) => !n.isRead).length

  return { count }
}

export async function deleteNotification(id: number) {
  const index = notifications.findIndex((n) => n.id === id)

  if (index === -1) {
    throw new Error(`Notification with ID ${id} not found`)
  }

  notifications.splice(index, 1)

  return { success: true }
}
