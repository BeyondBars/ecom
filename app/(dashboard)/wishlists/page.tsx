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
import { ArrowUpDown, MoreHorizontal, Plus } from "lucide-react"

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
import type { Wishlist } from "@/lib/data/wishlists"
import { DeleteDialog } from "@/components/common/delete-dialog"
import { toast } from "@/components/ui/use-toast"

const data: Wishlist[] = [
  {
    id: 1,
    name: "Summer Wishlist",
    user: {
      id: 1,
      name: "John Doe",
      email: "john@example.com",
    },
    itemsCount: 5,
    isPublic: true,
    createdAt: "2023-04-15T10:00:00Z",
  },
  {
    id: 2,
    name: "Birthday Ideas",
    user: {
      id: 2,
      name: "Jane Smith",
      email: "jane@example.com",
    },
    itemsCount: 12,
    isPublic: false,
    createdAt: "2023-05-20T14:30:00Z",
  },
  {
    id: 3,
    name: "Tech Gadgets",
    user: {
      id: 3,
      name: "Bob Johnson",
      email: "bob@example.com",
    },
    itemsCount: 8,
    isPublic: true,
    createdAt: "2023-06-10T09:15:00Z",
  },
  {
    id: 4,
    name: "Home Decor",
    user: {
      id: 1,
      name: "John Doe",
      email: "john@example.com",
    },
    itemsCount: 15,
    isPublic: false,
    createdAt: "2023-07-05T16:45:00Z",
  },
  {
    id: 5,
    name: "Gift Ideas",
    user: {
      id: 4,
      name: "Alice Williams",
      email: "alice@example.com",
    },
    itemsCount: 7,
    isPublic: true,
    createdAt: "2023-08-12T11:20:00Z",
  },
]

export default function WishlistsPage() {
  const router = useRouter()
  const [sorting, setSorting] = useState<SortingState>([])
  const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([])
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({})
  const [rowSelection, setRowSelection] = useState({})
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false)
  const [selectedWishlist, setSelectedWishlist] = useState<Wishlist | null>(null)

  const columns: ColumnDef<Wishlist>[] = [
    {
      accessorKey: "id",
      header: "ID",
      cell: ({ row }) => <div className="w-[80px]">{row.getValue("id")}</div>,
    },
    {
      accessorKey: "name",
      header: ({ column }) => {
        return (
          <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}>
            Name
            <ArrowUpDown className="ml-2 h-4 w-4" />
          </Button>
        )
      },
      cell: ({ row }) => <div className="font-medium">{row.getValue("name")}</div>,
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
      accessorKey: "itemsCount",
      header: ({ column }) => {
        return (
          <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}>
            Items
            <ArrowUpDown className="ml-2 h-4 w-4" />
          </Button>
        )
      },
      cell: ({ row }) => <div className="text-center">{row.getValue("itemsCount")}</div>,
    },
    {
      accessorKey: "isPublic",
      header: "Visibility",
      cell: ({ row }) => {
        const isPublic = row.getValue("isPublic") as boolean
        return <Badge variant={isPublic ? "default" : "secondary"}>{isPublic ? "Public" : "Private"}</Badge>
      },
    },
    {
      accessorKey: "createdAt",
      header: ({ column }) => {
        return (
          <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}>
            Created At
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
        const wishlist = row.original

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
              <DropdownMenuItem onClick={() => router.push(`/wishlists/${wishlist.id}`)}>View Details</DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem
                onClick={() => {
                  setSelectedWishlist(wishlist)
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
    if (!selectedWishlist) return

    try {
      // API call would go here
      // await deleteWishlist(selectedWishlist.id)

      toast({
        title: "Wishlist deleted",
        description: `Wishlist "${selectedWishlist.name}" has been deleted.`,
      })

      // Refresh data
      router.refresh()
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to delete wishlist. Please try again.",
        variant: "destructive",
      })
    } finally {
      setDeleteDialogOpen(false)
      setSelectedWishlist(null)
    }
  }

  return (
    <>
      <div className="h-full flex-1 flex-col space-y-8 p-8 md:flex">
        <div className="flex items-center justify-between space-y-2">
          <div>
            <h2 className="text-2xl font-bold tracking-tight">Wishlists</h2>
            <p className="text-muted-foreground">Manage customer wishlists and their items</p>
          </div>
          <div className="flex items-center space-x-2">
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Add Wishlist
            </Button>
          </div>
        </div>
        <Card>
          <CardHeader>
            <CardTitle>All Wishlists</CardTitle>
            <CardDescription>View and manage all customer wishlists in your store.</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="mb-4 flex items-center gap-4">
              <Input
                placeholder="Filter wishlists..."
                value={(table.getColumn("name")?.getFilterValue() as string) ?? ""}
                onChange={(event) => table.getColumn("name")?.setFilterValue(event.target.value)}
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
                        No wishlists found.
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
        title="Delete Wishlist"
        description={`Are you sure you want to delete the wishlist "${selectedWishlist?.name}"? This action cannot be undone.`}
      />
    </>
  )
}
