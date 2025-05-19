"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Download, Filter, RefreshCw, Search, Trash2, Heart, ExternalLink } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Checkbox } from "@/components/ui/checkbox"
import { DeleteDialog } from "@/components/common/delete-dialog"
import { useToast } from "@/components/ui/use-toast"
import { DataTablePagination } from "@/components/common/data-table-pagination"
import { DataTableViewOptions } from "@/components/common/data-table-view-options"
import { likes } from "@/lib/data/likes"

export default function LikesPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [selectedLikes, setSelectedLikes] = useState<string[]>([])
  const [showDeleteDialog, setShowDeleteDialog] = useState(false)
  const [likeToDelete, setLikeToDelete] = useState<string | null>(null)
  const [filters, setFilters] = useState({
    search: "",
    user: "",
    type: "",
    sort: "created_at",
  })

  const handleSelectAll = (checked: boolean) => {
    if (checked) {
      setSelectedLikes(likes.map((like) => like.id))
    } else {
      setSelectedLikes([])
    }
  }

  const handleSelectLike = (likeId: string, checked: boolean) => {
    if (checked) {
      setSelectedLikes([...selectedLikes, likeId])
    } else {
      setSelectedLikes(selectedLikes.filter((id) => id !== likeId))
    }
  }

  const handleDeleteLike = (likeId: string) => {
    setLikeToDelete(likeId)
    setShowDeleteDialog(true)
  }

  const confirmDelete = () => {
    // Delete like logic here
    toast({
      title: "Like deleted",
      description: "The like has been deleted successfully.",
    })
    setShowDeleteDialog(false)
    setLikeToDelete(null)
  }

  const handleBulkAction = (action: string) => {
    if (action === "delete") {
      // Delete selected likes logic here
      toast({
        title: "Likes deleted",
        description: `${selectedLikes.length} likes have been deleted.`,
      })
      setSelectedLikes([])
    }
  }

  const resetFilters = () => {
    setFilters({
      search: "",
      user: "",
      type: "",
      sort: "created_at",
    })
  }

  const getLikeableTypeLabel = (type: string) => {
    switch (type) {
      case "product":
        return "Product"
      case "blog_post":
        return "Blog Post"
      default:
        return type
    }
  }

  const getLikeableTypeBadgeColor = (type: string) => {
    switch (type) {
      case "product":
        return "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300"
      case "blog_post":
        return "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300"
      default:
        return "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
    }
  }

  return (
    <div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight">Likes</h2>
        <div className="flex items-center gap-2">
          <Button
            variant="outline"
            size="sm"
            className="h-9 gap-1"
            onClick={() => {
              // Export likes logic here
              toast({
                title: "Export started",
                description: "Your likes are being exported.",
              })
            }}
          >
            <Download className="h-4 w-4" />
            <span className="hidden sm:inline">Export</span>
          </Button>
        </div>
      </div>

      <Card>
        <CardHeader className="p-4">
          <CardTitle>Filters</CardTitle>
          <CardDescription>Narrow down your likes list with filters</CardDescription>
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
                  placeholder="Search likes..."
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
              <label htmlFor="type" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Type
              </label>
              <Select value={filters.type} onValueChange={(value) => setFilters({ ...filters, type: value })}>
                <SelectTrigger id="type">
                  <SelectValue placeholder="All Types" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Types</SelectItem>
                  <SelectItem value="product">Product</SelectItem>
                  <SelectItem value="blog_post">Blog Post</SelectItem>
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
                  <SelectItem value="created_at">Newest</SelectItem>
                  <SelectItem value="user_name">User Name</SelectItem>
                  <SelectItem value="likeable_name">Item Name</SelectItem>
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
            <CardTitle>Likes List</CardTitle>
            <div className="flex items-center gap-2">
              <Select
                value={selectedLikes.length > 0 ? "bulk" : ""}
                onValueChange={(value) => {
                  if (value && value !== "bulk") {
                    handleBulkAction(value)
                  }
                }}
                disabled={selectedLikes.length === 0}
              >
                <SelectTrigger className="h-9 w-[160px]" disabled={selectedLikes.length === 0}>
                  <SelectValue placeholder="Bulk Actions" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="bulk" disabled>
                    Bulk Actions
                  </SelectItem>
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
                      checked={selectedLikes.length > 0 && selectedLikes.length === likes.length}
                      onCheckedChange={handleSelectAll}
                      aria-label="Select all likes"
                    />
                  </TableHead>
                  <TableHead>User</TableHead>
                  <TableHead>Item</TableHead>
                  <TableHead>Type</TableHead>
                  <TableHead>Created</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {likes.map((like) => (
                  <TableRow key={like.id}>
                    <TableCell>
                      <Checkbox
                        checked={selectedLikes.includes(like.id)}
                        onCheckedChange={(checked) => handleSelectLike(like.id, checked as boolean)}
                        aria-label={`Select like by ${like.user.name}`}
                      />
                    </TableCell>
                    <TableCell className="font-medium">{like.user.name}</TableCell>
                    <TableCell>
                      <div className="flex items-center">
                        <Heart className="h-4 w-4 mr-2 text-rose-500" />
                        <span>{like.likeable.name}</span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <Badge variant="outline" className={getLikeableTypeBadgeColor(like.likeable_type)}>
                        {getLikeableTypeLabel(like.likeable_type)}
                      </Badge>
                    </TableCell>
                    <TableCell>{new Date(like.created_at).toLocaleDateString()}</TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end gap-2">
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => {
                            // View item logic here
                            if (like.likeable_type === "product") {
                              router.push(`/products/${like.likeable.id}`)
                            } else if (like.likeable_type === "blog_post") {
                              router.push(`/blog/${like.likeable.id}`)
                            }
                          }}
                        >
                          <ExternalLink className="h-4 w-4" />
                          <span className="sr-only">View</span>
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          className="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900"
                          onClick={() => handleDeleteLike(like.id)}
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

      <DeleteDialog
        open={showDeleteDialog}
        onOpenChange={setShowDeleteDialog}
        title="Delete Like"
        description="Are you sure you want to delete this like? This action cannot be undone."
        onConfirm={confirmDelete}
      />
    </div>
  )
}
