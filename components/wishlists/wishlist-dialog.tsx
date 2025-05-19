"use client"

import { useState } from "react"
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
import { Switch } from "@/components/ui/switch"
import type { Wishlist } from "@/lib/data/wishlists"

interface WishlistDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  wishlist: Wishlist | null
  onSave: (wishlist: Wishlist) => void
}

export default function WishlistDialog({ open, onOpenChange, wishlist, onSave }: WishlistDialogProps) {
  const [name, setName] = useState(wishlist?.name || "")
  const [description, setDescription] = useState(wishlist?.description || "")
  const [isPublic, setIsPublic] = useState(wishlist?.is_public || false)

  const handleSave = () => {
    if (!name.trim()) return

    onSave({
      id: wishlist?.id || 0,
      name,
      description,
      is_public: isPublic,
      user_id: wishlist?.user_id || 1,
      created_at: wishlist?.created_at || new Date().toISOString(),
      updated_at: new Date().toISOString(),
      items_count: wishlist?.items_count || 0,
      user: wishlist?.user || {
        id: 1,
        name: "John Doe",
        email: "john@example.com",
      },
    })

    // Reset form
    setName("")
    setDescription("")
    setIsPublic(false)
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>{wishlist ? "Edit Wishlist" : "Create Wishlist"}</DialogTitle>
          <DialogDescription>
            {wishlist ? "Update the wishlist details below." : "Fill in the details to create a new wishlist."}
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
              placeholder="My Wishlist"
              required
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
              placeholder="A description of your wishlist"
            />
          </div>
          <div className="grid grid-cols-4 items-center gap-4">
            <Label htmlFor="is-public" className="text-right">
              Public
            </Label>
            <div className="col-span-3 flex items-center space-x-2">
              <Switch id="is-public" checked={isPublic} onCheckedChange={setIsPublic} />
              <Label htmlFor="is-public">{isPublic ? "Public" : "Private"}</Label>
            </div>
          </div>
        </div>
        <DialogFooter>
          <Button type="submit" onClick={handleSave}>
            Save
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}
