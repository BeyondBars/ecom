"use client"

import { useState, useEffect } from "react"
import { PlusCircle, Search, Filter, RefreshCw, Trash2, Edit, ToggleLeft, ToggleRight } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Badge } from "@/components/ui/badge"
import { DataTablePagination } from "@/components/common/data-table-pagination"
import { DataTableViewOptions } from "@/components/common/data-table-view-options"
import { CouponDialog } from "@/components/coupons/coupon-dialog"
import { DeleteDialog } from "@/components/common/delete-dialog"
import { formatDate } from "@/lib/utils"
import { useToast } from "@/components/ui/use-toast"

// Update the CouponsPage component to fetch data from the API
export default function CouponsPage() {
  const [searchQuery, setSearchQuery] = useState("")
  const [statusFilter, setStatusFilter] = useState("all")
  const [typeFilter, setTypeFilter] = useState("all")
  const [showCouponDialog, setShowCouponDialog] = useState(false)
  const [showDeleteDialog, setShowDeleteDialog] = useState(false)
  const [selectedCoupon, setSelectedCoupon] = useState<any>(null)
  const [currentPage, setCurrentPage] = useState(1)
  const [perPage, setPerPage] = useState(10)
  const [isLoading, setIsLoading] = useState(true)
  const [couponData, setCouponData] = useState<any>({ coupons: [], pagination: { total: 0, totalPages: 1 } })
  const { toast } = useToast()

  // Fetch coupons from the API
  const fetchCoupons = async () => {
    setIsLoading(true)
    try {
      const queryParams = new URLSearchParams()
      if (searchQuery) queryParams.append("code", searchQuery)
      if (statusFilter !== "all") queryParams.append("status", statusFilter)
      if (typeFilter !== "all") queryParams.append("discountType", typeFilter)
      queryParams.append("page", currentPage.toString())
      queryParams.append("limit", perPage.toString())

      const response = await fetch(`/api/coupons?${queryParams.toString()}`)
      if (!response.ok) throw new Error("Failed to fetch coupons")

      const data = await response.json()
      setCouponData(data)
    } catch (error) {
      console.error("Error fetching coupons:", error)
      toast({
        title: "Error",
        description: "Failed to fetch coupons. Please try again.",
        variant: "destructive",
      })
    } finally {
      setIsLoading(false)
    }
  }

  // Fetch coupons when filters or pagination changes
  useEffect(() => {
    fetchCoupons()
  }, [searchQuery, statusFilter, typeFilter, currentPage, perPage])

  // Handle coupon edit
  const handleEditCoupon = (coupon: any) => {
    setSelectedCoupon(coupon)
    setShowCouponDialog(true)
  }

  // Handle coupon delete
  const handleDeleteCoupon = (coupon: any) => {
    setSelectedCoupon(coupon)
    setShowDeleteDialog(true)
  }

  // Handle coupon status toggle
  const handleToggleStatus = async (coupon: any) => {
    try {
      const newStatus = coupon.status === "active" ? "inactive" : "active"
      const response = await fetch(`/api/coupons/${coupon.id}/status`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ status: newStatus }),
      })

      if (!response.ok) throw new Error("Failed to update coupon status")

      toast({
        title: "Success",
        description: `Coupon ${coupon.code} is now ${newStatus}.`,
      })

      // Refresh the coupon list
      fetchCoupons()
    } catch (error) {
      console.error("Error updating coupon status:", error)
      toast({
        title: "Error",
        description: "Failed to update coupon status. Please try again.",
        variant: "destructive",
      })
    }
  }

  // Handle coupon dialog close
  const handleCouponDialogClose = async (coupon?: any) => {
    setShowCouponDialog(false)

    if (coupon) {
      try {
        let response

        if (selectedCoupon) {
          // Update existing coupon
          response = await fetch(`/api/coupons/${selectedCoupon.id}`, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(coupon),
          })
        } else {
          // Create new coupon
          response = await fetch("/api/coupons", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(coupon),
          })
        }

        if (!response.ok) throw new Error("Failed to save coupon")

        toast({
          title: "Success",
          description: selectedCoupon
            ? `Coupon ${coupon.code} updated successfully.`
            : `Coupon ${coupon.code} created successfully.`,
        })

        // Refresh the coupon list
        fetchCoupons()
      } catch (error) {
        console.error("Error saving coupon:", error)
        toast({
          title: "Error",
          description: "Failed to save coupon. Please try again.",
          variant: "destructive",
        })
      }
    }

    setSelectedCoupon(null)
  }

  // Handle delete dialog close
  const handleDeleteDialogClose = (open: boolean) => {
    if (!open) {
      setShowDeleteDialog(false)
      setSelectedCoupon(null)
    }
  }

  // Handle delete confirmation
  const handleDeleteConfirm = async () => {
    try {
      const response = await fetch(`/api/coupons/${selectedCoupon.id}`, {
        method: "DELETE",
      })

      if (!response.ok) throw new Error("Failed to delete coupon")

      toast({
        title: "Success",
        description: `Coupon ${selectedCoupon.code} deleted successfully.`,
      })

      // Refresh the coupon list
      fetchCoupons()
      setShowDeleteDialog(false)
      setSelectedCoupon(null)
    } catch (error) {
      console.error("Error deleting coupon:", error)
      toast({
        title: "Error",
        description: "Failed to delete coupon. Please try again.",
        variant: "destructive",
      })
    }
  }

  return (
    <div className="flex flex-col space-y-6 p-1 md:p-8">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-3xl font-bold tracking-tight">Coupons</h2>
          <p className="text-muted-foreground">Manage discount coupons for your store</p>
        </div>
        <Button onClick={() => setShowCouponDialog(true)} className="flex items-center gap-2">
          <PlusCircle className="h-4 w-4" />
          <span>Add Coupon</span>
        </Button>
      </div>

      <Card>
        <CardHeader className="pb-3">
          <CardTitle>Coupon Management</CardTitle>
          <CardDescription>View and manage all discount coupons for your store</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex flex-col space-y-4">
            <div className="flex flex-col space-y-2 md:flex-row md:items-center md:justify-between md:space-y-0">
              <div className="flex flex-1 items-center space-x-2">
                <div className="relative flex-1 md:max-w-sm">
                  <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                  <Input
                    placeholder="Search coupons..."
                    className="pl-8"
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                  />
                </div>
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button variant="outline" size="sm" className="h-9 gap-1">
                      <Filter className="h-3.5 w-3.5" />
                      <span>Filters</span>
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end" className="w-[200px]">
                    <div className="p-2 space-y-2">
                      <div className="space-y-1">
                        <label className="text-xs font-medium">Status</label>
                        <Select value={statusFilter} onValueChange={setStatusFilter}>
                          <SelectTrigger className="h-8">
                            <SelectValue placeholder="Select status" />
                          </SelectTrigger>
                          <SelectContent>
                            <SelectItem value="all">All</SelectItem>
                            <SelectItem value="active">Active</SelectItem>
                            <SelectItem value="inactive">Inactive</SelectItem>
                            <SelectItem value="expired">Expired</SelectItem>
                          </SelectContent>
                        </Select>
                      </div>
                      <div className="space-y-1">
                        <label className="text-xs font-medium">Type</label>
                        <Select value={typeFilter} onValueChange={setTypeFilter}>
                          <SelectTrigger className="h-8">
                            <SelectValue placeholder="Select type" />
                          </SelectTrigger>
                          <SelectContent>
                            <SelectItem value="all">All</SelectItem>
                            <SelectItem value="percentage">Percentage</SelectItem>
                            <SelectItem value="fixed">Fixed Amount</SelectItem>
                          </SelectContent>
                        </Select>
                      </div>
                    </div>
                  </DropdownMenuContent>
                </DropdownMenu>
                <Button
                  variant="outline"
                  size="sm"
                  className="h-9 gap-1"
                  onClick={() => {
                    setSearchQuery("")
                    setStatusFilter("all")
                    setTypeFilter("all")
                  }}
                >
                  <RefreshCw className="h-3.5 w-3.5" />
                  <span>Reset</span>
                </Button>
              </div>
              <DataTableViewOptions />
            </div>

            <div className="rounded-md border">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted/50">
                    <tr className="text-left text-sm">
                      <th className="whitespace-nowrap px-4 py-3 font-medium">Code</th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">Discount</th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">Min. Order</th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">Start Date</th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">Expiry Date</th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">Usage</th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">Status</th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {couponData.coupons.length === 0 ? (
                      <tr>
                        <td colSpan={8} className="px-4 py-3 text-center text-sm">
                          No coupons found
                        </td>
                      </tr>
                    ) : (
                      couponData.coupons.map((coupon) => (
                        <tr key={coupon.id} className="border-t">
                          <td className="whitespace-nowrap px-4 py-3 font-medium">
                            <div className="font-mono text-sm">{coupon.code}</div>
                          </td>
                          <td className="whitespace-nowrap px-4 py-3">
                            {coupon.discountType === "percentage" ? (
                              <span>{coupon.discountValue}%</span>
                            ) : (
                              <span>${coupon.discountValue.toFixed(2)}</span>
                            )}
                            <div className="text-xs text-muted-foreground">
                              {coupon.discountType === "percentage" ? "Percentage" : "Fixed Amount"}
                            </div>
                          </td>
                          <td className="whitespace-nowrap px-4 py-3">${coupon.minimumOrderAmount.toFixed(2)}</td>
                          <td className="whitespace-nowrap px-4 py-3">{formatDate(coupon.startDate)}</td>
                          <td className="whitespace-nowrap px-4 py-3">{formatDate(coupon.expiryDate)}</td>
                          <td className="whitespace-nowrap px-4 py-3">
                            {coupon.usageCount}/{coupon.usageLimit || "∞"}
                          </td>
                          <td className="whitespace-nowrap px-4 py-3">
                            <Badge
                              variant={
                                coupon.status === "active"
                                  ? "default"
                                  : coupon.status === "inactive"
                                    ? "secondary"
                                    : "destructive"
                              }
                            >
                              {coupon.status}
                            </Badge>
                          </td>
                          <td className="whitespace-nowrap px-4 py-3">
                            <div className="flex items-center gap-2">
                              <Button
                                variant="ghost"
                                size="icon"
                                onClick={() => handleToggleStatus(coupon)}
                                title={coupon.status === "active" ? "Deactivate" : "Activate"}
                                className={`transition-colors duration-300 ${
                                  coupon.status === "active"
                                    ? "text-green-500 hover:text-green-600"
                                    : "text-red-500 hover:text-red-600"
                                }`}
                              >
                                {coupon.status === "active" ? (
                                  <ToggleRight className="h-4 w-4 transition-transform duration-300 ease-in-out" />
                                ) : (
                                  <ToggleLeft className="h-4 w-4 transition-transform duration-300 ease-in-out" />
                                )}
                              </Button>
                              <Button variant="ghost" size="icon" onClick={() => handleEditCoupon(coupon)}>
                                <Edit className="h-4 w-4" />
                              </Button>
                              <Button variant="ghost" size="icon" onClick={() => handleDeleteCoupon(coupon)}>
                                <Trash2 className="h-4 w-4" />
                              </Button>
                            </div>
                          </td>
                        </tr>
                      ))
                    )}
                  </tbody>
                </table>
              </div>
            </div>

            <DataTablePagination
              pageCount={couponData.pagination.totalPages}
              currentPage={currentPage}
              perPage={perPage}
              onPageChange={setCurrentPage}
              onPerPageChange={setPerPage}
            />
          </div>
        </CardContent>
      </Card>

      {/* Coupon Dialog */}
      <CouponDialog open={showCouponDialog} onOpenChange={handleCouponDialogClose} coupon={selectedCoupon} />

      {/* Delete Dialog */}
      <DeleteDialog
        open={showDeleteDialog}
        onOpenChange={handleDeleteDialogClose}
        title="Delete Coupon"
        description={`Are you sure you want to delete the coupon "${selectedCoupon?.code}"? This action cannot be undone.`}
        onConfirm={handleDeleteConfirm}
      />
    </div>
  )
}
