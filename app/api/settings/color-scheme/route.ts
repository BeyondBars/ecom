import { NextResponse } from "next/server"

export async function POST(request: Request) {
  try {
    const { colorScheme } = await request.json()

    // In a real application, you would save this to your database
    // For now, we'll just return a success response

    return NextResponse.json({ success: true, colorScheme })
  } catch (error) {
    return NextResponse.json({ error: "Failed to update color scheme" }, { status: 500 })
  }
}
