"use client"

import type React from "react"

import { useState, useEffect } from "react"
import { CalendarIcon } from "lucide-react"
import { format } from "date-fns"
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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Switch } from "@/components/ui/switch"
import { Calendar } from "@/components/ui/calendar"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { cn } from "@/lib/utils"

interface CouponDialogProps {
  open: boolean
  onOpenChange: (coupon?: any) => void
  coupon?: any
}

export function CouponDialog({ open, onOpenChange, coupon }: CouponDialogProps) {
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [formData, setFormData] = useState({
    code: "",
    discountType: "percentage",
    discountValue: "",
    minimumOrderAmount: "",
    startDate: new Date(),
    expiryDate: new Date(new Date().setMonth(new Date().getMonth() + 1)),
    usageLimit: "",
    status: "active",
  })

  // Initialize form data when editing an existing coupon
  useEffect(() => {
    if (coupon) {
      setFormData({
        code: coupon.code || "",
        discountType: coupon.discountType || "percentage",
        discountValue: coupon.discountValue?.toString() || "",
        minimumOrderAmount: coupon.minimumOrderAmount?.toString() || "",
        startDate: new Date(coupon.startDate) || new Date(),
        expiryDate: new Date(coupon.expiryDate) || new Date(new Date().setMonth(new Date().getMonth() + 1)),
        usageLimit: coupon.usageLimit?.toString() || "",
        status: coupon.status || "active",
      })
    } else {
      // Reset form data when creating a new coupon
      setFormData({
        code: "",
        discountType: "percentage",
        discountValue: "",
        minimumOrderAmount: "",
        startDate: new Date(),
        expiryDate: new Date(new Date().setMonth(new Date().getMonth() + 1)),
        usageLimit: "",
        status: "active",
      })
    }
  }, [coupon])

  const handleChange = (field: string, value: any) => {
    setFormData((prev) => ({ ...prev, [field]: value }))
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSubmitting(true)

    try {
      // In a real app, this would call an API to save the coupon
      console.log("Saving coupon:", formData)

      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 1000))

      // Close the dialog and pass the saved coupon
      onOpenChange({
        ...coupon,
        ...formData,
        discountValue: Number.parseFloat(formData.discountValue),
        minimumOrderAmount: Number.parseFloat(formData.minimumOrderAmount),
        usageLimit: formData.usageLimit ? Number.parseInt(formData.usageLimit) : null,
      })
    } catch (error) {
      console.error("Error saving coupon:", error)
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <Dialog open={open} onOpenChange={() => onOpenChange()}>
      <DialogContent className="sm:max-w-[550px]">
        <form onSubmit={handleSubmit}>
          <DialogHeader>
            <DialogTitle>{coupon ? "Edit Coupon" : "Create Coupon"}</DialogTitle>
            <DialogDescription>
              {coupon ? "Update the details of this coupon." : "Create a new discount coupon for your store."}
            </DialogDescription>
          </DialogHeader>
          <div className="grid gap-4 py-4">
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="code" className="text-right">
                Code
              </Label>
              <Input
                id="code"
                value={formData.code}
                onChange={(e) => handleChange("code", e.target.value)}
                className="col-span-3"
                required
              />
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="discountType" className="text-right">
                Discount Type
              </Label>
              <Select value={formData.discountType} onValueChange={(value) => handleChange("discountType", value)}>
                <SelectTrigger id="discountType" className="col-span-3">
                  <SelectValue placeholder="Select discount type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="percentage">Percentage</SelectItem>
                  <SelectItem value="fixed">Fixed Amount</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="discountValue" className="text-right">
                {formData.discountType === "percentage" ? "Percentage" : "Amount"}
              </Label>
              <div className="col-span-3 relative">
                <Input
                  id="discountValue"
                  type="number"
                  value={formData.discountValue}
                  onChange={(e) => handleChange("discountValue", e.target.value)}
                  className={formData.discountType === "percentage" ? "pr-8" : "pl-8"}
                  min={0}
                  max={formData.discountType === "percentage" ? 100 : undefined}
                  step={formData.discountType === "percentage" ? 1 : 0.01}
                  required
                />
                <span className="absolute right-3 top-1/2 -translate-y-1/2">
                  {formData.discountType === "percentage" ? "%" : ""}
                </span>
                <span className="absolute left-3 top-1/2 -translate-y-1/2">
                  {formData.discountType === "fixed" ? "$" : ""}
                </span>
              </div>
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="minimumOrderAmount" className="text-right">
                Min. Order
              </Label>
              <div className="col-span-3 relative">
                <Input
                  id="minimumOrderAmount"
                  type="number"
                  value={formData.minimumOrderAmount}
                  onChange={(e) => handleChange("minimumOrderAmount", e.target.value)}
                  className="pl-8"
                  min={0}
                  step={0.01}
                />
                <span className="absolute left-3 top-1/2 -translate-y-1/2">$</span>
              </div>
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label className="text-right">Start Date</Label>
              <Popover>
                <PopoverTrigger asChild>
                  <Button
                    variant={"outline"}
                    className={cn(
                      "col-span-3 justify-start text-left font-normal",
                      !formData.startDate && "text-muted-foreground",
                    )}
                  >
                    <CalendarIcon className="mr-2 h-4 w-4" />
                    {formData.startDate ? format(formData.startDate, "PPP") : <span>Pick a date</span>}
                  </Button>
                </PopoverTrigger>
                <PopoverContent className="w-auto p-0">
                  <Calendar
                    mode="single"
                    selected={formData.startDate}
                    onSelect={(date) => handleChange("startDate", date)}
                    initialFocus
                  />
                </PopoverContent>
              </Popover>
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label className="text-right">Expiry Date</Label>
              <Popover>
                <PopoverTrigger asChild>
                  <Button
                    variant={"outline"}
                    className={cn(
                      "col-span-3 justify-start text-left font-normal",
                      !formData.expiryDate && "text-muted-foreground",
                    )}
                  >
                    <CalendarIcon className="mr-2 h-4 w-4" />
                    {formData.expiryDate ? format(formData.expiryDate, "PPP") : <span>Pick a date</span>}
                  </Button>
                </PopoverTrigger>
                <PopoverContent className="w-auto p-0">
                  <Calendar
                    mode="single"
                    selected={formData.expiryDate}
                    onSelect={(date) => handleChange("expiryDate", date)}
                    initialFocus
                  />
                </PopoverContent>
              </Popover>
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="usageLimit" className="text-right">
                Usage Limit
              </Label>
              <Input
                id="usageLimit"
                type="number"
                value={formData.usageLimit}
                onChange={(e) => handleChange("usageLimit", e.target.value)}
                className="col-span-3"
                min={0}
                placeholder="Leave empty for unlimited"
              />
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="status" className="text-right">
                Active
              </Label>
              <div className="col-span-3 flex items-center space-x-2">
                <Switch
                  id="status"
                  checked={formData.status === "active"}
                  onCheckedChange={(checked) => handleChange("status", checked ? "active" : "inactive")}
                />
                <span>{formData.status === "active" ? "Active" : "Inactive"}</span>
              </div>
            </div>
          </div>
          <DialogFooter>
            <Button type="button" variant="outline" onClick={() => onOpenChange()}>
              Cancel
            </Button>
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting ? "Saving..." : coupon ? "Update" : "Create"}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  )
}
