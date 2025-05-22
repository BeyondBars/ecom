import { type NextRequest, NextResponse } from "next/server"
import { emailTemplates } from "@/lib/data/email-templates"

export async function GET(request: NextRequest, { params }: { params: { id: string } }) {
  try {
    const id = params.id

    // In a real app, you would fetch from a database
    const template = emailTemplates.find((template) => template.id === id)

    if (!template) {
      return NextResponse.json({ error: "Email template not found" }, { status: 404 })
    }

    return NextResponse.json(template)
  } catch (error) {
    return NextResponse.json({ error: "Failed to fetch email template" }, { status: 500 })
  }
}

export async function PUT(request: NextRequest, { params }: { params: { id: string } }) {
  try {
    const id = params.id
    const data = await request.json()

    // Validate required fields
    if (!data.name || !data.subject || !data.body || !data.type || !data.triggeredBy) {
      return NextResponse.json({ error: "Missing required fields" }, { status: 400 })
    }

    // In a real app, you would update in a database
    const templateIndex = emailTemplates.findIndex((template) => template.id === id)

    if (templateIndex === -1) {
      return NextResponse.json({ error: "Email template not found" }, { status: 404 })
    }

    const updatedTemplate = {
      ...emailTemplates[templateIndex],
      ...data,
      id,
    }

    return NextResponse.json(updatedTemplate)
  } catch (error) {
    return NextResponse.json({ error: "Failed to update email template" }, { status: 500 })
  }
}

export async function DELETE(request: NextRequest, { params }: { params: { id: string } }) {
  try {
    const id = params.id

    // In a real app, you would delete from a database
    const templateIndex = emailTemplates.findIndex((template) => template.id === id)

    if (templateIndex === -1) {
      return NextResponse.json({ error: "Email template not found" }, { status: 404 })
    }

    return NextResponse.json({ success: true })
  } catch (error) {
    return NextResponse.json({ error: "Failed to delete email template" }, { status: 500 })
  }
}
