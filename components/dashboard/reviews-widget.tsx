"use client"

import { useState } from "react"
import Link from "next/link"
import { Star } from "lucide-react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { reviews } from "@/lib/data/reviews"

export function ReviewsWidget() {
  const [activeTab, setActiveTab] = useState("recent")

  const pendingReviews = reviews.filter((review) => !review.approved && !review.rejected)
  const recentReviews = reviews.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
  const featuredReviews = reviews.filter((review) => review.featured)

  const renderStars = (rating: number) => {
    return (
      <div className="flex items-center">
        {Array.from({ length: 5 }).map((_, i) => (
          <Star key={i} className={`h-3 w-3 ${i < rating ? "text-yellow-400 fill-yellow-400" : "text-gray-300"}`} />
        ))}
      </div>
    )
  }

  return (
    <Card>
      <CardHeader className="pb-2">
        <div className="flex items-center justify-between">
          <div>
            <CardTitle>Reviews</CardTitle>
            <CardDescription>Manage customer product reviews</CardDescription>
          </div>
          <Link href="/reviews">
            <Button variant="outline" size="sm">
              View All
            </Button>
          </Link>
        </div>
      </CardHeader>
      <CardContent>
        <Tabs defaultValue="recent" onValueChange={setActiveTab}>
          <TabsList className="grid w-full grid-cols-3">
            <TabsTrigger value="recent">Recent</TabsTrigger>
            <TabsTrigger value="pending">
              Pending
              {pendingReviews.length > 0 && (
                <Badge variant="secondary" className="ml-2">
                  {pendingReviews.length}
                </Badge>
              )}
            </TabsTrigger>
            <TabsTrigger value="featured">Featured</TabsTrigger>
          </TabsList>
          <TabsContent value="recent" className="space-y-4 pt-4">
            {recentReviews.slice(0, 5).map((review) => (
              <div key={review.id} className="flex items-start space-x-4">
                <Avatar className="h-8 w-8">
                  <AvatarImage src={review.user.avatar || "/placeholder.svg"} alt={review.user.name} />
                  <AvatarFallback>{review.user.name.charAt(0)}</AvatarFallback>
                </Avatar>
                <div className="flex-1 space-y-1">
                  <div className="flex items-center justify-between">
                    <p className="text-sm font-medium">{review.user.name}</p>
                    <span className="text-xs text-muted-foreground">
                      {new Date(review.created_at).toLocaleDateString()}
                    </span>
                  </div>
                  <div className="flex items-center gap-2">
                    {renderStars(review.rating)}
                    <span className="text-xs text-muted-foreground">for {review.product.name}</span>
                  </div>
                  <p className="text-sm font-medium">{review.title}</p>
                  <p className="text-xs text-muted-foreground line-clamp-2">{review.comment}</p>
                </div>
              </div>
            ))}
          </TabsContent>
          <TabsContent value="pending" className="space-y-4 pt-4">
            {pendingReviews.length > 0 ? (
              pendingReviews.slice(0, 5).map((review) => (
                <div key={review.id} className="flex items-start space-x-4">
                  <Avatar className="h-8 w-8">
                    <AvatarImage src={review.user.avatar || "/placeholder.svg"} alt={review.user.name} />
                    <AvatarFallback>{review.user.name.charAt(0)}</AvatarFallback>
                  </Avatar>
                  <div className="flex-1 space-y-1">
                    <div className="flex items-center justify-between">
                      <p className="text-sm font-medium">{review.user.name}</p>
                      <span className="text-xs text-muted-foreground">
                        {new Date(review.created_at).toLocaleDateString()}
                      </span>
                    </div>
                    <div className="flex items-center gap-2">
                      {renderStars(review.rating)}
                      <span className="text-xs text-muted-foreground">for {review.product.name}</span>
                    </div>
                    <p className="text-sm font-medium">{review.title}</p>
                    <p className="text-xs text-muted-foreground line-clamp-2">{review.comment}</p>
                    <div className="flex gap-2 pt-1">
                      <Button size="sm" variant="outline" className="h-7 px-2 text-xs">
                        Approve
                      </Button>
                      <Button size="sm" variant="outline" className="h-7 px-2 text-xs text-red-500 hover:text-red-600">
                        Reject
                      </Button>
                    </div>
                  </div>
                </div>
              ))
            ) : (
              <div className="flex h-32 items-center justify-center">
                <p className="text-sm text-muted-foreground">No pending reviews</p>
              </div>
            )}
          </TabsContent>
          <TabsContent value="featured" className="space-y-4 pt-4">
            {featuredReviews.length > 0 ? (
              featuredReviews.slice(0, 5).map((review) => (
                <div key={review.id} className="flex items-start space-x-4">
                  <Avatar className="h-8 w-8">
                    <AvatarImage src={review.user.avatar || "/placeholder.svg"} alt={review.user.name} />
                    <AvatarFallback>{review.user.name.charAt(0)}</AvatarFallback>
                  </Avatar>
                  <div className="flex-1 space-y-1">
                    <div className="flex items-center justify-between">
                      <p className="text-sm font-medium">{review.user.name}</p>
                      <span className="text-xs text-muted-foreground">
                        {new Date(review.created_at).toLocaleDateString()}
                      </span>
                    </div>
                    <div className="flex items-center gap-2">
                      {renderStars(review.rating)}
                      <span className="text-xs text-muted-foreground">for {review.product.name}</span>
                    </div>
                    <p className="text-sm font-medium">{review.title}</p>
                    <p className="text-xs text-muted-foreground line-clamp-2">{review.comment}</p>
                    <div className="flex gap-2 pt-1">
                      <Badge variant="outline" className="bg-blue-50 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                        Featured
                      </Badge>
                    </div>
                  </div>
                </div>
              ))
            ) : (
              <div className="flex h-32 items-center justify-center">
                <p className="text-sm text-muted-foreground">No featured reviews</p>
              </div>
            )}
          </TabsContent>
        </Tabs>
      </CardContent>
    </Card>
  )
}
