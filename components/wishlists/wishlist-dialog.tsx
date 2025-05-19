"use client"

import type React from "react"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Check, ChevronsUpDown, Loader2, Plus, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { toast } from "@/components/ui/use-toast"
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/components/ui/command"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { Switch } from "@/components/ui/switch"
import { Badge } from "@/components/ui/badge"
import { cn } from "@/lib/utils"
import type { Wishlist } from "@/lib/data/wishlists"

interface Product {
  id: number
  name: string
  price: number
  image: string
}

interface User {
  id: number
  name: string
  email: string
}

interface WishlistDialogProps {
  wishlist?: Wishlist
  open: boolean
  onOpenChange: (open: boolean) => void
}

export function WishlistDialog({ wishlist, open, onOpenChange }: WishlistDialogProps) {
  const router = useRouter()
  const isEditing = !!wishlist

  const [name, setName] = useState(wishlist?.name || "")
  const [description, setDescription] = useState(wishlist?.description || "")
  const [isPublic, setIsPublic] = useState(wishlist?.isPublic || false)
  const [selectedUser, setSelectedUser] = useState<User | null>(wishlist?.user || null)
  const [selectedProducts, setSelectedProducts] = useState<Product[]>([])
  const [userOpen, setUserOpen] = useState(false)
  const [productsOpen, setProductsOpen] = useState(false)
  const [isSubmitting, setIsSubmitting] = useState(false)

  // Mock data for users and products
  const users: User[] = [
    { id: 1, name: "John Doe", email: "john@example.com" },
    { id: 2, name: "Jane Smith", email: "jane@example.com" },
    { id: 3, name: "Bob Johnson", email: "bob@example.com" },
    { id: 4, name: "Alice Williams", email: "alice@example.com" },
  ]

  const products: Product[] = [
    { id: 101, name: "Wireless Headphones", price: 99.99, image: "/placeholder.svg" },
    { id: 102, name: "Smart Watch Series 7", price: 299.99, image: "/placeholder.svg" },
    { id: 103, name: "Bluetooth Speaker", price: 79.99, image: "/placeholder.svg" },
    { id: 104, name: "Laptop Pro", price: 1299.99, image: "/placeholder.svg" },
    { id: 105, name: "Smartphone X", price: 899.99, image: "/placeholder.svg" },
  ]

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    if (!name) {
      toast({
        title: "Validation Error",
        description: "Wishlist name is required",
        variant: "destructive",
      })
      return
    }

    if (!selectedUser) {
      toast({
        title: "Validation Error",
        description: "Please select a user",
        variant: "destructive",
      })
      return
    }

    setIsSubmitting(true)

    try {
      // Prepare data for API
      const wishlistData = {
        name,
        description,
        is_public: isPublic,
        user_id: selectedUser.id,
        product_ids: selectedProducts.map((p) => p.id),
      }

      // API call would go here
      // if (isEditing) {
      //   await updateWishlist(wishlist.id, wishlistData)
      // } else {
      //   await createWishlist(wishlistData)
      // }

      toast({
        title: isEditing ? "Wishlist Updated" : "Wishlist Created",
        description: isEditing
          ? `Wishlist "${name}" has been updated successfully.`
          : `Wishlist "${name}" has been created successfully.`,
      })

      onOpenChange(false)
      router.refresh()
    } catch (error) {
      toast({
        title: "Error",
        description: isEditing
          ? "Failed to update wishlist. Please try again."
          : "Failed to create wishlist. Please try again.",
        variant: "destructive",
      })
    } finally {
      setIsSubmitting(false)
    }
  }

  const removeProduct = (productId: number) => {
    setSelectedProducts(selectedProducts.filter((p) => p.id !== productId))
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[600px]">
        <form onSubmit={handleSubmit}>
          <DialogHeader>
            <DialogTitle>{isEditing ? "Edit Wishlist" : "Create Wishlist"}</DialogTitle>
            <DialogDescription>
              {isEditing ? "Update the wishlist details below." : "Fill in the details to create a new wishlist."}
            </DialogDescription>
          </DialogHeader>
          <div className="grid gap-4 py-4">
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="name" className="text-right">
                Name
              </Label>
              <Input
                id="name"
                value={name}
                onChange={(e) => setName(e.target.value)}
                className="col-span-3"
                placeholder="Enter wishlist name"
              />
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="description" className="text-right">
                Description
              </Label>
              <Textarea
                id="description"
                value={description}
                onChange={(e) => setDescription(e.target.value)}
                className="col-span-3"
                placeholder="Enter wishlist description"
              />
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="user" className="text-right">
                User
              </Label>
              <div className="col-span-3">
                <Popover open={userOpen} onOpenChange={setUserOpen}>
                  <PopoverTrigger asChild>
                    <Button
                      variant="outline"
                      role="combobox"
                      aria-expanded={userOpen}
                      className="w-full justify-between"
                    >
                      {selectedUser ? `${selectedUser.name} (${selectedUser.email})` : "Select user..."}
                      <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent className="w-[400px] p-0">
                    <Command>
                      <CommandInput placeholder="Search users..." />
                      <CommandEmpty>No user found.</CommandEmpty>
                      <CommandGroup>
                        {users.map((user) => (
                          <CommandItem
                            key={user.id}
                            value={user.name}
                            onSelect={() => {
                              setSelectedUser(user)
                              setUserOpen(false)
                            }}
                          >
                            <Check
                              className={cn("mr-2 h-4 w-4", selectedUser?.id === user.id ? "opacity-100" : "opacity-0")}
                            />
                            <div className="flex flex-col">
                              <span>{user.name}</span>
                              <span className="text-xs text-muted-foreground">{user.email}</span>
                            </div>
                          </CommandItem>
                        ))}
                      </CommandGroup>
                    </Command>
                  </PopoverContent>
                </Popover>
              </div>
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="visibility" className="text-right">
                Visibility
              </Label>
              <div className="col-span-3 flex items-center space-x-2">
                <Switch id="visibility" checked={isPublic} onCheckedChange={setIsPublic} />
                <Label htmlFor="visibility" className="cursor-pointer">
                  {isPublic ? "Public" : "Private"}
                </Label>
              </div>
            </div>
            <div className="grid grid-cols-4 items-start gap-4">
              <Label className="text-right pt-2">Products</Label>
              <div className="col-span-3 space-y-4">
                <Popover open={productsOpen} onOpenChange={setProductsOpen}>
                  <PopoverTrigger asChild>
                    <Button
                      variant="outline"
                      role="combobox"
                      aria-expanded={productsOpen}
                      className="w-full justify-between"
                    >
                      Select products
                      <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent className="w-[400px] p-0">
                    <Command>
                      <CommandInput placeholder="Search products..." />
                      <CommandEmpty>No product found.</CommandEmpty>
                      <CommandList>
                        <CommandGroup>
                          {products
                            .filter((product) => !selectedProducts.some((p) => p.id === product.id))
                            .map((product) => (
                              <CommandItem
                                key={product.id}
                                value={product.name}
                                onSelect={() => {
                                  setSelectedProducts([...selectedProducts, product])
                                  setProductsOpen(false)
                                }}
                              >
                                <div className="flex items-center">
                                  <Plus className="mr-2 h-4 w-4" />
                                  <span>{product.name}</span>
                                  <span className="ml-2 text-xs text-muted-foreground">${product.price}</span>
                                </div>
                              </CommandItem>
                            ))}
                        </CommandGroup>
                      </CommandList>
                    </Command>
                  </PopoverContent>
                </Popover>

                <div className="flex flex-wrap gap-2">
                  {selectedProducts.map((product) => (
                    <Badge key={product.id} variant="secondary" className="flex items-center gap-1">
                      {product.name}
                      <Button
                        variant="ghost"
                        size="icon"
                        className="h-4 w-4 p-0 hover:bg-transparent"
                        onClick={() => removeProduct(product.id)}
                      >
                        <X className="h-3 w-3" />
                        <span className="sr-only">Remove {product.name}</span>
                      </Button>
                    </Badge>
                  ))}
                  {selectedProducts.length === 0 && (
                    <span className="text-sm text-muted-foreground">No products selected</span>
                  )}
                </div>
              </div>
            </div>
          </div>
          <DialogFooter>
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
              Cancel
            </Button>
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              {isEditing ? "Update" : "Create"}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  )
}
