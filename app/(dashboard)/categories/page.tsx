"use client"

import { useState } from "react"
import { Plus, Edit, Trash2 } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { CategoryDialog } from "@/components/categories/category-dialog"
import { BrandDialog } from "@/components/categories/brand-dialog"
import { DeleteDialog } from "@/components/common/delete-dialog"
import { useToast } from "@/components/ui/use-toast"
import { categories } from "@/lib/data/categories"
import { brands } from "@/lib/data/brands"

export default function CategoriesPage() {
  const { toast } = useToast()
  const [activeTab, setActiveTab] = useState("categories")
  const [showCategoryDialog, setShowCategoryDialog] = useState(false)
  const [showBrandDialog, setShowBrandDialog] = useState(false)
  const [showDeleteDialog, setShowDeleteDialog] = useState(false)
  const [editingCategory, setEditingCategory] = useState<any>(null)
  const [editingBrand, setEditingBrand] = useState<any>(null)
  const [itemToDelete, setItemToDelete] = useState<{
    id: string
    type: "category" | "brand"
  } | null>(null)

  const handleEditCategory = (category: any) => {
    setEditingCategory(category)
    setShowCategoryDialog(true)
  }

  const handleEditBrand = (brand: any) => {
    setEditingBrand(brand)
    setShowBrandDialog(true)
  }

  const handleDeleteItem = (id: string, type: "category" | "brand") => {
    setItemToDelete({ id, type })
    setShowDeleteDialog(true)
  }

  const confirmDelete = () => {
    if (!itemToDelete) return

    // Delete item logic here
    toast({
      title: `${itemToDelete.type === "category" ? "Category" : "Brand"} deleted`,
      description: `The ${itemToDelete.type === "category" ? "category" : "brand"} has been deleted successfully.`,
    })
    setShowDeleteDialog(false)
    setItemToDelete(null)
  }

  const handleSaveCategory = (category: any) => {
    // Save category logic here
    toast({
      title: "Category saved",
      description: "The category has been saved successfully.",
    })
    setShowCategoryDialog(false)
    setEditingCategory(null)
  }

  const handleSaveBrand = (brand: any) => {
    // Save brand logic here
    toast({
      title: "Brand saved",
      description: "The brand has been saved successfully.",
    })
    setShowBrandDialog(false)
    setEditingBrand(null)
  }

  return (
    <div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight">Categories & Brands</h2>
        <div className="flex items-center gap-2">
          <Button
            onClick={() => {
              setEditingCategory(null)
              setShowCategoryDialog(true)
            }}
            className="h-9 gap-1"
          >
            <Plus className="h-4 w-4" />
            <span>Add Category</span>
          </Button>
          <Button
            onClick={() => {
              setEditingBrand(null)
              setShowBrandDialog(true)
            }}
            variant="outline"
            className="h-9 gap-1"
          >
            <Plus className="h-4 w-4" />
            <span>Add Brand</span>
          </Button>
        </div>
      </div>

      <Tabs defaultValue="categories" value={activeTab} onValueChange={setActiveTab} className="space-y-4">
        <TabsList>
          <TabsTrigger value="categories">Categories</TabsTrigger>
          <TabsTrigger value="brands">Brands</TabsTrigger>
        </TabsList>

        <TabsContent value="categories" className="space-y-4">
          <Card>
            <CardHeader className="p-4">
              <CardTitle>Categories</CardTitle>
              <CardDescription>Manage your product categories</CardDescription>
            </CardHeader>
            <CardContent className="p-0">
              <div className="rounded-md border">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Name</TableHead>
                      <TableHead>Slug</TableHead>
                      <TableHead>Products</TableHead>
                      <TableHead>Parent</TableHead>
                      <TableHead className="text-right">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {categories.map((category) => (
                      <TableRow key={category.id}>
                        <TableCell className="font-medium">{category.name}</TableCell>
                        <TableCell>{category.slug}</TableCell>
                        <TableCell>{category.productCount}</TableCell>
                        <TableCell>{category.parent ? category.parent.name : "-"}</TableCell>
                        <TableCell className="text-right">
                          <div className="flex justify-end gap-2">
                            <Button variant="ghost" size="icon" onClick={() => handleEditCategory(category)}>
                              <Edit className="h-4 w-4" />
                              <span className="sr-only">Edit</span>
                            </Button>
                            <Button
                              variant="ghost"
                              size="icon"
                              className="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900"
                              onClick={() => handleDeleteItem(category.id, "category")}
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
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="brands" className="space-y-4">
          <Card>
            <CardHeader className="p-4">
              <CardTitle>Brands</CardTitle>
              <CardDescription>Manage your product brands</CardDescription>
            </CardHeader>
            <CardContent className="p-0">
              <div className="rounded-md border">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Name</TableHead>
                      <TableHead>Slug</TableHead>
                      <TableHead>Products</TableHead>
                      <TableHead className="text-right">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {brands.map((brand) => (
                      <TableRow key={brand.id}>
                        <TableCell className="font-medium">
                          <div className="flex items-center">
                            {brand.logo && (
                              <div className="h-8 w-8 rounded-md bg-gray-100 dark:bg-gray-800 mr-3 overflow-hidden">
                                <img
                                  src={brand.logo || "/placeholder.svg"}
                                  alt={brand.name}
                                  className="h-full w-full object-contain"
                                />
                              </div>
                            )}
                            <span>{brand.name}</span>
                          </div>
                        </TableCell>
                        <TableCell>{brand.slug}</TableCell>
                        <TableCell>{brand.productCount}</TableCell>
                        <TableCell className="text-right">
                          <div className="flex justify-end gap-2">
                            <Button variant="ghost" size="icon" onClick={() => handleEditBrand(brand)}>
                              <Edit className="h-4 w-4" />
                              <span className="sr-only">Edit</span>
                            </Button>
                            <Button
                              variant="ghost"
                              size="icon"
                              className="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900"
                              onClick={() => handleDeleteItem(brand.id, "brand")}
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
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      <CategoryDialog
        open={showCategoryDialog}
        onOpenChange={setShowCategoryDialog}
        category={editingCategory}
        onSave={handleSaveCategory}
        categories={categories}
      />

      <BrandDialog
        open={showBrandDialog}
        onOpenChange={setShowBrandDialog}
        brand={editingBrand}
        onSave={handleSaveBrand}
      />

      <DeleteDialog
        open={showDeleteDialog}
        onOpenChange={setShowDeleteDialog}
        title={`Delete ${itemToDelete?.type === "category" ? "Category" : "Brand"}`}
        description={`Are you sure you want to delete this ${
          itemToDelete?.type === "category" ? "category" : "brand"
        }? This action cannot be undone.`}
        onConfirm={confirmDelete}
      />
    </div>
  )
}
