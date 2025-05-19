import { NextResponse } from "next/server"
import { invoices } from "@/lib/data/invoices"

export async function GET(request: Request, { params }: { params: { id: string } }) {
  const invoice = invoices.find((invoice) => invoice.id === params.id)

  if (!invoice) {
    return NextResponse.json({ error: "Invoice not found" }, { status: 404 })
  }

  // In a real app, this would generate a PDF
  // For now, we'll just return a success message
  return NextResponse.json({
    success: true,
    message: "Invoice PDF generated successfully",
    downloadUrl: `/api/invoices/${params.id}/download`,
  })
}
