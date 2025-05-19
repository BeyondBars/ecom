"use client"

import type React from "react"

import { useState } from "react"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { Label } from "@/components/ui/label"
import { Input } from "@/components/ui/input"
import { Textarea } from "@/components/ui/textarea"
import { Checkbox } from "@/components/ui/checkbox"
import { Star } from "lucide-react"

interface ReviewDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  review: any | null
  onSave: (review: any) => void
}

export function ReviewDialog({ open, onOpenChange, review, onSave }: ReviewDialogProps) {
  const [formData, setFormData] = useState<any>(
    review || {
      id: "",
      product_id: "",
      user_id: "",
      rating: 5,
      title: "",
      comment: "",
      verified_purchase: false,
      approved: false,
      featured: false,
    },
  )

  const handleChange = (field: string, value: any) => {
    setFormData({
      ...formData,
      [field]: value,
    })
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    onSave(formData)
  }

  const handleRatingChange = (rating: number) => {
    setFormData({
      ...formData,
      rating,
    })
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[600px]">
        <form onSubmit={handleSubmit}>
          <DialogHeader>
            <DialogTitle>{review ? "Edit Review" : "Add Review"}</DialogTitle>
            <DialogDescription>
              {review
                ? "Make changes to the review here. Click save when you're done."
                : "Add a new review here. Click save when you're done."}
            </DialogDescription>
          </DialogHeader>
          <div className="grid gap-4 py-4">
            {review && (
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="product" className="text-right">
                    Product
                  </Label>
                  <Input id="product" value={review.product?.name || ""} className="mt-1" disabled />
                </div>
                <div>
                  <Label htmlFor="customer" className="text-right">
                    Customer
                  </Label>
                  <Input id="customer" value={review.user?.name || ""} className="mt-1" disabled />
                </div>
              </div>
            )}

            <div className="space-y-2">
              <Label htmlFor="rating">Rating</Label>
              <div className="flex items-center space-x-1">
                {[1, 2, 3, 4, 5].map((star) => (
                  <button
                    key={star}
                    type="button"
                    className="focus:outline-none"
                    onClick={() => handleRatingChange(star)}
                  >
                    <Star
                      className={`h-6 w-6 ${
                        star <= formData.rating ? "text-yellow-400 fill-yellow-400" : "text-gray-300"
                      }`}
                    />
                  </button>
                ))}
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="title">Review Title</Label>
              <Input id="title" value={formData.title} onChange={(e) => handleChange("title", e.target.value)} />
            </div>

            <div className="space-y-2">
              <Label htmlFor="comment">Review Comment</Label>
              <Textarea
                id="comment"
                rows={5}
                value={formData.comment}
                onChange={(e) => handleChange("comment", e.target.value)}
              />
            </div>

            <div className="space-y-4">
              <div className="flex items-center space-x-2">
                <Checkbox
                  id="verified_purchase"
                  checked={formData.verified_purchase}
                  onCheckedChange={(checked) => handleChange("verified_purchase", checked)}
                />
                <Label htmlFor="verified_purchase">Verified Purchase</Label>
              </div>

              <div className="flex items-center space-x-2">
                <Checkbox
                  id="approved"
                  checked={formData.approved}
                  onCheckedChange={(checked) => handleChange("approved", checked)}
                />
                <Label htmlFor="approved">Approved</Label>
              </div>

              <div className="flex items-center space-x-2">
                <Checkbox
                  id="featured"
                  checked={formData.featured}
                  onCheckedChange={(checked) => handleChange("featured", checked)}
                />
                <Label htmlFor="featured">Featured</Label>
              </div>
            </div>
          </div>
          <DialogFooter>
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
              Cancel
            </Button>
            <Button type="submit">Save</Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  )
}
