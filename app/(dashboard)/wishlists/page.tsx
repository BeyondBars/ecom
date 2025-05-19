"use client"

import { useState } from "react"
import { Plus } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Badge } from "@/components/ui/badge"
import type { Wishlist } from "@/lib/data/wishlists"
import WishlistDialog from "@/components/wishlists/wishlist-dialog"

const WISHLISTS_DATA: Wishlist[] = [
  {
    id: 1,
    name: "Electronics",
    description: "My favorite electronic products",
    is_public: true,
    user_id: 1,
    created_at: "2023-01-15T08:30:00.000Z",
    updated_at: "2023-01-15T08:30:00.000Z",
    items_count: 5,
    user: {
      id: 1,
      name: "John Doe",
      email: "john@example.com",
    },
  },
  {
    id: 2,
    name: "Home Appliances",
    description: "Products for my new home",
    is_public: false,
    user_id: 1,
    created_at: "2023-02-20T10:15:00.000Z",
    updated_at: "2023-02-20T10:15:00.000Z",
    items_count: 3,
    user: {
      id: 1,
      name: "John Doe",
      email: "john@example.com",
    },
  },
  {
    id: 3,
    name: "Gift Ideas",
    description: "Products to buy as gifts",
    is_public: true,
    user_id: 2,
    created_at: "2023-03-10T14:45:00.000Z",
    updated_at: "2023-03-10T14:45:00.000Z",
    items_count: 8,
    user: {
      id: 2,
      name: "Jane Smith",
      email: "jane@example.com",
    },
  },
]

export default function WishlistsPage() {
  const [wishlists, setWishlists] = useState<Wishlist[]>(WISHLISTS_DATA)
  const [searchQuery, setSearchQuery] = useState("")
  const [openDialog, setOpenDialog] = useState(false)
  const [selectedWishlist, setSelectedWishlist] = useState<Wishlist | null>(null)

  const filteredWishlists = wishlists.filter(
    (wishlist) =>
      wishlist.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      wishlist.description?.toLowerCase().includes(searchQuery.toLowerCase()) ||
      wishlist.user.name.toLowerCase().includes(searchQuery.toLowerCase()),
  )

  const handleCreate = () => {
    setSelectedWishlist(null)
    setOpenDialog(true)
  }

  const handleEdit = (wishlist: Wishlist) => {
    setSelectedWishlist(wishlist)
    setOpenDialog(true)
  }

  const handleSave = (wishlist: Wishlist) => {
    if (selectedWishlist) {
      // Update existing wishlist
      setWishlists(wishlists.map((w) => (w.id === wishlist.id ? { ...w, ...wishlist } : w)))
    } else {
      // Create new wishlist
      const newWishlist = {
        ...wishlist,
        id: Math.max(0, ...wishlists.map((w) => w.id)) + 1,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
        items_count: 0,
        user: {
          id: 1,
          name: "John Doe",
          email: "john@example.com",
        },
      }
      setWishlists([...wishlists, newWishlist])
    }
    setOpenDialog(false)
  }

  const handleDelete = (id: number) => {
    setWishlists(wishlists.filter((wishlist) => wishlist.id !== id))
  }

  return (
    <div className="space-y-4 p-8 pt-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight">Wishlists</h2>
        <Button onClick={handleCreate}>
          <Plus className="mr-2 h-4 w-4" />
          Add Wishlist
        </Button>
      </div>
      <div className="flex items-center py-4">
        <Input
          placeholder="Search wishlists..."
          className="max-w-sm"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
        />
      </div>
      <Card>
        <CardHeader>
          <CardTitle>All Wishlists</CardTitle>
          <CardDescription>Manage customer wishlists and their products.</CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Description</TableHead>
                <TableHead>User</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Items</TableHead>
                <TableHead>Created</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredWishlists.map((wishlist) => (
                <TableRow key={wishlist.id}>
                  <TableCell className="font-medium">{wishlist.name}</TableCell>
                  <TableCell>{wishlist.description}</TableCell>
                  <TableCell>{wishlist.user.name}</TableCell>
                  <TableCell>
                    <Badge variant={wishlist.is_public ? "default" : "outline"}>
                      {wishlist.is_public ? "Public" : "Private"}
                    </Badge>
                  </TableCell>
                  <TableCell>{wishlist.items_count}</TableCell>
                  <TableCell>{new Date(wishlist.created_at).toLocaleDateString()}</TableCell>
                  <TableCell>
                    <div className="flex space-x-2">
                      <Button variant="outline" size="sm" onClick={() => handleEdit(wishlist)}>
                        Edit
                      </Button>
                      <Button variant="destructive" size="sm" onClick={() => handleDelete(wishlist.id)}>
                        Delete
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      <WishlistDialog open={openDialog} onOpenChange={setOpenDialog} wishlist={selectedWishlist} onSave={handleSave} />
    </div>
  )
}
