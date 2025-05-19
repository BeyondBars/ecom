import { NextResponse } from "next/server"
import { invoices } from "@/lib/data/invoices"

export async function GET() {
  return NextResponse.json(invoices)
}

export async function POST(request: Request) {
  try {
    const invoice = await request.json()

    // In a real app, this would save to a database
    // For now, we'll just return the invoice with an ID
    const newInvoice = {
      ...invoice,
      id: crypto.randomUUID(),
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    }

    return NextResponse.json(newInvoice, { status: 201 })
  } catch (error) {
    return NextResponse.json({ error: "Failed to create invoice" }, { status: 500 })
  }
}
