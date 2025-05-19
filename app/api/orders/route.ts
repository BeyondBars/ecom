import { NextResponse } from "next/server"

// Mock data
const orders = [
  {
    id: "1",
    orderNumber: "ORD-2023-0001",
    customer: {
      id: "1",
      name: "John Doe",
      email: "john.doe@example.com",
    },
    items: [
      {
        id: "1",
        productId: "1",
        productName: "iPhone 13 Pro",
        quantity: 1,
        price: 999.99,
        total: 999.99,
      },
    ],
    status: "completed",
    paymentStatus: "paid",
    paymentMethod: "credit_card",
    shippingAddress: {
      street: "123 Main St",
      city: "New York",
      state: "NY",
      zipCode: "10001",
      country: "USA",
    },
    subtotal: 999.99,
    tax: 90.0,
    shippingCost: 15.0,
    total: 1104.99,
    createdAt: "2023-03-15T10:30:00.000Z",
    updatedAt: "2023-03-15T14:45:00.000Z",
  },
  {
    id: "2",
    orderNumber: "ORD-2023-0002",
    customer: {
      id: "2",
      name: "Jane Smith",
      email: "jane.smith@example.com",
    },
    items: [
      {
        id: "2",
        productId: "3",
        productName: 'MacBook Pro 14"',
        quantity: 1,
        price: 1999.99,
        total: 1999.99,
      },
      {
        id: "3",
        productId: "4",
        productName: "Sony WH-1000XM4",
        quantity: 1,
        price: 349.99,
        total: 349.99,
      },
    ],
    status: "processing",
    paymentStatus: "paid",
    paymentMethod: "paypal",
    shippingAddress: {
      street: "456 Oak Ave",
      city: "Los Angeles",
      state: "CA",
      zipCode: "90001",
      country: "USA",
    },
    subtotal: 2349.98,
    tax: 211.5,
    shippingCost: 0.0,
    total: 2561.48,
    createdAt: "2023-03-20T09:15:00.000Z",
    updatedAt: "2023-03-20T09:15:00.000Z",
  },
  {
    id: "3",
    orderNumber: "ORD-2023-0003",
    customer: {
      id: "3",
      name: "Robert Johnson",
      email: "robert.johnson@example.com",
    },
    items: [
      {
        id: "4",
        productId: "5",
        productName: "iPad Air",
        quantity: 2,
        price: 599.99,
        total: 1199.98,
      },
    ],
    status: "shipped",
    paymentStatus: "paid",
    paymentMethod: "credit_card",
    shippingAddress: {
      street: "789 Pine St",
      city: "Chicago",
      state: "IL",
      zipCode: "60007",
      country: "USA",
    },
    subtotal: 1199.98,
    tax: 108.0,
    shippingCost: 12.5,
    total: 1320.48,
    createdAt: "2023-03-25T14:20:00.000Z",
    updatedAt: "2023-03-26T11:30:00.000Z",
  },
  {
    id: "4",
    orderNumber: "ORD-2023-0004",
    customer: {
      id: "4",
      name: "Emily Davis",
      email: "emily.davis@example.com",
    },
    items: [
      {
        id: "5",
        productId: "2",
        productName: "Samsung Galaxy S22",
        quantity: 1,
        price: 899.99,
        total: 899.99,
      },
    ],
    status: "pending",
    paymentStatus: "pending",
    paymentMethod: "bank_transfer",
    shippingAddress: {
      street: "321 Elm St",
      city: "Houston",
      state: "TX",
      zipCode: "77001",
      country: "USA",
    },
    subtotal: 899.99,
    tax: 81.0,
    shippingCost: 15.0,
    total: 995.99,
    createdAt: "2023-04-01T16:45:00.000Z",
    updatedAt: "2023-04-01T16:45:00.000Z",
  },
  {
    id: "5",
    orderNumber: "ORD-2023-0005",
    customer: {
      id: "5",
      name: "Michael Wilson",
      email: "michael.wilson@example.com",
    },
    items: [
      {
        id: "6",
        productId: "1",
        productName: "iPhone 13 Pro",
        quantity: 1,
        price: 999.99,
        total: 999.99,
      },
      {
        id: "7",
        productId: "5",
        productName: "iPad Air",
        quantity: 1,
        price: 599.99,
        total: 599.99,
      },
    ],
    status: "delivered",
    paymentStatus: "paid",
    paymentMethod: "credit_card",
    shippingAddress: {
      street: "555 Maple Ave",
      city: "Miami",
      state: "FL",
      zipCode: "33101",
      country: "USA",
    },
    subtotal: 1599.98,
    tax: 144.0,
    shippingCost: 15.0,
    total: 1758.98,
    createdAt: "2023-04-05T11:10:00.000Z",
    updatedAt: "2023-04-07T15:30:00.000Z",
  },
]

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url)
    const page = Number.parseInt(searchParams.get("page") || "1")
    const limit = Number.parseInt(searchParams.get("limit") || "10")
    const status = searchParams.get("status") || ""
    const search = searchParams.get("search") || ""

    // Filter orders based on search term and status
    let filteredOrders = orders

    if (status) {
      filteredOrders = filteredOrders.filter((order) => order.status === status)
    }

    if (search) {
      filteredOrders = filteredOrders.filter(
        (order) =>
          order.orderNumber.toLowerCase().includes(search.toLowerCase()) ||
          order.customer.name.toLowerCase().includes(search.toLowerCase()) ||
          order.customer.email.toLowerCase().includes(search.toLowerCase()),
      )
    }

    // Calculate pagination
    const startIndex = (page - 1) * limit
    const endIndex = page * limit
    const paginatedOrders = filteredOrders.slice(startIndex, endIndex)

    // Return paginated response
    return NextResponse.json({
      success: true,
      data: paginatedOrders,
      meta: {
        total: filteredOrders.length,
        page,
        limit,
        totalPages: Math.ceil(filteredOrders.length / limit),
      },
      message: "Orders retrieved successfully",
    })
  } catch (error) {
    console.error("Error fetching orders:", error)
    return NextResponse.json({ success: false, message: "Failed to fetch orders" }, { status: 500 })
  }
}

export async function POST(request: Request) {
  try {
    const order = await request.json()

    // Validate required fields
    if (!order.customer || !order.items || order.items.length === 0) {
      return NextResponse.json({ success: false, message: "Customer and items are required" }, { status: 400 })
    }

    // In a real app, you would save to a database
    // For demo, just return the order with an ID
    const newOrder = {
      id: (orders.length + 1).toString(),
      orderNumber: `ORD-2023-${(orders.length + 1).toString().padStart(4, "0")}`,
      ...order,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    }

    return NextResponse.json({
      success: true,
      data: newOrder,
      message: "Order created successfully",
    })
  } catch (error) {
    console.error("Error creating order:", error)
    return NextResponse.json({ success: false, message: "Failed to create order" }, { status: 500 })
  }
}
