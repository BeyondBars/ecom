import { type NextRequest, NextResponse } from "next/server"
import { emailTemplates } from "@/lib/data/email-templates"

export async function POST(request: NextRequest, { params }: { params: { id: string } }) {
  try {
    const id = params.id
    const { email } = await request.json()

    if (!email) {
      return NextResponse.json({ error: "Email address is required" }, { status: 400 })
    }

    // In a real app, you would fetch the template from a database
    const template = emailTemplates.find((template) => template.id === id)

    if (!template) {
      return NextResponse.json({ error: "Email template not found" }, { status: 404 })
    }

    // In a real app, you would send an actual email here
    // For now, we'll just simulate a successful send

    return NextResponse.json({
      success: true,
      message: `Test email sent to ${email} using template "${template.name}"`,
    })
  } catch (error) {
    return NextResponse.json({ error: "Failed to send test email" }, { status: 500 })
  }
}
