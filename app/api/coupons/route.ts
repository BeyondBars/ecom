import { type NextRequest, NextResponse } from "next/server"
import { coupons } from "@/lib/data/coupons"

export async function GET(request: NextRequest) {
  try {
    // Get query parameters
    const searchParams = request.nextUrl.searchParams
    const code = searchParams.get("code")
    const status = searchParams.get("status")
    const discountType = searchParams.get("discountType")
    const page = Number.parseInt(searchParams.get("page") || "1")
    const limit = Number.parseInt(searchParams.get("limit") || "10")

    // Filter coupons based on query parameters
    let filteredCoupons = [...coupons]

    if (code) {
      filteredCoupons = filteredCoupons.filter((coupon) => coupon.code.toLowerCase().includes(code.toLowerCase()))
    }

    if (status && status !== "all") {
      filteredCoupons = filteredCoupons.filter((coupon) => coupon.status === status)
    }

    if (discountType && discountType !== "all") {
      filteredCoupons = filteredCoupons.filter((coupon) => coupon.discountType === discountType)
    }

    // Calculate pagination
    const startIndex = (page - 1) * limit
    const endIndex = page * limit
    const paginatedCoupons = filteredCoupons.slice(startIndex, endIndex)
    const totalPages = Math.ceil(filteredCoupons.length / limit)

    return NextResponse.json({
      coupons: paginatedCoupons,
      pagination: {
        total: filteredCoupons.length,
        page,
        limit,
        totalPages,
      },
    })
  } catch (error) {
    console.error("Error fetching coupons:", error)
    return NextResponse.json({ error: "Failed to fetch coupons" }, { status: 500 })
  }
}

export async function POST(request: NextRequest) {
  try {
    const body = await request.json()

    // Validate required fields
    if (!body.code || !body.discountType || body.discountValue === undefined) {
      return NextResponse.json({ error: "Missing required fields" }, { status: 400 })
    }

    // Create new coupon
    const newCoupon = {
      id: coupons.length + 1,
      code: body.code,
      discountType: body.discountType,
      discountValue: Number.parseFloat(body.discountValue),
      minimumOrderAmount: Number.parseFloat(body.minimumOrderAmount || 0),
      startDate: body.startDate || new Date().toISOString(),
      expiryDate: body.expiryDate || new Date(new Date().setMonth(new Date().getMonth() + 1)).toISOString(),
      usageLimit: body.usageLimit ? Number.parseInt(body.usageLimit) : null,
      usageCount: 0,
      status: body.status || "active",
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    }

    // In a real app, this would save to a database
    // For now, we'll just return the new coupon
    return NextResponse.json(newCoupon, { status: 201 })
  } catch (error) {
    console.error("Error creating coupon:", error)
    return NextResponse.json({ error: "Failed to create coupon" }, { status: 500 })
  }
}
