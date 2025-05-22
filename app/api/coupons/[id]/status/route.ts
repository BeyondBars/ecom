import { type NextRequest, NextResponse } from "next/server"
import { coupons } from "@/lib/data/coupons"

export async function PUT(request: NextRequest, { params }: { params: { id: string } }) {
  try {
    const id = Number.parseInt(params.id)
    const couponIndex = coupons.findIndex((c) => c.id === id)

    if (couponIndex === -1) {
      return NextResponse.json({ error: "Coupon not found" }, { status: 404 })
    }

    const body = await request.json()

    if (!body.status || !["active", "inactive"].includes(body.status)) {
      return NextResponse.json({ error: "Invalid status" }, { status: 400 })
    }

    const updatedCoupon = {
      ...coupons[couponIndex],
      status: body.status,
      updatedAt: new Date().toISOString(),
    }

    // In a real app, this would update the database
    // For now, we'll just return the updated coupon
    return NextResponse.json(updatedCoupon)
  } catch (error) {
    console.error("Error updating coupon status:", error)
    return NextResponse.json({ error: "Failed to update coupon status" }, { status: 500 })
  }
}
