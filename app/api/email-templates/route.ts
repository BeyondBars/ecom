import { type NextRequest, NextResponse } from "next/server"
import { emailTemplates } from "@/lib/data/email-templates"

export async function GET(request: NextRequest) {
  try {
    // In a real app, you would fetch data from a database
    return NextResponse.json(emailTemplates)
  } catch (error) {
    return NextResponse.json({ error: "Failed to fetch email templates" }, { status: 500 })
  }
}

export async function POST(request: NextRequest) {
  try {
    const data = await request.json()

    // Validate required fields
    if (!data.name || !data.subject || !data.body || !data.type || !data.triggeredBy) {
      return NextResponse.json({ error: "Missing required fields" }, { status: 400 })
    }

    // In a real app, you would save to a database
    const newTemplate = {
      id: `temp-${Date.now()}`,
      ...data,
      isActive: data.isActive !== undefined ? data.isActive : true,
    }

    return NextResponse.json(newTemplate, { status: 201 })
  } catch (error) {
    return NextResponse.json({ error: "Failed to create email template" }, { status: 500 })
  }
}
