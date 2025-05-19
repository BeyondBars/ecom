import { NextResponse } from "next/server"
import { invoices } from "@/lib/data/invoices"

export async function GET(request: Request, { params }: { params: { id: string } }) {
  const invoice = invoices.find((invoice) => invoice.id === params.id)

  if (!invoice) {
    return NextResponse.json({ error: "Invoice not found" }, { status: 404 })
  }

  return NextResponse.json(invoice)
}

export async function PUT(request: Request, { params }: { params: { id: string } }) {
  try {
    const updatedInvoice = await request.json()

    // In a real app, this would update the database
    // For now, we'll just return the updated invoice
    return NextResponse.json({
      ...updatedInvoice,
      id: params.id,
      updatedAt: new Date().toISOString(),
    })
  } catch (error) {
    return NextResponse.json({ error: "Failed to update invoice" }, { status: 500 })
  }
}

export async function DELETE(request: Request, { params }: { params: { id: string } }) {
  // In a real app, this would delete from the database
  // For now, we'll just return a success message
  return NextResponse.json({ success: true })
}
