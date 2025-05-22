"use client"

import type React from "react"

import { useState, useEffect } from "react"
import { Check, ChevronsUpDown } from "lucide-react"

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
import { Command, CommandList, CommandEmpty, CommandGroup, CommandInput, CommandItem } from "@/components/ui/command"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { Switch } from "@/components/ui/switch"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Textarea } from "@/components/ui/textarea"
import { useToast } from "@/components/ui/use-toast"
import { cn } from "@/lib/utils"
import { ContentEditor } from "@/components/editor/content-editor"

const modules = [
  { value: "user", label: "User" },
  { value: "product", label: "Product" },
  { value: "order", label: "Order" },
  { value: "cart", label: "Cart" },
  { value: "wishlist", label: "Wishlist" },
  { value: "coupon", label: "Coupon" },
  { value: "payment", label: "Payment" },
  { value: "shipping", label: "Shipping" },
  { value: "review", label: "Review" },
  { value: "system", label: "System" },
]

const triggers = [
  { value: "user.register", label: "User Registration", module: "user" },
  { value: "user.password_reset", label: "Password Reset", module: "user" },
  { value: "user.welcome", label: "Welcome Email", module: "user" },
  { value: "order.created", label: "Order Created", module: "order" },
  { value: "order.shipped", label: "Order Shipped", module: "order" },
  { value: "order.delivered", label: "Order Delivered", module: "order" },
  { value: "order.cancelled", label: "Order Cancelled", module: "order" },
  { value: "order.refunded", label: "Order Refunded", module: "order" },
  { value: "product.back_in_stock", label: "Product Back in Stock", module: "product" },
  { value: "product.price_drop", label: "Product Price Drop", module: "product" },
  { value: "cart.abandoned", label: "Abandoned Cart", module: "cart" },
  { value: "wishlist.price_drop", label: "Wishlist Item Price Drop", module: "wishlist" },
  { value: "coupon.new", label: "New Coupon", module: "coupon" },
  { value: "coupon.expiring", label: "Coupon Expiring", module: "coupon" },
  { value: "payment.success", label: "Payment Success", module: "payment" },
  { value: "payment.failed", label: "Payment Failed", module: "payment" },
  { value: "shipping.update", label: "Shipping Update", module: "shipping" },
  { value: "review.thank_you", label: "Review Thank You", module: "review" },
  { value: "system.maintenance", label: "System Maintenance", module: "system" },
]

interface EmailTemplateDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  template?: any
}

