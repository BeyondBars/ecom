import { type NextRequest, NextResponse } from "next/server"
import { coupons } from "@/lib/data/coupons"

export async function POST(request: NextRequest) {
  try {
    const body = await request.json()
    const { code, orderAmount } = body

    if (!code || orderAmount === undefined) {
      return NextResponse.json({ error: "Missing required fields" }, { status: 400 })
    }

    const coupon = coupons.find(
      (c) =>
        c.code.toLowerCase() === code.toLowerCase() &&
        c.status === "active" &&
        new Date(c.startDate) <= new Date() &&
        new Date(c.expiryDate) >= new Date(),
    )

    if (!coupon) {
      return NextResponse.json({
        valid: false,
        message: "Invalid or expired coupon code.",
      })
    }

    if (coupon.minimumOrderAmount > orderAmount) {
      return NextResponse.json({
        valid: false,
        message: `Minimum order amount of $${coupon.minimumOrderAmount.toFixed(2)} required.`,
        minimumOrderAmount: coupon.minimumOrderAmount,
      })
    }

    if (coupon.usageLimit !== null && coupon.usageCount >= coupon.usageLimit) {
      return NextResponse.json({
        valid: false,
        message: "Coupon usage limit reached.",
      })
    }

    return NextResponse.json({
      valid: true,
      coupon: {
        id: coupon.id,
        code: coupon.code,
        discountType: coupon.discountType,
        discountValue: coupon.discountValue,
      },
    })
  } catch (error) {
    console.error("Error validating coupon:", error)
    return NextResponse.json({ error: "Failed to validate coupon" }, { status: 500 })
  }
}
