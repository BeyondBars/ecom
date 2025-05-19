"use client"

import { useState } from "react"
import { format } from "date-fns"
import { Download, Printer, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import type { Invoice } from "@/lib/data/invoices"
import { cn } from "@/lib/utils"

interface InvoicePreviewProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  invoice: Invoice
}

export function InvoicePreview({ open, onOpenChange, invoice }: InvoicePreviewProps) {
  const [isPrinting, setIsPrinting] = useState(false)

  const handlePrint = () => {
    setIsPrinting(true)
    setTimeout(() => {
      window.print()
      setIsPrinting(false)
    }, 100)
  }

  const handleDownload = () => {
    // In a real app, this would generate a PDF
    alert("In a production app, this would download the invoice as a PDF")
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case "paid":
        return "bg-green-100 text-green-800"
      case "pending":
        return "bg-yellow-100 text-yellow-800"
      case "overdue":
        return "bg-red-100 text-red-800"
      case "draft":
        return "bg-gray-100 text-gray-800"
      default:
        return "bg-gray-100 text-gray-800"
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto print:shadow-none print:p-0 print:max-h-none print:max-w-none">
        <div className="print:hidden">
          <DialogHeader className="flex flex-row items-center justify-between">
            <DialogTitle>Invoice Preview</DialogTitle>
            <div className="flex items-center gap-2">
              <Button variant="outline" size="icon" onClick={handlePrint} disabled={isPrinting}>
                <Printer className="h-4 w-4" />
                <span className="sr-only">Print invoice</span>
              </Button>
              <Button variant="outline" size="icon" onClick={handleDownload}>
                <Download className="h-4 w-4" />
                <span className="sr-only">Download invoice</span>
              </Button>
              <Button variant="outline" size="icon" onClick={() => onOpenChange(false)}>
                <X className="h-4 w-4" />
                <span className="sr-only">Close</span>
              </Button>
            </div>
          </DialogHeader>
        </div>

        <div className="p-6 bg-white" id="invoice-print">
          <div className="flex justify-between items-start">
            <div>
              <h1 className="text-2xl font-bold">INVOICE</h1>
              <p className="text-sm text-gray-500">#{invoice.invoiceNumber}</p>
            </div>
            <div className="text-right">
              <div className="text-xl font-bold">Your Company Name</div>
              <div className="text-sm text-gray-500">
                123 Business Street
                <br />
                City, State 12345
                <br />
                contact@yourcompany.com
                <br />
                (123) 456-7890
              </div>
            </div>
          </div>

          <div className="mt-8 flex justify-between">
            <div>
              <h2 className="text-sm font-semibold text-gray-600 uppercase">Bill To:</h2>
              <div className="mt-1">
                <div className="font-medium">{invoice.customerName}</div>
                <div className="text-sm text-gray-500">{invoice.customerEmail}</div>
                <div className="text-sm text-gray-500 max-w-xs whitespace-pre-line">{invoice.customerAddress}</div>
              </div>
            </div>
            <div>
              <div className="text-sm grid grid-cols-2 gap-x-4 gap-y-2">
                <span className="font-semibold text-gray-600">Invoice Date:</span>
                <span>{format(new Date(invoice.date), "MMM dd, yyyy")}</span>
                <span className="font-semibold text-gray-600">Due Date:</span>
                <span>{format(new Date(invoice.dueDate), "MMM dd, yyyy")}</span>
                <span className="font-semibold text-gray-600">Status:</span>
                <span
                  className={cn(
                    "px-2 py-0.5 rounded-full text-xs font-medium capitalize",
                    getStatusColor(invoice.status),
                  )}
                >
                  {invoice.status}
                </span>
              </div>
            </div>
          </div>

          <div className="mt-8">
            <table className="w-full">
              <thead>
                <tr className="border-b border-gray-200">
                  <th className="py-2 text-left font-semibold text-gray-600">Description</th>
                  <th className="py-2 text-right font-semibold text-gray-600">Quantity</th>
                  <th className="py-2 text-right font-semibold text-gray-600">Unit Price</th>
                  <th className="py-2 text-right font-semibold text-gray-600">Total</th>
                </tr>
              </thead>
              <tbody>
                {invoice.items.map((item, index) => (
                  <tr key={item.id || index} className="border-b border-gray-100">
                    <td className="py-3">{item.description}</td>
                    <td className="py-3 text-right">{item.quantity}</td>
                    <td className="py-3 text-right">${item.unitPrice.toFixed(2)}</td>
                    <td className="py-3 text-right">${item.total.toFixed(2)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <div className="mt-6 flex justify-end">
            <div className="w-64">
              <div className="flex justify-between py-1">
                <span className="text-gray-600">Subtotal:</span>
                <span>${invoice.subtotal.toFixed(2)}</span>
              </div>
              <div className="flex justify-between py-1">
                <span className="text-gray-600">Tax (10%):</span>
                <span>${invoice.tax.toFixed(2)}</span>
              </div>
              <div className="flex justify-between py-1 border-t border-gray-200 font-semibold">
                <span>Total:</span>
                <span>${invoice.total.toFixed(2)}</span>
              </div>
            </div>
          </div>

          {invoice.notes && (
            <div className="mt-8">
              <h2 className="text-sm font-semibold text-gray-600 uppercase">Notes:</h2>
              <p className="mt-1 text-sm text-gray-500">{invoice.notes}</p>
            </div>
          )}

          <div className="mt-8 text-center text-sm text-gray-500">
            <p>Thank you for your business!</p>
            <p className="mt-1">Payment is due by {format(new Date(invoice.dueDate), "MMMM dd, yyyy")}</p>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  )
}
