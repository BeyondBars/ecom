"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Download, Filter, Plus, RefreshCw, Search, SlidersHorizontal, Trash2 } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Checkbox } from "@/components/ui/checkbox"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { BlogPostDialog } from "@/components/blog/blog-post-dialog"
import { DeleteDialog } from "@/components/common/delete-dialog"
import { useToast } from "@/components/ui/use-toast"
import { DataTablePagination } from "@/components/common/data-table-pagination"
import { DataTableViewOptions } from "@/components/common/data-table-view-options"
import { blogPosts } from "@/lib/data/blog-posts"

export default function BlogPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [selectedPosts, setSelectedPosts] = useState<string[]>([])
  const [showAddPostDialog, setShowAddPostDialog] = useState(false)
  const [showDeleteDialog, setShowDeleteDialog] = useState(false)
  const [editingPost, setEditingPost] = useState<any>(null)
  const [postToDelete, setPostToDelete] = useState<string | null>(null)
  const [filters, setFilters] = useState({
    search: "",
    status: "all",
    author: "all",
  })

  const handleSelectAll = (checked: boolean) => {
    if (checked) {
      setSelectedPosts(blogPosts.map((post) => post.id))
    } else {
      setSelectedPosts([])
    }
  }

  const handleSelectPost = (postId: string, checked: boolean) => {
    if (checked) {
      setSelectedPosts([...selectedPosts, postId])
    } else {
      setSelectedPosts(selectedPosts.filter((id) => id !== postId))
    }
  }

  const handleEditPost = (post: any) => {
    setEditingPost(post)
    setShowAddPostDialog(true)
  }

  const handleDeletePost = (postId: string) => {
    setPostToDelete(postId)
    setShowDeleteDialog(true)
  }

  const confirmDelete = () => {
    // Delete post logic here
    toast({
      title: "Post deleted",
      description: "The blog post has been deleted successfully.",
    })
    setShowDeleteDialog(false)
    setPostToDelete(null)
  }

  const handleSavePost = (post: any) => {
    // Save post logic here
    toast({
      title: "Post saved",
      description: "The blog post has been saved successfully.",
    })
    setShowAddPostDialog(false)
    setEditingPost(null)
  }

  const handleBulkAction = (action: string) => {
    if (action === "delete") {
      // Delete selected posts logic here
      toast({
        title: "Posts deleted",
        description: `${selectedPosts.length} posts have been deleted.`,
      })
      setSelectedPosts([])
    } else if (action === "publish") {
      // Publish selected posts logic here
      toast({
        title: "Posts published",
        description: `${selectedPosts.length} posts have been published.`,
      })
      setSelectedPosts([])
    } else if (action === "draft") {
      // Set selected posts to draft logic here
      toast({
        title: "Posts set to draft",
        description: `${selectedPosts.length} posts have been set to draft.`,
      })
      setSelectedPosts([])
    }
  }

  const resetFilters = () => {
    setFilters({
      search: "",
      status: "all",
      author: "all",
    })
  }

  const getStatusBadgeColor = (status: string) => {
    switch (status) {
      case "published":
        return "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300"
      case "draft":
        return "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300"
      case "archived":
        return "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300"
      default:
        return "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
    }
  }

  return (
    <div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight">Blog Management</h2>
        <div className="flex items-center gap-2">
          <Button
            variant="outline"
            size="sm"
            className="h-9 gap-1"
            onClick={() => {
              // Export posts logic here
              toast({
                title: "Export started",
                description: "Your blog posts are being exported.",
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
              setEditingPost(null)
              setShowAddPostDialog(true)
            }}
          >
            <Plus className="h-4 w-4" />
            <span>Add Post</span>
          </Button>
        </div>
      </div>

      <Card>
        <CardHeader className="p-4">
          <CardTitle>Filters</CardTitle>
          <CardDescription>Narrow down your blog post list with filters</CardDescription>
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
                  placeholder="Title or content..."
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
                  <SelectItem value="published">Published</SelectItem>
                  <SelectItem value="draft">Draft</SelectItem>
                  <SelectItem value="archived">Archived</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div>
              <label htmlFor="author" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Author
              </label>
              <Select value={filters.author} onValueChange={(value) => setFilters({ ...filters, author: value })}>
                <SelectTrigger id="author">
                  <SelectValue placeholder="All Authors" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Authors</SelectItem>
                  <SelectItem value="1">John Doe</SelectItem>
                  <SelectItem value="2">Jane Smith</SelectItem>
                  <SelectItem value="3">Robert Johnson</SelectItem>
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
            <CardTitle>Blog Posts</CardTitle>
            <div className="flex items-center gap-2">
              <Select
                value={selectedPosts.length > 0 ? "bulk" : ""}
                onValueChange={(value) => {
                  if (value && value !== "bulk") {
                    handleBulkAction(value)
                  }
                }}
                disabled={selectedPosts.length === 0}
              >
                <SelectTrigger className="h-9 w-[160px]" disabled={selectedPosts.length === 0}>
                  <SelectValue placeholder="Bulk Actions" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="bulk" disabled>
                    Bulk Actions
                  </SelectItem>
                  <SelectItem value="publish">Publish</SelectItem>
                  <SelectItem value="draft">Set to Draft</SelectItem>
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
                      checked={selectedPosts.length > 0 && selectedPosts.length === blogPosts.length}
                      onCheckedChange={handleSelectAll}
                      aria-label="Select all posts"
                    />
                  </TableHead>
                  <TableHead>Title</TableHead>
                  <TableHead>Author</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Comments</TableHead>
                  <TableHead>Published</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {blogPosts.map((post) => (
                  <TableRow key={post.id}>
                    <TableCell>
                      <Checkbox
                        checked={selectedPosts.includes(post.id)}
                        onCheckedChange={(checked) => handleSelectPost(post.id, checked as boolean)}
                        aria-label={`Select ${post.title}`}
                      />
                    </TableCell>
                    <TableCell className="font-medium">
                      <div className="flex items-center">
                        {post.featuredImage && (
                          <div className="h-10 w-10 rounded-md bg-gray-100 dark:bg-gray-800 mr-3 overflow-hidden">
                            <img
                              src={post.featuredImage || "/placeholder.svg"}
                              alt={post.title}
                              className="h-full w-full object-cover"
                            />
                          </div>
                        )}
                        <span>{post.title}</span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center">
                        <Avatar className="h-8 w-8 mr-2">
                          <AvatarImage src={post.author.avatar || "/placeholder.svg"} alt={post.author.name} />
                          <AvatarFallback>
                            {post.author.name
                              .split(" ")
                              .map((n) => n[0])
                              .join("")}
                          </AvatarFallback>
                        </Avatar>
                        <span>{post.author.name}</span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <Badge variant="outline" className={getStatusBadgeColor(post.status)}>
                        {post.status}
                      </Badge>
                    </TableCell>
                    <TableCell>{post.commentCount}</TableCell>
                    <TableCell>{post.publishedAt}</TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end gap-2">
                        <Button variant="ghost" size="icon" onClick={() => handleEditPost(post)}>
                          <SlidersHorizontal className="h-4 w-4" />
                          <span className="sr-only">Edit</span>
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          className="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900"
                          onClick={() => handleDeletePost(post.id)}
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

      <BlogPostDialog
        open={showAddPostDialog}
        onOpenChange={setShowAddPostDialog}
        post={editingPost}
        onSave={handleSavePost}
      />

      <DeleteDialog
        open={showDeleteDialog}
        onOpenChange={setShowDeleteDialog}
        title="Delete Blog Post"
        description="Are you sure you want to delete this blog post? This action cannot be undone."
        onConfirm={confirmDelete}
      />
    </div>
  )
}
