"use client"

import type React from "react"

import { useState } from "react"
import { Plus, Search } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Badge } from "@/components/ui/badge"
import { Switch } from "@/components/ui/switch"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { format } from "date-fns"
import { Calendar } from "@/components/ui/calendar"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { cn } from "@/lib/utils"
import { CalendarIcon } from "lucide-react"

// Mock data for banners
const banners = [
  {
    id: "1",
    title: "Summer Sale Hero Banner",
    image: "/summer-sale-banner.png",
    page: "Homepage",
    position: "Top",
    type: "Promotional",
    url: "/summer-sale",
    startDate: new Date(2023, 5, 1),
    endDate: new Date(2023, 7, 31),
    status: "Active",
  },
  {
    id: "2",
    title: "New Arrivals Slider",
    image: "/new-arrivals-fashion.png",
    page: "Homepage",
    position: "Middle",
    type: "Informational",
    url: "/new-arrivals",
    startDate: new Date(2023, 4, 15),
    endDate: new Date(2023, 11, 31),
    status: "Active",
  },
  {
    id: "3",
    title: "Free Shipping Promotion",
    image: "/free-shipping-banner.png",
    page: "Checkout Page",
    position: "Top",
    type: "Informational",
    url: "/shipping-policy",
    startDate: new Date(2023, 0, 1),
    endDate: new Date(2023, 11, 31),
    status: "Active",
  },
  {
    id: "4",
    title: "Related Products Banner",
    image: "/related-products.png",
    page: "Product Page",
    position: "Bottom",
    type: "Cross-sell",
    url: "/recommended",
    startDate: new Date(2023, 3, 1),
    endDate: new Date(2023, 11, 31),
    status: "Active",
  },
  {
    id: "5",
    title: "Holiday Special Offer",
    image: "/placeholder.svg?height=600&width=1200&query=holiday+special+offer",
    page: "Homepage",
    position: "Top",
    type: "Promotional",
    url: "/holiday-special",
    startDate: new Date(2023, 10, 15),
    endDate: new Date(2023, 11, 31),
    status: "Inactive",
  },
  {
    id: "6",
    title: "Accessory Upsell",
    image: "/placeholder.svg?height=300&width=600&query=accessory+upsell",
    page: "Product Page",
    position: "Middle",
    type: "Upsell",
    url: "/accessories",
    startDate: new Date(2023, 0, 1),
    endDate: new Date(2023, 11, 31),
    status: "Active",
  },
  {
    id: "7",
    title: "Newsletter Signup",
    image: "/placeholder.svg?height=250&width=800&query=newsletter+signup",
    page: "All Pages",
    position: "Bottom",
    type: "Informational",
    url: "/newsletter",
    startDate: new Date(2023, 0, 1),
    endDate: new Date(2023, 11, 31),
    status: "Active",
  },
]

