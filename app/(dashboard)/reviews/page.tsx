"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Check, Filter, RefreshCw, Search, Star, ThumbsUp, Trash2, X } from "lucide-react"

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
import { reviews } from "@/lib/data/reviews"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip"
import { ReviewDialog } from "@/components/reviews/review-dialog"

export default function ReviewsPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [selectedReviews, setSelectedReviews] = useState<string[]>([])
  const [showReviewDialog, setShowReviewDialog] = useState(false)
  const [showDeleteDialog, setShowDeleteDialog] = useState(false)
  const [editingReview, setEditingReview] = useState<any>(null)
  const [reviewToDelete, setReviewToDelete] = useState<string | null>(null)
  const [filters, setFilters] = useState({
    search: "",
    product: "",
    rating: "",
    status: "",
    sort: "created_at",
  })

  const handleSelectAll = (checked: boolean) => {
    if (checked) {
      setSelectedReviews(reviews.map((review) => review.id))
    } else {
      setSelectedReviews([])
    }
  }

  const handleSelectReview = (reviewId: string, checked: boolean) => {
    if (checked) {
      setSelectedReviews([...selectedReviews, reviewId])
    } else {
      setSelectedReviews(selectedReviews.filter((id) => id !== reviewId))
    }
  }

  const handleEditReview = (review: any) => {
    setEditingReview(review)
    setShowReviewDialog(true)
  }

  const handleDeleteReview = (reviewId: string) => {
    setReviewToDelete(reviewId)
    setShowDeleteDialog(true)
  }

  const confirmDelete = () => {
    // Delete review logic here
    toast({
      title: "Review deleted",
      description: "The review has been deleted successfully.",
    })
    setShowDeleteDialog(false)
    setReviewToDelete(null)
  }

  const handleSaveReview = (review: any) => {
    // Save review logic here
    toast({
      title: "Review saved",
      description: "The review has been saved successfully.",
    })
    setShowReviewDialog(false)
    setEditingReview(null)
  }

  const handleBulkAction = (action: string) => {
    if (action === "delete") {
      // Delete selected reviews logic here
      toast({
        title: "Reviews deleted",
        description: `${selectedReviews.length} reviews have been deleted.`,
      })
      setSelectedReviews([])
    } else if (action === "approve") {
      // Approve selected reviews logic here
      toast({
        title: "Reviews approved",
        description: `${selectedReviews.length} reviews have been approved.`,
      })
      setSelectedReviews([])
    } else if (action === "reject") {
      // Reject selected reviews logic here
      toast({
        title: "Reviews rejected",
        description: `${selectedReviews.length} reviews have been rejected.`,
      })
      setSelectedReviews([])
    }
  }

  const resetFilters = () => {
    setFilters({
      search: "",
      product: "",
      rating: "",
      status: "",
      sort: "created_at",
    })
  }

  const handleApproveReview = (reviewId: string) => {
    // Approve review logic here
    toast({
      title: "Review approved",
      description: "The review has been approved successfully.",
    })
  }

  const handleRejectReview = (reviewId: string) => {
    // Reject review logic here
    toast({
      title: "Review rejected",
      description: "The review has been rejected successfully.",
    })
  }

  const handleFeatureReview = (reviewId: string, featured: boolean) => {
    // Feature/unfeature review logic here
    toast({
      title: featured ? "Review featured" : "Review unfeatured",
      description: `The review has been ${featured ? "featured" : "unfeatured"} successfully.`,
    })
  }

  const renderStars = (rating: number) => {
    return (
      <div className="flex items-center">
        {Array.from({ length: 5 }).map((_, i) => (
          <Star key={i} className={`h-4 w-4 ${i < rating ? "text-yellow-400 fill-yellow-400" : "text-gray-300"}`} />
        ))}
      </div>
    )
  }

  return (
    <div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight">Reviews</h2>
      </div>

      <Card>
        <CardHeader className="p-4">
          <CardTitle>Filters</CardTitle>
          <CardDescription>Narrow down your review list with filters</CardDescription>
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
                  placeholder="Search reviews..."
                  value={filters.search}
                  onChange={(e) => setFilters({ ...filters, search: e.target.value })}
                />
              </div>
            </div>

            <div>
              <label htmlFor="product" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Product
              </label>
              <Select value={filters.product} onValueChange={(value) => setFilters({ ...filters, product: value })}>
                <SelectTrigger id="product">
                  <SelectValue placeholder="All Products" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Products</SelectItem>
                  <SelectItem value="iphone">iPhone 13 Pro</SelectItem>
                  <SelectItem value="samsung">Samsung Galaxy S21</SelectItem>
                  <SelectItem value="macbook">MacBook Pro 16"</SelectItem>
                  <SelectItem value="sony">Sony WH-1000XM4</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div>
              <label htmlFor="rating" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Rating
              </label>
              <Select value={filters.rating} onValueChange={(value) => setFilters({ ...filters, rating: value })}>
                <SelectTrigger id="rating">
                  <SelectValue placeholder="All Ratings" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Ratings</SelectItem>
                  <SelectItem value="5">5 Stars</SelectItem>
                  <SelectItem value="4">4 Stars</SelectItem>
                  <SelectItem value="3">3 Stars</SelectItem>
                  <SelectItem value="2">2 Stars</SelectItem>
                  <SelectItem value="1">1 Star</SelectItem>
                </SelectContent>
              </Select>
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
            <CardTitle>Review List</CardTitle>
            <div className="flex items-center gap-2">
              <Select
                value={selectedReviews.length > 0 ? "bulk" : ""}
                onValueChange={(value) => {
                  if (value && value !== "bulk") {
                    handleBulkAction(value)
                  }
                }}
                disabled={selectedReviews.length === 0}
              >
                <SelectTrigger className="h-9 w-[160px]" disabled={selectedReviews.length === 0}>
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
                      checked={selectedReviews.length > 0 && selectedReviews.length === reviews.length}
                      onCheckedChange={handleSelectAll}
                      aria-label="Select all reviews"
                    />
                  </TableHead>
                  <TableHead>Product</TableHead>
                  <TableHead>Customer</TableHead>
                  <TableHead>Rating</TableHead>
                  <TableHead>Review</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Date</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {reviews.map((review) => (
                  <TableRow key={review.id}>
                    <TableCell>
                      <Checkbox
                        checked={selectedReviews.includes(review.id)}
                        onCheckedChange={(checked) => handleSelectReview(review.id, checked as boolean)}
                        aria-label={`Select review ${review.id}`}
                      />
                    </TableCell>
                    <TableCell className="font-medium">
                      <div className="flex items-center">
                        <div className="h-10 w-10 rounded-md bg-gray-100 dark:bg-gray-800 mr-3 overflow-hidden">
                          {review.product.thumbnail && (
                            <img
                              src={review.product.thumbnail || "/placeholder.svg"}
                              alt={review.product.name}
                              className="h-full w-full object-cover"
                            />
                          )}
                        </div>
                        <span>{review.product.name}</span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        <Avatar className="h-8 w-8">
                          <AvatarImage src={review.user.avatar || "/placeholder.svg"} alt={review.user.name} />
                          <AvatarFallback>{review.user.name.charAt(0)}</AvatarFallback>
                        </Avatar>
                        <div>
                          <div className="font-medium">{review.user.name}</div>
                          <div className="text-xs text-muted-foreground">{review.user.email}</div>
                        </div>
                      </div>
                    </TableCell>
                    <TableCell>{renderStars(review.rating)}</TableCell>
                    <TableCell>
                      <div>
                        <div className="font-medium">{review.title}</div>
                        <div className="text-sm text-muted-foreground line-clamp-1">{review.comment}</div>
                      </div>
                    </TableCell>
                    <TableCell>
                      <Badge
                        variant="outline"
                        className={
                          review.approved
                            ? "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300"
                            : review.rejected
                              ? "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300"
                              : "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300"
                        }
                      >
                        {review.approved ? "Approved" : review.rejected ? "Rejected" : "Pending"}
                      </Badge>
                      {review.featured && (
                        <Badge
                          variant="outline"
                          className="ml-2 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300"
                        >
                          Featured
                        </Badge>
                      )}
                      {review.verified_purchase && (
                        <Badge
                          variant="outline"
                          className="ml-2 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300"
                        >
                          Verified
                        </Badge>
                      )}
                    </TableCell>
                    <TableCell>{new Date(review.created_at).toLocaleDateString()}</TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end gap-2">
                        <TooltipProvider>
                          {!review.approved && !review.rejected && (
                            <>
                              <Tooltip>
                                <TooltipTrigger asChild>
                                  <Button
                                    variant="ghost"
                                    size="icon"
                                    className="text-green-500 hover:text-green-700 hover:bg-green-100 dark:hover:bg-green-900"
                                    onClick={() => handleApproveReview(review.id)}
                                  >
                                    <Check className="h-4 w-4" />
                                    <span className="sr-only">Approve</span>
                                  </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                  <p>Approve Review</p>
                                </TooltipContent>
                              </Tooltip>
                              <Tooltip>
                                <TooltipTrigger asChild>
                                  <Button
                                    variant="ghost"
                                    size="icon"
                                    className="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900"
                                    onClick={() => handleRejectReview(review.id)}
                                  >
                                    <X className="h-4 w-4" />
                                    <span className="sr-only">Reject</span>
                                  </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                  <p>Reject Review</p>
                                </TooltipContent>
                              </Tooltip>
                            </>
                          )}
                          <Tooltip>
                            <TooltipTrigger asChild>
                              <Button
                                variant="ghost"
                                size="icon"
                                className={
                                  review.featured
                                    ? "text-blue-500 hover:text-blue-700 hover:bg-blue-100 dark:hover:bg-blue-900"
                                    : "text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800"
                                }
                                onClick={() => handleFeatureReview(review.id, !review.featured)}
                              >
                                <ThumbsUp className="h-4 w-4" />
                                <span className="sr-only">{review.featured ? "Unfeature" : "Feature"}</span>
                              </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                              <p>{review.featured ? "Unfeature Review" : "Feature Review"}</p>
                            </TooltipContent>
                          </Tooltip>
                          <Tooltip>
                            <TooltipTrigger asChild>
                              <Button variant="ghost" size="icon" onClick={() => handleEditReview(review)}>
                                <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  width="24"
                                  height="24"
                                  viewBox="0 0 24 24"
                                  fill="none"
                                  stroke="currentColor"
                                  strokeWidth="2"
                                  strokeLinecap="round"
                                  strokeLinejoin="round"
                                  className="h-4 w-4"
                                >
                                  <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                  <path d="m15 5 4 4"></path>
                                </svg>
                                <span className="sr-only">Edit</span>
                              </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                              <p>Edit Review</p>
                            </TooltipContent>
                          </Tooltip>
                          <Tooltip>
                            <TooltipTrigger asChild>
                              <Button
                                variant="ghost"
                                size="icon"
                                className="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900"
                                onClick={() => handleDeleteReview(review.id)}
                              >
                                <Trash2 className="h-4 w-4" />
                                <span className="sr-only">Delete</span>
                              </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                              <p>Delete Review</p>
                            </TooltipContent>
                          </Tooltip>
                        </TooltipProvider>
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

      <ReviewDialog
        open={showReviewDialog}
        onOpenChange={setShowReviewDialog}
        review={editingReview}
        onSave={handleSaveReview}
      />

      <DeleteDialog
        open={showDeleteDialog}
        onOpenChange={setShowDeleteDialog}
        title="Delete Review"
        description="Are you sure you want to delete this review? This action cannot be undone."
        onConfirm={confirmDelete}
      />
    </div>
  )
}
