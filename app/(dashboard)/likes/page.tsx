"use client"

import { useState } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import type { Like } from "@/lib/data/likes"

const LIKES_DATA: Like[] = [
  {
    id: 1,
    user_id: 1,
    likeable_id: 101,
    likeable_type: "App\\Models\\Product",
    created_at: "2023-01-15T08:30:00.000Z",
    updated_at: "2023-01-15T08:30:00.000Z",
    user: {
      id: 1,
      name: "John Doe",
      email: "john@example.com",
    },
    likeable: {
      id: 101,
      name: "Smartphone X",
      type: "product",
    },
  },
  {
    id: 2,
    user_id: 1,
    likeable_id: 201,
    likeable_type: "App\\Models\\BlogPost",
    created_at: "2023-02-20T10:15:00.000Z",
    updated_at: "2023-02-20T10:15:00.000Z",
    user: {
      id: 1,
      name: "John Doe",
      email: "john@example.com",
    },
    likeable: {
      id: 201,
      name: "Top 10 Tech Trends",
      type: "blog_post",
    },
  },
  {
    id: 3,
    user_id: 2,
    likeable_id: 102,
    likeable_type: "App\\Models\\Product",
    created_at: "2023-03-10T14:45:00.000Z",
    updated_at: "2023-03-10T14:45:00.000Z",
    user: {
      id: 2,
      name: "Jane Smith",
      email: "jane@example.com",
    },
    likeable: {
      id: 102,
      name: "Wireless Headphones",
      type: "product",
    },
  },
]

export default function LikesPage() {
  const [likes, setLikes] = useState<Like[]>(LIKES_DATA)
  const [searchQuery, setSearchQuery] = useState("")
  const [typeFilter, setTypeFilter] = useState<string>("all")

  const filteredLikes = likes.filter(
    (like) =>
      (typeFilter === "all" ||
        (typeFilter === "product" && like.likeable_type.includes("Product")) ||
        (typeFilter === "blog_post" && like.likeable_type.includes("BlogPost"))) &&
      (like.user.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        like.likeable.name.toLowerCase().includes(searchQuery.toLowerCase())),
  )

  const handleDelete = (id: number) => {
    setLikes(likes.filter((like) => like.id !== id))
  }

  const getLikeableTypeLabel = (type: string) => {
    if (type.includes("Product")) return "Product"
    if (type.includes("BlogPost")) return "Blog Post"
    return type
  }

  return (
    <div className="space-y-4 p-8 pt-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight">Likes</h2>
      </div>
      <div className="flex items-center gap-4 py-4">
        <Input
          placeholder="Search likes..."
          className="max-w-sm"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
        />
        <Select value={typeFilter} onValueChange={setTypeFilter}>
          <SelectTrigger className="w-[180px]">
            <SelectValue placeholder="Filter by type" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">All Types</SelectItem>
            <SelectItem value="product">Products</SelectItem>
            <SelectItem value="blog_post">Blog Posts</SelectItem>
          </SelectContent>
        </Select>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>All Likes</CardTitle>
          <CardDescription>Manage user likes on products and blog posts.</CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>User</TableHead>
                <TableHead>Item</TableHead>
                <TableHead>Type</TableHead>
                <TableHead>Date</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredLikes.map((like) => (
                <TableRow key={like.id}>
                  <TableCell className="font-medium">{like.user.name}</TableCell>
                  <TableCell>{like.likeable.name}</TableCell>
                  <TableCell>
                    <Badge variant={like.likeable_type.includes("Product") ? "default" : "secondary"}>
                      {getLikeableTypeLabel(like.likeable_type)}
                    </Badge>
                  </TableCell>
                  <TableCell>{new Date(like.created_at).toLocaleDateString()}</TableCell>
                  <TableCell>
                    <Button variant="destructive" size="sm" onClick={() => handleDelete(like.id)}>
                      Delete
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  )
}
