"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import {
  type ColumnDef,
  type ColumnFiltersState,
  type SortingState,
  type VisibilityState,
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
} from "@tanstack/react-table"
import { ArrowUpDown, ChevronDown, Download, Eye, MoreHorizontal, Pencil, Plus, Trash } from "lucide-react"
import { format } from "date-fns"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Input } from "@/components/ui/input"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { DataTablePagination } from "@/components/common/data-table-pagination"
import { DataTableViewOptions } from "@/components/common/data-table-view-options"
import { InvoiceDialog } from "@/components/invoices/invoice-dialog"
import { InvoicePreview } from "@/components/invoices/invoice-preview"
import { type Invoice, invoices } from "@/lib/data/invoices"
import { cn } from "@/lib/utils"
import { DeleteDialog } from "@/components/common/delete-dialog"

export default function InvoicesPage() {
  const router = useRouter()
  const [sorting, setSorting] = useState<SortingState>([])
  const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([])
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({})
  const [rowSelection, setRowSelection] = useState({})
  const [data, setData] = useState<Invoice[]>(invoices)

  // Dialog states
  const [createDialogOpen, setCreateDialogOpen] = useState(false)
  const [editDialogOpen, setEditDialogOpen] = useState(false)
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false)
  const [previewDialogOpen, setPreviewDialogOpen] = useState(false)
  const [selectedInvoice, setSelectedInvoice] = useState<Invoice | null>(null)

  const columns: ColumnDef<Invoice>[] = [
    {
      accessorKey: "invoiceNumber",
      header: "Invoice #",
      cell: ({ row }) => <div className="font-medium">{row.getValue("invoiceNumber")}</div>,
    },
    {
      accessorKey: "customerName",
      header: ({ column }) => {
        return (
          <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}>
            Customer
            <ArrowUpDown className="ml-2 h-4 w-4" />
          </Button>
        )
      },
      cell: ({ row }) => <div className="font-medium">{row.getValue("customerName")}</div>,
    },
    {
      accessorKey: "date",
      header: "Date",
      cell: ({ row }) => format(new Date(row.getValue("date")), "MMM dd, yyyy"),
    },
    {
      accessorKey: "dueDate",
      header: "Due Date",
      cell: ({ row }) => format(new Date(row.getValue("dueDate")), "MMM dd, yyyy"),
    },
    {
      accessorKey: "status",
      header: "Status",
      cell: ({ row }) => {
        const status = row.getValue("status") as string
        return (
          <div
            className={cn(
              "px-2 py-1 rounded-full text-xs font-medium capitalize w-fit",
              status === "paid"
                ? "bg-green-100 text-green-800"
                : status === "pending"
                  ? "bg-yellow-100 text-yellow-800"
                  : status === "overdue"
                    ? "bg-red-100 text-red-800"
                    : "bg-gray-100 text-gray-800",
            )}
          >
            {status}
          </div>
        )
      },
      filterFn: (row, id, value) => {
        return value.includes(row.getValue(id))
      },
    },
    {
      accessorKey: "total",
      header: () => <div className="text-right">Total</div>,
      cell: ({ row }) => {
        const amount = Number.parseFloat(row.getValue("total"))
        const formatted = new Intl.NumberFormat("en-US", {
          style: "currency",
          currency: "USD",
        }).format(amount)

        return <div className="text-right font-medium">{formatted}</div>
      },
    },
    {
      id: "actions",
      cell: ({ row }) => {
        const invoice = row.original

        return (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" className="h-8 w-8 p-0">
                <span className="sr-only">Open menu</span>
                <MoreHorizontal className="h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuLabel>Actions</DropdownMenuLabel>
              <DropdownMenuItem
                onClick={() => {
                  setSelectedInvoice(invoice)
                  setPreviewDialogOpen(true)
                }}
              >
                <Eye className="mr-2 h-4 w-4" />
                View
              </DropdownMenuItem>
              <DropdownMenuItem
                onClick={() => {
                  setSelectedInvoice(invoice)
                  setEditDialogOpen(true)
                }}
              >
                <Pencil className="mr-2 h-4 w-4" />
                Edit
              </DropdownMenuItem>
              <DropdownMenuItem
                onClick={() => {
                  // In a real app, this would download the invoice as a PDF
                  alert("In a production app, this would download the invoice as a PDF")
                }}
              >
                <Download className="mr-2 h-4 w-4" />
                Download
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem
                onClick={() => {
                  setSelectedInvoice(invoice)
                  setDeleteDialogOpen(true)
                }}
                className="text-red-600"
              >
                <Trash className="mr-2 h-4 w-4" />
                Delete
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        )
      },
    },
  ]

  const table = useReactTable({
    data,
    columns,
    onSortingChange: setSorting,
    onColumnFiltersChange: setColumnFilters,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onColumnVisibilityChange: setColumnVisibility,
    onRowSelectionChange: setRowSelection,
    state: {
      sorting,
      columnFilters,
      columnVisibility,
      rowSelection,
    },
  })

  const handleCreateInvoice = async (values: any) => {
    // In a real app, this would send a request to the server
    const newInvoice: Invoice = {
      id: crypto.randomUUID(),
      invoiceNumber: values.invoiceNumber,
      customerId: crypto.randomUUID(),
      customerName: values.customerName,
      customerEmail: values.customerEmail,
      customerAddress: values.customerAddress,
      date: values.date.toISOString().split("T")[0],
      dueDate: values.dueDate.toISOString().split("T")[0],
      status: values.status,
      items: values.items,
      subtotal: values.items.reduce((sum: number, item: any) => sum + item.total, 0),
      tax: values.items.reduce((sum: number, item: any) => sum + item.total, 0) * 0.1,
      total: values.items.reduce((sum: number, item: any) => sum + item.total, 0) * 1.1,
      notes: values.notes,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    }

    setData([newInvoice, ...data])
  }

  const handleUpdateInvoice = async (values: any) => {
    // In a real app, this would send a request to the server
    if (!selectedInvoice) return

    const updatedInvoice: Invoice = {
      ...selectedInvoice,
      invoiceNumber: values.invoiceNumber,
      customerName: values.customerName,
      customerEmail: values.customerEmail,
      customerAddress: values.customerAddress,
      date: values.date.toISOString().split("T")[0],
      dueDate: values.dueDate.toISOString().split("T")[0],
      status: values.status,
      items: values.items,
      subtotal: values.items.reduce((sum: number, item: any) => sum + item.total, 0),
      tax: values.items.reduce((sum: number, item: any) => sum + item.total, 0) * 0.1,
      total: values.items.reduce((sum: number, item: any) => sum + item.total, 0) * 1.1,
      notes: values.notes,
      updatedAt: new Date().toISOString(),
    }

    setData(data.map((invoice) => (invoice.id === selectedInvoice.id ? updatedInvoice : invoice)))
  }

  const handleDeleteInvoice = async () => {
    // In a real app, this would send a request to the server
    if (!selectedInvoice) return

    setData(data.filter((invoice) => invoice.id !== selectedInvoice.id))
    setDeleteDialogOpen(false)
  }

  return (
    <>
      <div className="flex items-center justify-between space-y-2">
        <div>
          <h2 className="text-2xl font-bold tracking-tight">Invoices</h2>
          <p className="text-muted-foreground">Create, manage and send invoices to your customers</p>
        </div>
        <div className="flex items-center space-x-2">
          <Button onClick={() => setCreateDialogOpen(true)}>
            <Plus className="mr-2 h-4 w-4" /> New Invoice
          </Button>
        </div>
      </div>
      <Card className="mt-6">
        <CardHeader className="flex flex-row items-center justify-between px-6 py-4">
          <div>
            <CardTitle>Invoice Management</CardTitle>
            <CardDescription>Manage all your customer invoices in one place</CardDescription>
          </div>
          <div className="flex items-center gap-2">
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="outline" className="ml-auto">
                  Status <ChevronDown className="ml-2 h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuLabel>Filter by status</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuCheckboxItem
                  checked={
                    (table.getColumn("status")?.getFilterValue() as string[]) === undefined ||
                    (table.getColumn("status")?.getFilterValue() as string[])?.length === 0
                  }
                  onCheckedChange={() => {
                    table.getColumn("status")?.setFilterValue(undefined)
                  }}
                >
                  All
                </DropdownMenuCheckboxItem>
                <DropdownMenuCheckboxItem
                  checked={((table.getColumn("status")?.getFilterValue() as string[]) || []).includes("paid")}
                  onCheckedChange={(checked) => {
                    const filterValues = (table.getColumn("status")?.getFilterValue() as string[]) || []
                    if (checked) {
                      table.getColumn("status")?.setFilterValue([...filterValues, "paid"])
                    } else {
                      table.getColumn("status")?.setFilterValue(filterValues.filter((value) => value !== "paid"))
                    }
                  }}
                >
                  Paid
                </DropdownMenuCheckboxItem>
                <DropdownMenuCheckboxItem
                  checked={((table.getColumn("status")?.getFilterValue() as string[]) || []).includes("pending")}
                  onCheckedChange={(checked) => {
                    const filterValues = (table.getColumn("status")?.getFilterValue() as string[]) || []
                    if (checked) {
                      table.getColumn("status")?.setFilterValue([...filterValues, "pending"])
                    } else {
                      table.getColumn("status")?.setFilterValue(filterValues.filter((value) => value !== "pending"))
                    }
                  }}
                >
                  Pending
                </DropdownMenuCheckboxItem>
                <DropdownMenuCheckboxItem
                  checked={((table.getColumn("status")?.getFilterValue() as string[]) || []).includes("overdue")}
                  onCheckedChange={(checked) => {
                    const filterValues = (table.getColumn("status")?.getFilterValue() as string[]) || []
                    if (checked) {
                      table.getColumn("status")?.setFilterValue([...filterValues, "overdue"])
                    } else {
                      table.getColumn("status")?.setFilterValue(filterValues.filter((value) => value !== "overdue"))
                    }
                  }}
                >
                  Overdue
                </DropdownMenuCheckboxItem>
                <DropdownMenuCheckboxItem
                  checked={((table.getColumn("status")?.getFilterValue() as string[]) || []).includes("draft")}
                  onCheckedChange={(checked) => {
                    const filterValues = (table.getColumn("status")?.getFilterValue() as string[]) || []
                    if (checked) {
                      table.getColumn("status")?.setFilterValue([...filterValues, "draft"])
                    } else {
                      table.getColumn("status")?.setFilterValue(filterValues.filter((value) => value !== "draft"))
                    }
                  }}
                >
                  Draft
                </DropdownMenuCheckboxItem>
              </DropdownMenuContent>
            </DropdownMenu>
            <Input
              placeholder="Search invoices..."
              value={(table.getColumn("invoiceNumber")?.getFilterValue() as string) ?? ""}
              onChange={(event) => table.getColumn("invoiceNumber")?.setFilterValue(event.target.value)}
              className="max-w-sm"
            />
            <DataTableViewOptions table={table} />
          </div>
        </CardHeader>
        <CardContent>
          <div className="rounded-md border">
            <Table>
              <TableHeader>
                {table.getHeaderGroups().map((headerGroup) => (
                  <TableRow key={headerGroup.id}>
                    {headerGroup.headers.map((header) => {
                      return (
                        <TableHead key={header.id}>
                          {header.isPlaceholder
                            ? null
                            : flexRender(header.column.columnDef.header, header.getContext())}
                        </TableHead>
                      )
                    })}
                  </TableRow>
                ))}
              </TableHeader>
              <TableBody>
                {table.getRowModel().rows?.length ? (
                  table.getRowModel().rows.map((row) => (
                    <TableRow key={row.id} data-state={row.getIsSelected() && "selected"}>
                      {row.getVisibleCells().map((cell) => (
                        <TableCell key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</TableCell>
                      ))}
                    </TableRow>
                  ))
                ) : (
                  <TableRow>
                    <TableCell colSpan={columns.length} className="h-24 text-center">
                      No invoices found.
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          </div>
          <div className="mt-4">
            <DataTablePagination table={table} />
          </div>
        </CardContent>
      </Card>

      {/* Create Invoice Dialog */}
      <InvoiceDialog open={createDialogOpen} onOpenChange={setCreateDialogOpen} onSubmit={handleCreateInvoice} />

      {/* Edit Invoice Dialog */}
      {selectedInvoice && (
        <InvoiceDialog
          open={editDialogOpen}
          onOpenChange={setEditDialogOpen}
          invoice={selectedInvoice}
          onSubmit={handleUpdateInvoice}
        />
      )}

      {/* Delete Invoice Dialog */}
      {selectedInvoice && (
        <DeleteDialog
          open={deleteDialogOpen}
          onOpenChange={setDeleteDialogOpen}
          title="Delete Invoice"
          description={`Are you sure you want to delete invoice #${selectedInvoice.invoiceNumber}? This action cannot be undone.`}
          onDelete={handleDeleteInvoice}
        />
      )}

      {/* Preview Invoice Dialog */}
      {selectedInvoice && (
        <InvoicePreview open={previewDialogOpen} onOpenChange={setPreviewDialogOpen} invoice={selectedInvoice} />
      )}
    </>
  )
}
