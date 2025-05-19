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
import { ArrowUpDown, MoreHorizontal } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import {
  DropdownMenu,
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
import { Badge } from "@/components/ui/badge"
import { DeleteDialog } from "@/components/common/delete-dialog"
import { toast } from "@/components/ui/use-toast"
import type { Like } from "@/lib/data/likes"

const data: Like[] = [
  {
    id: 1,
    user: {
      id: 1,
      name: "John Doe",
      email: "john@example.com",
    },
    likeableType: "product",
    likeableName: "Wireless Headphones",
    likeableId: 101,
    createdAt: "2023-04-15T10:00:00Z",
  },
  {
    id: 2,
    user: {
      id: 2,
      name: "Jane Smith",
      email: "jane@example.com",
    },
    likeableType: "blog-post",
    likeableName: "Top 10 Tech Gadgets of 2023",
    likeableId: 201,
    createdAt: "2023-05-20T14:30:00Z",
  },
  {
    id: 3,
    user: {
      id: 3,
      name: "Bob Johnson",
      email: "bob@example.com",
    },
    likeableType: "product",
    likeableName: "Smart Watch Series 7",
    likeableId: 102,
    createdAt: "2023-06-10T09:15:00Z",
  },
  {
    id: 4,
    user: {
      id: 1,
      name: "John Doe",
      email: "john@example.com",
    },
    likeableType: "product",
    likeableName: "Bluetooth Speaker",
    likeableId: 103,
    createdAt: "2023-07-05T16:45:00Z",
  },
  {
    id: 5,
    user: {
      id: 4,
      name: "Alice Williams",
      email: "alice@example.com",
    },
    likeableType: "blog-post",
    likeableName: "How to Choose the Right Smartphone",
    likeableId: 202,
    createdAt: "2023-08-12T11:20:00Z",
  },
]

export default function LikesPage() {
  const router = useRouter()
  const [sorting, setSorting] = useState<SortingState>([])
  const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([])
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({})
  const [rowSelection, setRowSelection] = useState({})
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false)
  const [selectedLike, setSelectedLike] = useState<Like | null>(null)

  const columns: ColumnDef<Like>[] = [
    {
      accessorKey: "id",
      header: "ID",
      cell: ({ row }) => <div className="w-[80px]">{row.getValue("id")}</div>,
    },
    {
      accessorKey: "user",
      header: "User",
      cell: ({ row }) => {
        const user = row.getValue("user") as { name: string; email: string }
        return (
          <div className="flex flex-col">
            <span className="font-medium">{user.name}</span>
            <span className="text-xs text-muted-foreground">{user.email}</span>
          </div>
        )
      },
    },
    {
      accessorKey: "likeableType",
      header: "Type",
      cell: ({ row }) => {
        const type = row.getValue("likeableType") as string
        return (
          <Badge variant={type === "product" ? "default" : "secondary"}>
            {type === "product" ? "Product" : "Blog Post"}
          </Badge>
        )
      },
    },
    {
      accessorKey: "likeableName",
      header: ({ column }) => {
        return (
          <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}>
            Item
            <ArrowUpDown className="ml-2 h-4 w-4" />
          </Button>
        )
      },
      cell: ({ row }) => <div className="font-medium">{row.getValue("likeableName")}</div>,
    },
    {
      accessorKey: "likeableId",
      header: "Item ID",
      cell: ({ row }) => <div>{row.getValue("likeableId")}</div>,
    },
    {
      accessorKey: "createdAt",
      header: ({ column }) => {
        return (
          <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}>
            Liked At
            <ArrowUpDown className="ml-2 h-4 w-4" />
          </Button>
        )
      },
      cell: ({ row }) => {
        const date = new Date(row.getValue("createdAt"))
        return <div>{date.toLocaleDateString()}</div>
      },
    },
    {
      id: "actions",
      enableHiding: false,
      cell: ({ row }) => {
        const like = row.original

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
                  if (like.likeableType === "product") {
                    router.push(`/products/${like.likeableId}`)
                  } else {
                    router.push(`/blog/${like.likeableId}`)
                  }
                }}
              >
                View Item
              </DropdownMenuItem>
              <DropdownMenuItem onClick={() => router.push(`/users/${like.user.id}`)}>View User</DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem
                onClick={() => {
                  setSelectedLike(like)
                  setDeleteDialogOpen(true)
                }}
                className="text-destructive focus:text-destructive"
              >
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

  const handleDelete = async () => {
    if (!selectedLike) return

    try {
      // API call would go here
      // await deleteLike(selectedLike.id)

      toast({
        title: "Like deleted",
        description: `Like has been deleted successfully.`,
      })

      // Refresh data
      router.refresh()
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to delete like. Please try again.",
        variant: "destructive",
      })
    } finally {
      setDeleteDialogOpen(false)
      setSelectedLike(null)
    }
  }

  return (
    <>
      <div className="h-full flex-1 flex-col space-y-8 p-8 md:flex">
        <div className="flex items-center justify-between space-y-2">
          <div>
            <h2 className="text-2xl font-bold tracking-tight">Likes</h2>
            <p className="text-muted-foreground">Manage product and blog post likes from customers</p>
          </div>
        </div>
        <Card>
          <CardHeader>
            <CardTitle>All Likes</CardTitle>
            <CardDescription>View and manage all likes in your store.</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="mb-4 flex items-center gap-4">
              <Input
                placeholder="Filter by item name..."
                value={(table.getColumn("likeableName")?.getFilterValue() as string) ?? ""}
                onChange={(event) => table.getColumn("likeableName")?.setFilterValue(event.target.value)}
                className="max-w-sm"
              />
              <DataTableViewOptions table={table} />
            </div>
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
                          <TableCell key={cell.id}>
                            {flexRender(cell.column.columnDef.cell, cell.getContext())}
                          </TableCell>
                        ))}
                      </TableRow>
                    ))
                  ) : (
                    <TableRow>
                      <TableCell colSpan={columns.length} className="h-24 text-center">
                        No likes found.
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
      </div>

      <DeleteDialog
        open={deleteDialogOpen}
        onOpenChange={setDeleteDialogOpen}
        onDelete={handleDelete}
        title="Delete Like"
        description="Are you sure you want to delete this like? This action cannot be undone."
      />
    </>
  )
}