export default function BannersPage() {
  const [isDialogOpen, setIsDialogOpen] = useState(false)
  const [selectedBanner, setSelectedBanner] = useState<any>(null)
  const [pageFilter, setPageFilter] = useState("all")
  const [typeFilter, setTypeFilter] = useState("all")
  const [searchQuery, setSearchQuery] = useState("")

  // Filter banners based on selected filters and search query
  const filteredBanners = banners.filter((banner) => {
    const matchesPage = pageFilter === "all" || banner.page === pageFilter
    const matchesType = typeFilter === "all" || banner.type === typeFilter
    const matchesSearch = banner.title.toLowerCase().includes(searchQuery.toLowerCase())
    return matchesPage && matchesType && matchesSearch
  })

  const handleEditBanner = (banner: any) => {
    setSelectedBanner(banner)
    setIsDialogOpen(true)
  }

  const handleCreateBanner = () => {
    setSelectedBanner(null)
    setIsDialogOpen(true)
  }

  const handleSaveBanner = (e: React.FormEvent) => {
    e.preventDefault()
    // Save banner logic would go here
    setIsDialogOpen(false)
  }

  const handleDeleteBanner = (id: string) => {
    // Delete banner logic would go here
    console.log(`Deleting banner with ID: ${id}`)
  }

  const handleToggleStatus = (id: string, currentStatus: string) => {
    // Toggle status logic would go here
    console.log(`Toggling status for banner with ID: ${id} from ${currentStatus}`)
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold">Banner Management</h1>
        <Button onClick={handleCreateBanner}>
          <Plus className="mr-2 h-4 w-4" />
          Add New Banner
        </Button>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Banners</CardTitle>
          <CardDescription>Manage promotional banners and sliders across your store.</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex flex-col md:flex-row gap-4 mb-6">
            <div className="relative flex-1">
              <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder="Search banners..."
                className="pl-8"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
              />
            </div>
            <Select value={pageFilter} onValueChange={setPageFilter}>
              <SelectTrigger className="w-full md:w-[180px]">
                <SelectValue placeholder="Filter by page" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Pages</SelectItem>
                <SelectItem value="Homepage">Homepage</SelectItem>
                <SelectItem value="Product Page">Product Page</SelectItem>
                <SelectItem value="Category Page">Category Page</SelectItem>
                <SelectItem value="Checkout Page">Checkout Page</SelectItem>
                <SelectItem value="Cart Page">Cart Page</SelectItem>
              </SelectContent>
            </Select>
            <Select value={typeFilter} onValueChange={setTypeFilter}>
              <SelectTrigger className="w-full md:w-[180px]">
                <SelectValue placeholder="Filter by type" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Types</SelectItem>
                <SelectItem value="Promotional">Promotional</SelectItem>
                <SelectItem value="Informational">Informational</SelectItem>
                <SelectItem value="Upsell">Upsell</SelectItem>
                <SelectItem value="Cross-sell">Cross-sell</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div className="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Banner</TableHead>
                  <TableHead>Page & Position</TableHead>
                  <TableHead>Type</TableHead>
                  <TableHead>Schedule</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredBanners.map((banner) => (
                  <TableRow key={banner.id}>
                    <TableCell>
                      <div className="flex items-center gap-3">
                        <img
                          src={banner.image || "/placeholder.svg"}
                          alt={banner.title}
                          className="h-12 w-20 rounded object-cover"
                        />
                        <div>
                          <div className="font-medium">{banner.title}</div>
                          <div className="text-sm text-muted-foreground truncate max-w-[200px]">{banner.url}</div>
                        </div>
                      </div>
                    </TableCell>
                    <TableCell>
                      <div className="font-medium">{banner.page}</div>
                      <div className="text-sm text-muted-foreground">{banner.position}</div>
                    </TableCell>
                    <TableCell>
                      <Badge
                        variant={
                          banner.type === "Promotional"
                            ? "default"
                            : banner.type === "Informational"
                              ? "secondary"
                              : banner.type === "Upsell"
                                ? "outline"
                                : "destructive"
                        }
                      >
                        {banner.type}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      <div className="text-sm">
                        {format(banner.startDate, "MMM d, yyyy")} - {format(banner.endDate, "MMM d, yyyy")}
                      </div>
                    </TableCell>
                    <TableCell>
                      <Switch
                        checked={banner.status === "Active"}
                        onCheckedChange={() => handleToggleStatus(banner.id, banner.status)}
                        className={cn(
                          banner.status === "Active" ? "bg-green-500" : "bg-red-500",
                          "transition-colors duration-300",
                        )}
                      />
                    </TableCell>
                    <TableCell className="text-right">
                      <Button variant="ghost" size="sm" onClick={() => handleEditBanner(banner)}>
                        Edit
                      </Button>
                      <Button
                        variant="ghost"
                        size="sm"
                        className="text-red-500 hover:text-red-700 hover:bg-red-50"
                        onClick={() => handleDeleteBanner(banner.id)}
                      >
                        Delete
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        </CardContent>
      </Card>

      <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
        <DialogContent className="max-w-3xl">
          <DialogHeader>
            <DialogTitle>{selectedBanner ? "Edit Banner" : "Create New Banner"}</DialogTitle>
            <DialogDescription>
              {selectedBanner
                ? "Update the details of your existing banner."
                : "Add a new banner to display on your store."}
            </DialogDescription>
          </DialogHeader>
          <form onSubmit={handleSaveBanner} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-4">
                <div>
                  <Label htmlFor="title">Banner Title</Label>
                  <Input id="title" defaultValue={selectedBanner?.title || ""} required />
                </div>

                <div>
                  <Label htmlFor="page">Page</Label>
                  <Select defaultValue={selectedBanner?.page || "Homepage"}>
                    <SelectTrigger>
                      <SelectValue placeholder="Select page" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="Homepage">Homepage</SelectItem>
                      <SelectItem value="Product Page">Product Page</SelectItem>
                      <SelectItem value="Category Page">Category Page</SelectItem>
                      <SelectItem value="Checkout Page">Checkout Page</SelectItem>
                      <SelectItem value="Cart Page">Cart Page</SelectItem>
                      <SelectItem value="All Pages">All Pages</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div>
                  <Label htmlFor="position">Position</Label>
                  <Select defaultValue={selectedBanner?.position || "Top"}>
                    <SelectTrigger>
                      <SelectValue placeholder="Select position" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="Top">Top</SelectItem>
                      <SelectItem value="Middle">Middle</SelectItem>
                      <SelectItem value="Bottom">Bottom</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div>
                  <Label htmlFor="type">Banner Type</Label>
                  <Select defaultValue={selectedBanner?.type || "Promotional"}>
                    <SelectTrigger>
                      <SelectValue placeholder="Select type" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="Promotional">Promotional</SelectItem>
                      <SelectItem value="Informational">Informational</SelectItem>
                      <SelectItem value="Upsell">Upsell</SelectItem>
                      <SelectItem value="Cross-sell">Cross-sell</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div>
                  <Label htmlFor="url">Link URL</Label>
                  <Input id="url" defaultValue={selectedBanner?.url || ""} placeholder="e.g., /summer-sale" />
                </div>

                <div className="flex flex-col gap-2">
                  <Label>Schedule</Label>
                  <div className="grid grid-cols-2 gap-4">
                    <div className="flex flex-col space-y-1">
                      <Label htmlFor="startDate" className="text-xs">
                        Start Date
                      </Label>
                      <Popover>
                        <PopoverTrigger asChild>
                          <Button variant="outline" className="justify-start text-left font-normal">
                            <CalendarIcon className="mr-2 h-4 w-4" />
                            {selectedBanner?.startDate ? format(selectedBanner.startDate, "PPP") : "Select date"}
                          </Button>
                        </PopoverTrigger>
                        <PopoverContent className="w-auto p-0">
                          <Calendar mode="single" initialFocus />
                        </PopoverContent>
                      </Popover>
                    </div>
                    <div className="flex flex-col space-y-1">
                      <Label htmlFor="endDate" className="text-xs">
                        End Date
                      </Label>
                      <Popover>
                        <PopoverTrigger asChild>
                          <Button variant="outline" className="justify-start text-left font-normal">
                            <CalendarIcon className="mr-2 h-4 w-4" />
                            {selectedBanner?.endDate ? format(selectedBanner.endDate, "PPP") : "Select date"}
                          </Button>
                        </PopoverTrigger>
                        <PopoverContent className="w-auto p-0">
                          <Calendar mode="single" initialFocus />
                        </PopoverContent>
                      </Popover>
                    </div>
                  </div>
                </div>

                <div className="flex items-center space-x-2">
                  <Switch id="status" defaultChecked={selectedBanner?.status === "Active"} />
                  <Label htmlFor="status">Active</Label>
                </div>
              </div>

              <div className="space-y-4">
                <div>
                  <Label>Banner Image</Label>
                  <div className="mt-2 flex flex-col gap-4">
                    <div className="border rounded-md p-4">
                      <div className="text-sm text-muted-foreground mb-2">
                        {selectedBanner?.page === "Homepage" && selectedBanner?.position === "Top"
                          ? "Recommended size: 1920×600px (Hero Banner)"
                          : selectedBanner?.page === "Homepage"
                            ? "Recommended size: 1200×400px (Slider Banner)"
                            : "Recommended size: 800×300px (Standard Banner)"}
                      </div>
                      <div className="flex items-center justify-center border border-dashed rounded-md h-[200px] bg-muted/50">
                        {selectedBanner?.image ? (
                          <img
                            src={selectedBanner.image || "/placeholder.svg"}
                            alt="Banner preview"
                            className="max-h-full max-w-full object-contain"
                          />
                        ) : (
                          <div className="text-center">
                            <div className="text-muted-foreground">Drag and drop or click to upload</div>
                            <Button variant="outline" size="sm" className="mt-2">
                              Upload Image
                            </Button>
                          </div>
                        )}
                      </div>
                    </div>

                    <div>
                      <Label htmlFor="alt">Alt Text</Label>
                      <Input
                        id="alt"
                        placeholder="Descriptive text for accessibility"
                        defaultValue={selectedBanner?.alt || ""}
                      />
                    </div>

                    <div>
                      <Label htmlFor="description">Banner Description (Optional)</Label>
                      <Textarea
                        id="description"
                        placeholder="Brief description of this banner"
                        className="min-h-[100px]"
                        defaultValue={selectedBanner?.description || ""}
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <DialogFooter>
              <Button type="button" variant="outline" onClick={() => setIsDialogOpen(false)}>
                Cancel
              </Button>
              <Button type="submit">{selectedBanner ? "Update Banner" : "Create Banner"}</Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  )
}
