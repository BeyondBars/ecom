"use client"

import { useState } from "react"
import { CheckCircle, Filter, RefreshCw, Search, Trash2, XCircle } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Checkbox } from "@/components/ui/checkbox"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { DeleteDialog } from "@/components/common/delete-dialog"
import { useToast } from "@/components/ui/use-toast"
import { DataTablePagination } from "@/components/common/data-table-pagination"
import { DataTableViewOptions } from "@/components/common/data-table-view-options"
import { comments } from "@/lib/data/comments"

export default function CommentsPage() {
  const { toast } = useToast()
  const [selectedComments, setSelectedComments] = useState<string[]>([])
  const [showDeleteDialog, setShowDeleteDialog] = useState(false)
  const [commentToDelete, setCommentToDelete] = useState<string | null>(null)
  const [filters, setFilters] = useState({
    search: "",
    status: "all", // Updated default value
    type: "all", // Updated default value
  })

  const handleSelectAll = (checked: boolean) => {
    if (checked) {
      setSelectedComments(comments.map((comment) => comment.id))
    } else {
      setSelectedComments([])
    }
  }

  const handleSelectComment = (commentId: string, checked: boolean) => {
    if (checked) {
      setSelectedComments([...selectedComments, commentId])
    } else {
      setSelectedComments(selectedComments.filter((id) => id !== commentId))
    }
  }

  const handleApproveComment = (commentId: string) => {
    // Approve comment logic here
    toast({
      title: "Comment approved",
      description: "The comment has been approved successfully.",
    })
  }

  const handleRejectComment = (commentId: string) => {
    // Reject comment logic here
    toast({
      title: "Comment rejected",
      description: "The comment has been rejected successfully.",
    })
  }

  const handleDeleteComment = (commentId: string) => {
    setCommentToDelete(commentId)
    setShowDeleteDialog(true)
  }

  const confirmDelete = () => {
    // Delete comment logic here
    toast({
      title: "Comment deleted",
      description: "The comment has been deleted successfully.",
    })
    setShowDeleteDialog(false)
    setCommentToDelete(null)
  }

  const handleBulkAction = (action: string) => {
    if (action === "delete") {
      // Delete selected comments logic here
      toast({
        title: "Comments deleted",
        description: `${selectedComments.length} comments have been deleted.`,
      })
      setSelectedComments([])
    } else if (action === "approve") {
      // Approve selected comments logic here
      toast({
        title: "Comments approved",
        description: `${selectedComments.length} comments have been approved.`,
      })
      setSelectedComments([])
    } else if (action === "reject") {
      // Reject selected comments logic here
      toast({
        title: "Comments rejected",
        description: `${selectedComments.length} comments have been rejected.`,
      })
      setSelectedComments([])
    }
  }

  const resetFilters = () => {
    setFilters({
      search: "",
      status: "all", // Updated default value
      type: "all", // Updated default value
    })
  }

  const getStatusBadgeColor = (status: string) => {
    switch (status) {
      case "approved":
        return "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300"
      case "pending":
        return "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300"
      case "rejected":
        return "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300"
      default:
        return "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
    }
  }

  return (
    <div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight">Comment Moderation</h2>
      </div>

      <Card>
        <CardHeader className="p-4">
          <CardTitle>Filters</CardTitle>
          <CardDescription>Narrow down your comment list with filters</CardDescription>
        </CardHeader>
        <CardContent className="p-4 pt-0">
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
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
                  placeholder="Content or author..."
                  value={filters.search}
                  onChange={(e) => setFilters({ ...filters, search: e.target.value })}
                />
              </div>
            </div>

            <div>
              <label htmlFor="status" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Status
              </label>
              <Select value={filters.status} onValueChange={(value) => setFilters({ ...filters, status: value })}>
                <SelectTrigger id="status">
                  <SelectValue placeholder="All Statuses" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Statuses</SelectItem>
                  <SelectItem value="approved">Approved</SelectItem>
                  <SelectItem value="pending">Pending</SelectItem>
                  <SelectItem value="rejected">Rejected</SelectItem>
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
                  <SelectItem value="blog">Blog</SelectItem>
                  <SelectItem value="product">Product</SelectItem>
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
            <CardTitle>Comments</CardTitle>
            <div className="flex items-center gap-2">
              <Select
                value={selectedComments.length > 0 ? "bulk" : ""}
                onValueChange={(value) => {
                  if (value && value !== "bulk") {
                    handleBulkAction(value)
                  }
                }}
                disabled={selectedComments.length === 0}
              >
                <SelectTrigger className="h-9 w-[160px]" disabled={selectedComments.length === 0}>
                  <SelectValue placeholder="Bulk Actions" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="bulk" disabled>
                    Bulk Actions
                  </SelectItem>
                  <SelectItem value="approve">Approve</SelectItem>
                  <SelectItem value="reject">Reject</SelectItem>
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
                      checked={selectedComments.length > 0 && selectedComments.length === comments.length}
                      onCheckedChange={handleSelectAll}
                      aria-label="Select all comments"
                    />
                  </TableHead>
                  <TableHead>Author</TableHead>
                  <TableHead>Comment</TableHead>
                  <TableHead>On</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Date</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {comments.map((comment) => (
                  <TableRow key={comment.id}>
                    <TableCell>
                      <Checkbox
                        checked={selectedComments.includes(comment.id)}
                        onCheckedChange={(checked) => handleSelectComment(comment.id, checked as boolean)}
                        aria-label={`Select comment by ${comment.author.name}`}
                      />
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center">
                        <Avatar className="h-8 w-8 mr-2">
                          <AvatarImage src={comment.author.avatar || "/placeholder.svg"} alt={comment.author.name} />
                          <AvatarFallback>
                            {comment.author.name
                              .split(" ")
                              .map((n) => n[0])
                              .join("")}
                          </AvatarFallback>
                        </Avatar>
                        <div>
                          <div className="font-medium">{comment.author.name}</div>
                          <div className="text-xs text-gray-500 dark:text-gray-400">{comment.author.email}</div>
                        </div>
                      </div>
                    </TableCell>
                    <TableCell>
                      <div className="max-w-xs truncate">{comment.content}</div>
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center">
                        <Badge variant="outline">{comment.type}</Badge>
                        <span className="ml-2">{comment.itemTitle}</span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <Badge variant="outline" className={getStatusBadgeColor(comment.status)}>
                        {comment.status}
                      </Badge>
                    </TableCell>
                    <TableCell>{comment.date}</TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end gap-2">
                        {comment.status !== "approved" && (
                          <Button
                            variant="ghost"
                            size="icon"
                            className="text-green-500 hover:text-green-700 hover:bg-green-100 dark:hover:bg-green-900"
                            onClick={() => handleApproveComment(comment.id)}
                          >
                            <CheckCircle className="h-4 w-4" />
                            <span className="sr-only">Approve</span>
                          </Button>
                        )}
                        {comment.status !== "rejected" && (
                          <Button
                            variant="ghost"
                            size="icon"
                            className="text-yellow-500 hover:text-yellow-700 hover:bg-yellow-100 dark:hover:bg-yellow-900"
                            onClick={() => handleRejectComment(comment.id)}
                          >
                            <XCircle className="h-4 w-4" />
                            <span className="sr-only">Reject</span>
                          </Button>
                        )}
                        <Button
                          variant="ghost"
                          size="icon"
                          className="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900"
                          onClick={() => handleDeleteComment(comment.id)}
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
        title="Delete Comment"
        description="Are you sure you want to delete this comment? This action cannot be undone."
        onConfirm={confirmDelete}
      />
    </div>
  )
}
