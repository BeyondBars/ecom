"use client"

import { useState, useEffect } from "react"
import { zodResolver } from "@hookform/resolvers/zod"
import { useForm } from "react-hook-form"
import * as z from "zod"
import { X, Plus, Trash2 } from "lucide-react"

import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form"
import { Input } from "@/components/ui/input"
import { Textarea } from "@/components/ui/textarea"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Switch } from "@/components/ui/switch"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { products } from "@/lib/data/products"
import { users } from "@/lib/data/users"

const wishlistFormSchema = z.object({
  name: z.string().min(2, {
    message: "Wishlist name must be at least 2 characters.",
  }),
  user_id: z.string({
    required_error: "Please select a user.",
  }),
  is_public: z.boolean().default(false),
  description: z.string().optional(),
})

interface WishlistDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  wishlist?: any
  onSave: (wishlist: any) => void
}

export function WishlistDialog({ open, onOpenChange, wishlist, onSave }: WishlistDialogProps) {
  const [wishlistItems, setWishlistItems] = useState<any[]>([])
  const [showAddItemForm, setShowAddItemForm] = useState(false)
  const [selectedProductId, setSelectedProductId] = useState<string>("")
  const [itemNotes, setItemNotes] = useState<string>("")

  const form = useForm<z.infer<typeof wishlistFormSchema>>({
    resolver: zodResolver(wishlistFormSchema),
    defaultValues: {
      name: wishlist?.name || "",
      user_id: wishlist?.user_id || "",
      is_public: wishlist?.is_public || false,
      description: wishlist?.description || "",
    },
  })

  useEffect(() => {
    if (wishlist) {
      form.reset({
        name: wishlist.name || "",
        user_id: wishlist.user_id || "",
        is_public: wishlist.is_public || false,
        description: wishlist.description || "",
      })
      setWishlistItems(wishlist.items || [])
    } else {
      form.reset({
        name: "",
        user_id: "",
        is_public: false,
        description: "",
      })
      setWishlistItems([])
    }
  }, [wishlist, form])

  function onSubmit(values: z.infer<typeof wishlistFormSchema>) {
    onSave({ ...values, items: wishlistItems })
  }

  const handleAddItem = () => {
    if (!selectedProductId) return

    const product = products.find((p) => p.id === selectedProductId)
    if (!product) return

    // Check if product already exists in wishlist
    if (wishlistItems.some((item) => item.product_id === selectedProductId)) {
      return
    }

    setWishlistItems([
      ...wishlistItems,
      {
        id: `temp-${Date.now()}`,
        product_id: selectedProductId,
        product,
        notes: itemNotes,
      },
    ])

    setSelectedProductId("")
    setItemNotes("")
    setShowAddItemForm(false)
  }

  const handleRemoveItem = (itemId: string) => {
    setWishlistItems(wishlistItems.filter((item) => item.id !== itemId))
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>{wishlist ? "Edit Wishlist" : "Add New Wishlist"}</DialogTitle>
          <DialogDescription>
            {wishlist ? "Update the wishlist details below." : "Fill in the details to add a new wishlist."}
          </DialogDescription>
        </DialogHeader>
        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
              <FormField
                control={form.control}
                name="name"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Wishlist Name</FormLabel>
                    <FormControl>
                      <Input placeholder="My Wishlist" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="user_id"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>User</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Select a user" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        {users.map((user) => (
                          <SelectItem key={user.id} value={user.id}>
                            {user.name}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>

            <FormField
              control={form.control}
              name="description"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Description</FormLabel>
                  <FormControl>
                    <Textarea
                      placeholder="Description of the wishlist"
                      className="resize-none"
                      rows={3}
                      {...field}
                      value={field.value || ""}
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="is_public"
              render={({ field }) => (
                <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                  <div className="space-y-0.5">
                    <FormLabel className="text-base">Public Wishlist</FormLabel>
                    <FormDescription>Make this wishlist visible to everyone</FormDescription>
                  </div>
                  <FormControl>
                    <Switch checked={field.value} onCheckedChange={field.onChange} />
                  </FormControl>
                </FormItem>
              )}
            />

            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <h3 className="text-lg font-medium">Wishlist Items</h3>
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  onClick={() => setShowAddItemForm(true)}
                  className="gap-1"
                >
                  <Plus className="h-4 w-4" />
                  Add Item
                </Button>
              </div>

              {showAddItemForm && (
                <div className="rounded-md border p-4 space-y-4">
                  <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div className="space-y-2">
                      <label htmlFor="product" className="text-sm font-medium">
                        Product
                      </label>
                      <Select value={selectedProductId} onValueChange={setSelectedProductId}>
                        <SelectTrigger id="product">
                          <SelectValue placeholder="Select a product" />
                        </SelectTrigger>
                        <SelectContent>
                          {products.map((product) => (
                            <SelectItem key={product.id} value={product.id}>
                              {product.name}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>
                    <div className="space-y-2">
                      <label htmlFor="notes" className="text-sm font-medium">
                        Notes
                      </label>
                      <Input
                        id="notes"
                        placeholder="Optional notes"
                        value={itemNotes}
                        onChange={(e) => setItemNotes(e.target.value)}
                      />
                    </div>
                  </div>
                  <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" size="sm" onClick={() => setShowAddItemForm(false)}>
                      Cancel
                    </Button>
                    <Button type="button" size="sm" onClick={handleAddItem}>
                      Add to Wishlist
                    </Button>
                  </div>
                </div>
              )}

              {wishlistItems.length > 0 ? (
                <div className="rounded-md border">
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Product</TableHead>
                        <TableHead>Price</TableHead>
                        <TableHead>Notes</TableHead>
                        <TableHead className="text-right">Actions</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {wishlistItems.map((item) => (
                        <TableRow key={item.id}>
                          <TableCell className="font-medium">
                            <div className="flex items-center">
                              <div className="h-10 w-10 rounded-md bg-gray-100 dark:bg-gray-800 mr-3 overflow-hidden">
                                {item.product.thumbnail && (
                                  <img
                                    src={item.product.thumbnail || "/placeholder.svg"}
                                    alt={item.product.name}
                                    className="h-full w-full object-cover"
                                  />
                                )}
                              </div>
                              <span>{item.product.name}</span>
                            </div>
                          </TableCell>
                          <TableCell>${item.product.price.toFixed(2)}</TableCell>
                          <TableCell>{item.notes || "-"}</TableCell>
                          <TableCell className="text-right">
                            <Button
                              type="button"
                              variant="ghost"
                              size="icon"
                              className="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900"
                              onClick={() => handleRemoveItem(item.id)}
                            >
                              <Trash2 className="h-4 w-4" />
                              <span className="sr-only">Remove</span>
                            </Button>
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </div>
              ) : (
                <div className="rounded-md border border-dashed p-8 text-center">
                  <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                    <X className="h-6 w-6 text-gray-500" />
                  </div>
                  <h3 className="mt-2 text-sm font-medium">No items</h3>
                  <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    This wishlist doesn't have any items yet.
                  </p>
                  <div className="mt-4">
                    <Button type="button" variant="outline" size="sm" onClick={() => setShowAddItemForm(true)}>
                      Add your first item
                    </Button>
                  </div>
                </div>
              )}
            </div>

            <DialogFooter>
              <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
                Cancel
              </Button>
              <Button type="submit">{wishlist ? "Update Wishlist" : "Add Wishlist"}</Button>
            </DialogFooter>
          </form>
        </Form>
      </DialogContent>
    </Dialog>
  )
}
