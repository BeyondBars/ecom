"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Download, Filter, Plus, RefreshCw, Search, SlidersHorizontal, Trash2, Heart, Globe, Lock } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Checkbox } from "@/components/ui/checkbox"
import { WishlistDialog } from "@/components/wishlists/wishlist-dialog"
import { DeleteDialog } from "@/components/common/delete-dialog"
import { useToast } from "@/components/ui/use-toast"
import { DataTablePagination } from "@/components/common/data-table-pagination"
import { DataTableViewOptions } from "@/components/common/data-table-view-options"
import { wishlists } from "@/lib/data/wishlists"

export default function WishlistsPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [selectedWishlists, setSelectedWishlists] = useState<string[]>([])
  const [showAddWishlistDialog, setShowAddWishlistDialog] = useState(false)
  const [showDeleteDialog, setShowDeleteDialog] = useState(false)
  const [editingWishlist, setEditingWishlist] = useState<any>(null)
  const [wishlistToDelete, setWishlistToDelete] = useState<string | null>(null)
  const [filters, setFilters] = useState({
    search: "",
    user: "",
    visibility: "",
    sort: "created_at",
  })

  const handleSelectAll = (checked: boolean) => {
    if (checked) {
      setSelectedWishlists(wishlists.map((wishlist) => wishlist.id))
    } else {
      setSelectedWishlists([])
    }
  }

  const handleSelectWishlist = (wishlistId: string, checked: boolean) => {
    if (checked) {
      setSelectedWishlists([...selectedWishlists, wishlistId])
    } else {
      setSelectedWishlists(selectedWishlists.filter((id) => id !== wishlistId))
    }
  }

  const handleEditWishlist = (wishlist: any) => {
    setEditingWishlist(wishlist)
    setShowAddWishlistDialog(true)
  }

  const handleDeleteWishlist = (wishlistId: string) => {
    setWishlistToDelete(wishlistId)
    setShowDeleteDialog(true)
  }

  const confirmDelete = () => {
    // Delete wishlist logic here
    toast({
      title: "Wishlist deleted",
      description: "The wishlist has been deleted successfully.",
    })
    setShowDeleteDialog(false)
    setWishlistToDelete(null)
  }

  const handleSaveWishlist = (wishlist: any) => {
    // Save wishlist logic here
    toast({
      title: "Wishlist saved",
      description: "The wishlist has been saved successfully.",
    })
    setShowAddWishlistDialog(false)
    setEditingWishlist(null)
  }

  const handleBulkAction = (action: string) => {
    if (action === "delete") {
      // Delete selected wishlists logic here
      toast({
        title: "Wishlists deleted",
        description: `${selectedWishlists.length} wishlists have been deleted.`,
      })
      setSelectedWishlists([])
    } else if (action === "make-public") {
      // Make selected wishlists public logic here
      toast({
        title: "Wishlists updated",
        description: `${selectedWishlists.length} wishlists have been made public.`,
      })
      setSelectedWishlists([])
    } else if (action === "make-private") {
      // Make selected wishlists private logic here
      toast({
        title: "Wishlists updated",
        description: `${selectedWishlists.length} wishlists have been made private.`,
      })
      setSelectedWishlists([])
    }
  }

  const resetFilters = () => {
    setFilters({
      search: "",
      user: "",
      visibility: "",
      sort: "created_at",
    })
  }

  return (
    <div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight">Wishlists</h2>
        <div className="flex items-center gap-2">
          <Button
            variant="outline"
            size="sm"
            className="h-9 gap-1"
            onClick={() => {
              // Export wishlists logic here
              toast({
                title: "Export started",
                description: "Your wishlists are being exported.",
              })
            }}
          >
            <Download className="h-4 w-4" />
            <span className="hidden sm:inline">Export</span>
          </Button>
          <Button
            size="sm"
            className="h-9 gap-1"
            onClick={() => {
              setEditingWishlist(null)
              setShowAddWishlistDialog(true)
            }}
          >
            <Plus className="h-4 w-4" />
            <span>Add Wishlist</span>
          </Button>
        </div>
      </div>

      <Card>
        <CardHeader className="p-4">
          <CardTitle>Filters</CardTitle>
          <CardDescription>Narrow down your wishlist list with filters</CardDescription>
        </CardHeader>
        <CardContent className="p-4 pt-0">
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4">
            <div>
              <label htmlFor="search" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Search
              </label>
              <div className="relative rounded-md shadow-sm">
                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Search className="h-4 w-4 text-gray-400" />
                </div>
                <Input
                  type="text"
                  id="search"
                  className="pl-10"
                  placeholder="Search wishlists..."
                  value={filters.search}
                  onChange={(e) => setFilters({ ...filters, search: e.target.value })}
                />
              </div>
            </div>

            <div>
              <label htmlFor="user" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                User
              </label>
              <Select value={filters.user} onValueChange={(value) => setFilters({ ...filters, user: value })}>
                <SelectTrigger id="user">
                  <SelectValue placeholder="All Users" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Users</SelectItem>
                  <SelectItem value="1">John Doe</SelectItem>
                  <SelectItem value="2">Jane Smith</SelectItem>
                  <SelectItem value="3">Bob Johnson</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div>
              <label htmlFor="visibility" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Visibility
              </label>
              <Select
                value={filters.visibility}
                onValueChange={(value) => setFilters({ ...filters, visibility: value })}
              >
                <SelectTrigger id="visibility">
                  <SelectValue placeholder="All Visibilities" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Visibilities</SelectItem>
                  <SelectItem value="public">Public</SelectItem>
                  <SelectItem value="private">Private</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div>
              <label htmlFor="sort" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Sort By
              </label>
              <Select value={filters.sort} onValueChange={(value) => setFilters({ ...filters, sort: value })}>
                <SelectTrigger id="sort">
                  <SelectValue placeholder="Sort by" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="name">Name</SelectItem>
                  <SelectItem value="created_at">Newest</SelectItem>
                  <SelectItem value="items_count">Most Items</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div className="mt-4 flex justify-end gap-2">
            <Button variant="outline" size="sm" onClick={resetFilters} className="h-9 gap-1">
              <RefreshCw className="h-4 w-4" />
              Reset
            </Button>
            <Button size="sm" className="h-9 gap-1">
              <Filter className="h-4 w-4" />
              Apply Filters
            </Button>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader className="p-4">
          <div className="flex items-center justify-between">
            <CardTitle>Wishlist List</CardTitle>
            <div className="flex items-center gap-2">
              <Select
                value={selectedWishlists.length > 0 ? "bulk" : ""}
                onValueChange={(value) => {
                  if (value && value !== "bulk") {
                    handleBulkAction(value)
                  }
                }}
                disabled={selectedWishlists.length === 0}
              >
                <SelectTrigger className="h-9 w-[160px]" disabled={selectedWishlists.length === 0}>
                  <SelectValue placeholder="Bulk Actions" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="bulk" disabled>
                    Bulk Actions
                  </SelectItem>
                  <SelectItem value="make-public">Make Public</SelectItem>
                  <SelectItem value="make-private">Make Private</SelectItem>
                  <SelectItem value="delete">Delete</SelectItem>
                </SelectContent>
              </Select>
              <DataTableViewOptions />
            </div>
          </div>
        </CardHeader>
        <CardContent className="p-0">
          <div className="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead className="w-12">
                    <Checkbox
                      checked={selectedWishlists.length > 0 && selectedWishlists.length === wishlists.length}
                      onCheckedChange={handleSelectAll}
                      aria-label="Select all wishlists"
                    />
                  </TableHead>
                  <TableHead>Name</TableHead>
                  <TableHead>User</TableHead>
                  <TableHead>Items</TableHead>
                  <TableHead>Visibility</TableHead>
                  <TableHead>Created</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {wishlists.map((wishlist) => (
                  <TableRow key={wishlist.id}>
                    <TableCell>
                      <Checkbox
                        checked={selectedWishlists.includes(wishlist.id)}
                        onCheckedChange={(checked) => handleSelectWishlist(wishlist.id, checked as boolean)}
                        aria-label={`Select ${wishlist.name}`}
                      />
                    </TableCell>
                    <TableCell className="font-medium">
                      <div className="flex items-center">
                        <Heart className="h-4 w-4 mr-2 text-rose-500" />
                        <span>{wishlist.name}</span>
                      </div>
                    </TableCell>
                    <TableCell>{wishlist.user.name}</TableCell>
                    <TableCell>{wishlist.items.length}</TableCell>
                    <TableCell>
                      <Badge
                        variant="outline"
                        className={
                          wishlist.is_public
                            ? "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300"
                            : "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                        }
                      >
                        {wishlist.is_public ? (
                          <div className="flex items-center">
                            <Globe className="h-3 w-3 mr-1" />
                            Public
                          </div>
                        ) : (
                          <div className="flex items-center">
                            <Lock className="h-3 w-3 mr-1" />
                            Private
                          </div>
                        )}
                      </Badge>
                    </TableCell>
                    <TableCell>{new Date(wishlist.created_at).toLocaleDateString()}</TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end gap-2">
                        <Button variant="ghost" size="icon" onClick={() => handleEditWishlist(wishlist)}>
                          <SlidersHorizontal className="h-4 w-4" />
                          <span className="sr-only">Edit</span>
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          className="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900"
                          onClick={() => handleDeleteWishlist(wishlist.id)}
                        >
                          <Trash2 className="h-4 w-4" />
                          <span className="sr-only">Delete</span>
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
          <div className="p-4">
            <DataTablePagination
              pageCount={10}
              currentPage={1}
              perPage={10}
              onPageChange={(page) => {
                console.log("Page changed to:", page)
              }}
              onPerPageChange={(perPage) => {
                console.log("Per page changed to:", perPage)
              }}
            />
          </div>
        </CardContent>
      </Card>

      <WishlistDialog
        open={showAddWishlistDialog}
        onOpenChange={setShowAddWishlistDialog}
        wishlist={editingWishlist}
        onSave={handleSaveWishlist}
      />

      <DeleteDialog
        open={showDeleteDialog}
        onOpenChange={setShowDeleteDialog}
        title="Delete Wishlist"
        description="Are you sure you want to delete this wishlist? This action cannot be undone."
        onConfirm={confirmDelete}
      />
    </div>
  )
}