export function EmailTemplateDialog({ open, onOpenChange, template }: EmailTemplateDialogProps) {
  const { toast } = useToast()
  const [name, setName] = useState("")
  const [subject, setSubject] = useState("")
  const [body, setBody] = useState("")
  const [type, setType] = useState("transactional")
  const [module, setModule] = useState("")
  const [triggeredBy, setTriggeredBy] = useState("")
  const [isActive, setIsActive] = useState(true)
  const [openTriggerCombobox, setOpenTriggerCombobox] = useState(false)
  const [activeTab, setActiveTab] = useState("editor")

  // Filter triggers based on selected module
  const filteredTriggers = module ? triggers.filter((trigger) => trigger.module === module) : triggers

  useEffect(() => {
    if (template) {
      setName(template.name || "")
      setSubject(template.subject || "")
      setBody(template.body || "")
      setType(template.type || "transactional")
      setModule(template.module || "")
      setTriggeredBy(template.triggeredBy || "")
      setIsActive(template.isActive !== undefined ? template.isActive : true)
    } else {
      setName("")
      setSubject("")
      setBody("")
      setType("transactional")
      setModule("")
      setTriggeredBy("")
      setIsActive(true)
    }
  }, [template, open])

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    if (!name || !subject || !body || !type || !triggeredBy) {
      toast({
        title: "Validation Error",
        description: "Please fill in all required fields.",
        variant: "destructive",
      })
      return
    }

    const templateData = {
      id: template?.id || `temp-${Date.now()}`,
      name,
      subject,
      body,
      type,
      module,
      triggeredBy,
      isActive,
    }

    // In a real app, you would call an API to save the template
    toast({
      title: template ? "Template updated" : "Template created",
      description: `${name} has been ${template ? "updated" : "created"} successfully.`,
    })

    onOpenChange(false)
  }

  const handleModuleChange = (value: string) => {
    setModule(value)
    // Reset trigger if it doesn't belong to the selected module
    const currentTrigger = triggers.find((t) => t.value === triggeredBy)
    if (currentTrigger && currentTrigger.module !== value) {
      setTriggeredBy("")
    }
  }

  const insertVariable = (variable: string) => {
    setBody((prev) => prev + ` {{${variable}}}`)
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-3xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>{template ? "Edit Email Template" : "Create Email Template"}</DialogTitle>
          <DialogDescription>
            {template
              ? "Update the details of your email template."
              : "Create a new email template for your eCommerce store."}
          </DialogDescription>
        </DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div className="space-y-2">
              <Label htmlFor="name">Template Name</Label>
              <Input
                id="name"
                value={name}
                onChange={(e) => setName(e.target.value)}
                placeholder="e.g., Order Confirmation"
                required
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="subject">Email Subject</Label>
              <Input
                id="subject"
                value={subject}
                onChange={(e) => setSubject(e.target.value)}
                placeholder="e.g., Your order has been confirmed"
                required
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="type">Template Type</Label>
              <Select value={type} onValueChange={setType} required>
                <SelectTrigger id="type">
                  <SelectValue placeholder="Select template type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="transactional">Transactional</SelectItem>
                  <SelectItem value="marketing">Marketing</SelectItem>
                  <SelectItem value="notification">System Notification</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="module">Module</Label>
              <Select value={module} onValueChange={handleModuleChange} required>
                <SelectTrigger id="module">
                  <SelectValue placeholder="Select module" />
                </SelectTrigger>
                <SelectContent>
                  {modules.map((module) => (
                    <SelectItem key={module.value} value={module.value}>
                      {module.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="triggeredBy">Triggered By</Label>
              <Popover open={openTriggerCombobox} onOpenChange={setOpenTriggerCombobox}>
                <PopoverTrigger asChild>
                  <Button
                    variant="outline"
                    role="combobox"
                    aria-expanded={openTriggerCombobox}
                    className="w-full justify-between"
                  >
                    {triggeredBy
                      ? triggers.find((trigger) => trigger.value === triggeredBy)?.label
                      : "Select trigger event..."}
                    <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                  </Button>
                </PopoverTrigger>
                <PopoverContent className="w-full p-0">
                  <Command>
                    <CommandInput placeholder="Search trigger events..." />
                    <CommandList>
                      <CommandEmpty>No trigger event found.</CommandEmpty>
                      <CommandGroup>
                        {filteredTriggers.map((trigger) => (
                          <CommandItem
                            key={trigger.value}
                            value={trigger.value}
                            onSelect={(currentValue) => {
                              setTriggeredBy(currentValue === triggeredBy ? "" : currentValue)
                              setOpenTriggerCombobox(false)
                            }}
                          >
                            <Check
                              className={cn(
                                "mr-2 h-4 w-4",
                                triggeredBy === trigger.value ? "opacity-100" : "opacity-0",
                              )}
                            />
                            {trigger.label}
                          </CommandItem>
                        ))}
                      </CommandGroup>
                    </CommandList>
                  </Command>
                </PopoverContent>
              </Popover>
            </div>
            <div className="flex items-center space-x-2">
              <Switch id="isActive" checked={isActive} onCheckedChange={setIsActive} />
              <Label htmlFor="isActive">Active</Label>
            </div>
          </div>

          <div className="space-y-2">
            <div className="flex justify-between items-center">
              <Label htmlFor="body">Email Body</Label>
              <div className="flex space-x-2">
                <Button type="button" variant="outline" size="sm" onClick={() => insertVariable("user.name")}>
                  Add User Name
                </Button>
                <Button type="button" variant="outline" size="sm" onClick={() => insertVariable("order.id")}>
                  Add Order ID
                </Button>
                <Button type="button" variant="outline" size="sm" onClick={() => insertVariable("product.name")}>
                  Add Product Name
                </Button>
              </div>
            </div>

            <Tabs value={activeTab} onValueChange={setActiveTab}>
              <TabsList className="grid w-full grid-cols-2">
                <TabsTrigger value="editor">Visual Editor</TabsTrigger>
                <TabsTrigger value="html">HTML</TabsTrigger>
              </TabsList>
              <TabsContent value="editor" className="border rounded-md p-4 min-h-[300px]">
                <ContentEditor content={body} onChange={setBody} placeholder="Compose your email content here..." />
              </TabsContent>
              <TabsContent value="html">
                <Textarea
                  id="body"
                  value={body}
                  onChange={(e) => setBody(e.target.value)}
                  placeholder="Enter HTML content here..."
                  className="min-h-[300px] font-mono"
                />
              </TabsContent>
            </Tabs>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
              Cancel
            </Button>
            <Button type="submit">{template ? "Update Template" : "Create Template"}</Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  )
}
