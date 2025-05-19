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
import { ProductDialog } from "@/components/products/product-dialog"
import { DeleteDialog } from "@/components/common/delete-dialog"
import { useToast } from "@/components/ui/use-toast"
import { DataTablePagination } from "@/components/common/data-table-pagination"
import { DataTableViewOptions } from "@/components/common/data-table-view-options"
import { products } from "@/lib/data/products"

export default function ProductsPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [selectedProducts, setSelectedProducts] = useState<string[]>([])
  const [showAddProductDialog, setShowAddProductDialog] = useState(false)
  const [showDeleteDialog, setShowDeleteDialog] = useState(false)
  const [editingProduct, setEditingProduct] = useState<any>(null)
  const [productToDelete, setProductToDelete] = useState<string | null>(null)
  const [filters, setFilters] = useState({
    search: "",
    category: "",
    status: "",
    sort: "name",
  })

  const handleSelectAll = (checked: boolean) => {
    if (checked) {
      setSelectedProducts(products.map((product) => product.id))
    } else {
      setSelectedProducts([])
    }
  }

  const handleSelectProduct = (productId: string, checked: boolean) => {
    if (checked) {
      setSelectedProducts([...selectedProducts, productId])
    } else {
      setSelectedProducts(selectedProducts.filter((id) => id !== productId))
    }
  }

  const handleEditProduct = (product: any) => {
    setEditingProduct(product)
    setShowAddProductDialog(true)
  }

  const handleDeleteProduct = (productId: string) => {
    setProductToDelete(productId)
    setShowDeleteDialog(true)
  }

  const confirmDelete = () => {
    // Delete product logic here
    toast({
      title: "Product deleted",
      description: "The product has been deleted successfully.",
    })
    setShowDeleteDialog(false)
    setProductToDelete(null)
  }

  const handleSaveProduct = (product: any) => {
    // Save product logic here
    toast({
      title: "Product saved",
      description: "The product has been saved successfully.",
    })
    setShowAddProductDialog(false)
    setEditingProduct(null)
  }

  const handleBulkAction = (action: string) => {
    if (action === "delete") {
      // Delete selected products logic here
      toast({
        title: "Products deleted",
        description: `${selectedProducts.length} products have been deleted.`,
      })
      setSelectedProducts([])
    } else if (action === "activate") {
      // Activate selected products logic here
      toast({
        title: "Products activated",
        description: `${selectedProducts.length} products have been activated.`,
      })
      setSelectedProducts([])
    } else if (action === "deactivate") {
      // Deactivate selected products logic here
      toast({
        title: "Products deactivated",
        description: `${selectedProducts.length} products have been deactivated.`,
      })
      setSelectedProducts([])
    }
  }

  const resetFilters = () => {
    setFilters({
      search: "",
      category: "",
      status: "",
      sort: "name",
    })
  }

  const getStatusBadgeColor = (status: string) => {
    switch (status) {
      case "active":
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
        <h2 className="text-3xl font-bold tracking-tight">Products</h2>
        <div className="flex items-center gap-2">
          <Button
            variant="outline"
            size="sm"
            className="h-9 gap-1"
            onClick={() => {
              // Export products logic here
              toast({
                title: "Export started",
                description: "Your products are being exported.",
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
              setEditingProduct(null)
              setShowAddProductDialog(true)
            }}
          >
            <Plus className="h-4 w-4" />
            <span>Add Product</span>
          </Button>
        </div>
      </div>

      <Card>
        <CardHeader className="p-4">
          <CardTitle>Filters</CardTitle>
          <CardDescription>Narrow down your product list with filters</CardDescription>
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
                  placeholder="Search products..."
                  value={filters.search}
                  onChange={(e) => setFilters({ ...filters, search: e.target.value })}
                />
              </div>
            </div>

            <div>
              <label htmlFor="category" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Category
              </label>
              <Select value={filters.category} onValueChange={(value) => setFilters({ ...filters, category: value })}>
                <SelectTrigger id="category">
                  <SelectValue placeholder="All Categories" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Categories</SelectItem>
                  <SelectItem value="electronics">Electronics</SelectItem>
                  <SelectItem value="clothing">Clothing</SelectItem>
                  <SelectItem value="home">Home & Kitchen</SelectItem>
                  <SelectItem value="books">Books</SelectItem>
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
                  <SelectItem value="active">Active</SelectItem>
                  <SelectItem value="draft">Draft</SelectItem>
                  <SelectItem value="archived">Archived</SelectItem>
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
                  <SelectItem value="price-asc">Price: Low to High</SelectItem>
                  <SelectItem value="price-desc">Price: High to Low</SelectItem>
                  <SelectItem value="created_at">Newest</SelectItem>
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
            <CardTitle>Product List</CardTitle>
            <div className="flex items-center gap-2">
              <Select
                value={selectedProducts.length > 0 ? "bulk" : ""}
                onValueChange={(value) => {
                  if (value && value !== "bulk") {
                    handleBulkAction(value)
                  }
                }}
                disabled={selectedProducts.length === 0}
              >
                <SelectTrigger className="h-9 w-[160px]" disabled={selectedProducts.length === 0}>
                  <SelectValue placeholder="Bulk Actions" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="bulk" disabled>
                    Bulk Actions
                  </SelectItem>
                  <SelectItem value="activate">Activate</SelectItem>
                  <SelectItem value="deactivate">Deactivate</SelectItem>
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
                      checked={selectedProducts.length > 0 && selectedProducts.length === products.length}
                      onCheckedChange={handleSelectAll}
                      aria-label="Select all products"
                    />
                  </TableHead>
                  <TableHead>Product</TableHead>
                  <TableHead>SKU</TableHead>
                  <TableHead>Price</TableHead>
                  <TableHead>Stock</TableHead>
                  <TableHead>Category</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {products.map((product) => (
                  <TableRow key={product.id}>
                    <TableCell>
                      <Checkbox
                        checked={selectedProducts.includes(product.id)}
                        onCheckedChange={(checked) => handleSelectProduct(product.id, checked as boolean)}
                        aria-label={`Select ${product.name}`}
                      />
                    </TableCell>
                    <TableCell className="font-medium">
                      <div className="flex items-center">
                        <div className="h-10 w-10 rounded-md bg-gray-100 dark:bg-gray-800 mr-3 overflow-hidden">
                          {product.thumbnail && (
                            <img
                              src={product.thumbnail || "/placeholder.svg"}
                              alt={product.name}
                              className="h-full w-full object-cover"
                            />
                          )}
                        </div>
                        <span>{product.name}</span>
                      </div>
                    </TableCell>
                    <TableCell>{product.sku}</TableCell>
                    <TableCell>${product.price.toFixed(2)}</TableCell>
                    <TableCell>{product.stock}</TableCell>
                    <TableCell>{product.category}</TableCell>
                    <TableCell>
                      <Badge variant="outline" className={getStatusBadgeColor(product.status)}>
                        {product.status}
                      </Badge>
                    </TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end gap-2">
                        <Button variant="ghost" size="icon" onClick={() => handleEditProduct(product)}>
                          <SlidersHorizontal className="h-4 w-4" />
                          <span className="sr-only">Edit</span>
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          className="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900"
                          onClick={() => handleDeleteProduct(product.id)}
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

      <ProductDialog
        open={showAddProductDialog}
        onOpenChange={setShowAddProductDialog}
        product={editingProduct}
        onSave={handleSaveProduct}
      />

      <DeleteDialog
        open={showDeleteDialog}
        onOpenChange={setShowDeleteDialog}
        title="Delete Product"
        description="Are you sure you want to delete this product? This action cannot be undone."
        onConfirm={confirmDelete}
      />
    </div>
  )
}
