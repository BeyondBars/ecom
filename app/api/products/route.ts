import { NextResponse } from "next/server"

// Mock data
const products = [
  {
    id: "1",
    name: "iPhone 13 Pro",
    description: "Apple's flagship smartphone with advanced camera system",
    price: 999.99,
    category: "Electronics",
    brand: "Apple",
    sku: "APIP13P-128",
    stock: 50,
    images: ["/modern-smartphone.png"],
    featured: true,
    createdAt: "2023-01-15T08:00:00.000Z",
    updatedAt: "2023-01-15T08:00:00.000Z",
  },
  {
    id: "2",
    name: "Samsung Galaxy S22",
    description: "Samsung's premium smartphone with high-end specifications",
    price: 899.99,
    category: "Electronics",
    brand: "Samsung",
    sku: "SAMS22-128",
    stock: 35,
    images: ["/samsung-products.png"],
    featured: true,
    createdAt: "2023-01-20T10:30:00.000Z",
    updatedAt: "2023-01-20T10:30:00.000Z",
  },
  {
    id: "3",
    name: 'MacBook Pro 14"',
    description: "Powerful laptop for professionals with M1 Pro chip",
    price: 1999.99,
    category: "Computers",
    brand: "Apple",
    sku: "APMB14-512",
    stock: 20,
    images: ["/placeholder-l7j90.png"],
    featured: true,
    createdAt: "2023-02-05T14:15:00.000Z",
    updatedAt: "2023-02-05T14:15:00.000Z",
  },
  {
    id: "4",
    name: "Sony WH-1000XM4",
    description: "Premium noise-cancelling headphones with excellent sound quality",
    price: 349.99,
    category: "Audio",
    brand: "Sony",
    sku: "SONWH1000XM4",
    stock: 45,
    images: ["/diverse-people-listening-headphones.png"],
    featured: false,
    createdAt: "2023-02-10T09:45:00.000Z",
    updatedAt: "2023-02-10T09:45:00.000Z",
  },
  {
    id: "5",
    name: "iPad Air",
    description: "Versatile tablet with powerful performance",
    price: 599.99,
    category: "Electronics",
    brand: "Apple",
    sku: "APIPA-64",
    stock: 30,
    images: ["/silver-ipad-on-wooden-desk.png"],
    featured: false,
    createdAt: "2023-02-15T11:20:00.000Z",
    updatedAt: "2023-02-15T11:20:00.000Z",
  },
]

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url)
    const page = Number.parseInt(searchParams.get("page") || "1")
    const limit = Number.parseInt(searchParams.get("limit") || "10")
    const search = searchParams.get("search") || ""

    // Filter products based on search term
    let filteredProducts = products
    if (search) {
      filteredProducts = products.filter(
        (product) =>
          product.name.toLowerCase().includes(search.toLowerCase()) ||
          product.description.toLowerCase().includes(search.toLowerCase()) ||
          product.brand.toLowerCase().includes(search.toLowerCase()) ||
          product.category.toLowerCase().includes(search.toLowerCase()),
      )
    }

    // Calculate pagination
    const startIndex = (page - 1) * limit
    const endIndex = page * limit
    const paginatedProducts = filteredProducts.slice(startIndex, endIndex)

    // Return paginated response
    return NextResponse.json({
      success: true,
      data: paginatedProducts,
      meta: {
        total: filteredProducts.length,
        page,
        limit,
        totalPages: Math.ceil(filteredProducts.length / limit),
      },
      message: "Products retrieved successfully",
    })
  } catch (error) {
    console.error("Error fetching products:", error)
    return NextResponse.json({ success: false, message: "Failed to fetch products" }, { status: 500 })
  }
}

export async function POST(request: Request) {
  try {
    const product = await request.json()

    // Validate required fields
    if (!product.name || !product.price) {
      return NextResponse.json({ success: false, message: "Name and price are required" }, { status: 400 })
    }

    // In a real app, you would save to a database
    // For demo, just return the product with an ID
    const newProduct = {
      id: (products.length + 1).toString(),
      ...product,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    }

    return NextResponse.json({
      success: true,
      data: newProduct,
      message: "Product created successfully",
    })
  } catch (error) {
    console.error("Error creating product:", error)
    return NextResponse.json({ success: false, message: "Failed to create product" }, { status: 500 })
  }
}
