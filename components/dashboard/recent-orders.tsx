"use client"

import { formatCurrency, formatDate } from "@/lib/utils"
import { Badge } from "@/components/ui/badge"
import { ScrollArea } from "@/components/ui/scroll-area"

// Mock data for recent orders
const recentOrders = [
  {
    id: "1",
    orderNumber: "ORD-2023-0001",
    customer: "John Doe",
    date: "2023-03-15T10:30:00.000Z",
    status: "completed",
    total: 1104.99,
  },
  {
    id: "2",
    orderNumber: "ORD-2023-0002",
    customer: "Jane Smith",
    date: "2023-03-20T09:15:00.000Z",
    status: "processing",
    total: 2561.48,
  },
  {
    id: "3",
    orderNumber: "ORD-2023-0003",
    customer: "Robert Johnson",
    date: "2023-03-25T14:20:00.000Z",
    status: "shipped",
    total: 1320.48,
  },
  {
    id: "4",
    orderNumber: "ORD-2023-0004",
    customer: "Emily Davis",
    date: "2023-04-01T16:45:00.000Z",
    status: "pending",
    total: 995.99,
  },
  {
    id: "5",
    orderNumber: "ORD-2023-0005",
    customer: "Michael Wilson",
    date: "2023-04-05T11:10:00.000Z",
    status: "delivered",
    total: 1758.98,
  },
]

// Status badge variants
const statusVariants = {
  pending: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300",
  processing: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300",
  shipped: "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300",
  delivered: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300",
  completed: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300",
  cancelled: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300",
}

export function RecentOrders() {
  return (
    <ScrollArea className="h-[300px]">
      <div className="space-y-4">
        {recentOrders.map((order) => (
          <div key={order.id} className="flex items-center justify-between space-x-4">
            <div className="space-y-1">
              <p className="text-sm font-medium leading-none">{order.customer}</p>
              <p className="text-sm text-muted-foreground">{order.orderNumber}</p>
              <p className="text-xs text-muted-foreground">{formatDate(order.date)}</p>
            </div>
            <div className="flex items-center space-x-2">
              <Badge variant="outline" className={statusVariants[order.status as keyof typeof statusVariants]}>
                {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
              </Badge>
              <span className="font-medium">{formatCurrency(order.total)}</span>
            </div>
          </div>
        ))}
      </div>
    </ScrollArea>
  )
}
