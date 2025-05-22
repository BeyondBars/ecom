import { type NextRequest, NextResponse } from "next/server"
import { coupons } from "@/lib/data/coupons"

export async function GET(request: NextRequest, { params }: { params: { id: string } }) {
  try {
    const id = Number.parseInt(params.id)
    const coupon = coupons.find((c) => c.id === id)

    if (!coupon) {
      return NextResponse.json({ error: "Coupon not found" }, { status: 404 })
    }

    return NextResponse.json(coupon)
  } catch (error) {
    console.error("Error fetching coupon:", error)
    return NextResponse.json({ error: "Failed to fetch coupon" }, { status: 500 })
  }
}

export async function PUT(request: NextRequest, { params }: { params: { id: string } }) {
  try {
    const id = Number.parseInt(params.id)
    const couponIndex = coupons.findIndex((c) => c.id === id)

    if (couponIndex === -1) {
      return NextResponse.json({ error: "Coupon not found" }, { status: 404 })
    }

    const body = await request.json()
    const updatedCoupon = {
      ...coupons[couponIndex],
      ...body,
      updatedAt: new Date().toISOString(),
    }

    // In a real app, this would update the database
    // For now, we'll just return the updated coupon
    return NextResponse.json(updatedCoupon)
  } catch (error) {
    console.error("Error updating coupon:", error)
    return NextResponse.json({ error: "Failed to update coupon" }, { status: 500 })
  }
}

export async function DELETE(request: NextRequest, { params }: { params: { id: string } }) {
  try {
    const id = Number.parseInt(params.id)
    const couponIndex = coupons.findIndex((c) => c.id === id)

    if (couponIndex === -1) {
      return NextResponse.json({ error: "Coupon not found" }, { status: 404 })
    }

    // In a real app, this would delete from the database
    // For now, we'll just return a success message
    return NextResponse.json({ message: "Coupon deleted successfully" })
  } catch (error) {
    console.error("Error deleting coupon:", error)
    return NextResponse.json({ error: "Failed to delete coupon" }, { status: 500 })
  }
}
