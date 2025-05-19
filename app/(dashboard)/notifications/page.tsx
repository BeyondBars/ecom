"use client"

import React from "react"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Bell, Check, Filter, RefreshCw, Trash2 } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import {
  type Notification,
  getNotifications,
  markAllAsRead,
  markAsRead,
  deleteNotification,
} from "@/lib/data/notifications"
import { Badge } from "@/components/ui/badge"
import { toast } from "@/components/ui/use-toast"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { ScrollArea } from "@/components/ui/scroll-area"

export default function NotificationsPage() {
  const router = useRouter()
  const [activeTab, setActiveTab] = useState("all")
  const [notifications, setNotifications] = useState<Notification[]>([])
  const [isLoading, setIsLoading] = useState(true)

  // Fetch notifications based on active tab
  const fetchNotifications = async () => {
    setIsLoading(true)
    try {
      const params: { type?: string; isRead?: boolean } = {}

      if (activeTab === "unread") {
        params.isRead = false
      } else if (activeTab === "read") {
        params.isRead = true
      } else if (activeTab !== "all") {
        params.type = activeTab
      }

      const response = await getNotifications(params)
      setNotifications(response.data)
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to fetch notifications",
        variant: "destructive",
      })
    } finally {
      setIsLoading(false)
    }
  }

  // Handle marking a notification as read
  const handleMarkAsRead = async (id: number) => {
    try {
      await markAsRead(id)
      fetchNotifications()
      toast({
        title: "Success",
        description: "Notification marked as read",
      })
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to mark notification as read",
        variant: "destructive",
      })
    }
  }

  // Handle marking all notifications as read
  const handleMarkAllAsRead = async () => {
    try {
      await markAllAsRead()
      fetchNotifications()
      toast({
        title: "Success",
        description: "All notifications marked as read",
      })
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to mark all notifications as read",
        variant: "destructive",
      })
    }
  }

  // Handle deleting a notification
  const handleDeleteNotification = async (id: number) => {
    try {
      await deleteNotification(id)
      fetchNotifications()
      toast({
        title: "Success",
        description: "Notification deleted",
      })
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to delete notification",
        variant: "destructive",
      })
    }
  }

  // Get icon based on notification type
  const getNotificationIcon = (type: string) => {
    switch (type) {
      case "info":
        return <Bell className="h-5 w-5 text-blue-500" />
      case "success":
        return <Check className="h-5 w-5 text-green-500" />
      case "warning":
        return <Bell className="h-5 w-5 text-yellow-500" />
      case "error":
        return <Bell className="h-5 w-5 text-red-500" />
      default:
        return <Bell className="h-5 w-5" />
    }
  }

  // Get badge color based on notification type
  const getNotificationBadgeVariant = (type: string): "default" | "secondary" | "destructive" | "outline" => {
    switch (type) {
      case "info":
        return "default"
      case "success":
        return "default"
      case "warning":
        return "secondary"
      case "error":
        return "destructive"
      default:
        return "outline"
    }
  }

  // Handle tab change
  const handleTabChange = (value: string) => {
    setActiveTab(value)
  }

  // Handle notification click
  const handleNotificationClick = (notification: Notification) => {
    if (!notification.isRead) {
      handleMarkAsRead(notification.id)
    }

    if (notification.link) {
      router.push(notification.link)
    }
  }

  // Fetch notifications when tab changes
  React.useEffect(() => {
    fetchNotifications()
  }, [activeTab])

  return (
    <div className="container mx-auto py-6">
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-3xl font-bold">Notifications</h1>
        <div className="flex items-center gap-2">
          <Button variant="outline" size="sm" onClick={fetchNotifications}>
            <RefreshCw className="h-4 w-4 mr-2" />
            Refresh
          </Button>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="outline" size="sm">
                <Filter className="h-4 w-4 mr-2" />
                Filter
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuItem onClick={() => handleTabChange("all")}>All</DropdownMenuItem>
              <DropdownMenuItem onClick={() => handleTabChange("unread")}>Unread</DropdownMenuItem>
              <DropdownMenuItem onClick={() => handleTabChange("read")}>Read</DropdownMenuItem>
              <DropdownMenuItem onClick={() => handleTabChange("info")}>Info</DropdownMenuItem>
              <DropdownMenuItem onClick={() => handleTabChange("success")}>Success</DropdownMenuItem>
              <DropdownMenuItem onClick={() => handleTabChange("warning")}>Warning</DropdownMenuItem>
              <DropdownMenuItem onClick={() => handleTabChange("error")}>Error</DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
          <Button variant="outline" size="sm" onClick={handleMarkAllAsRead}>
            <Check className="h-4 w-4 mr-2" />
            Mark All as Read
          </Button>
        </div>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Notifications</CardTitle>
          <CardDescription>View and manage your notifications</CardDescription>
        </CardHeader>
        <CardContent>
          <Tabs defaultValue="all" value={activeTab} onValueChange={handleTabChange}>
            <TabsList className="mb-4">
              <TabsTrigger value="all">All</TabsTrigger>
              <TabsTrigger value="unread">Unread</TabsTrigger>
              <TabsTrigger value="info">Info</TabsTrigger>
              <TabsTrigger value="success">Success</TabsTrigger>
              <TabsTrigger value="warning">Warning</TabsTrigger>
              <TabsTrigger value="error">Error</TabsTrigger>
            </TabsList>

            <TabsContent value={activeTab}>
              {isLoading ? (
                <div className="flex justify-center py-8">
                  <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                </div>
              ) : notifications.length === 0 ? (
                <div className="text-center py-8">
                  <Bell className="h-12 w-12 mx-auto text-gray-400" />
                  <p className="mt-2 text-gray-500">No notifications found</p>
                </div>
              ) : (
                <ScrollArea className="h-[500px]">
                  <div className="space-y-4">
                    {notifications.map((notification) => (
                      <div
                        key={notification.id}
                        className={`p-4 rounded-lg border ${
                          notification.isRead ? "bg-white" : "bg-gray-50"
                        } hover:bg-gray-100 transition-colors cursor-pointer`}
                        onClick={() => handleNotificationClick(notification)}
                      >
                        <div className="flex items-start gap-4">
                          <div className="mt-1">{getNotificationIcon(notification.type)}</div>
                          <div className="flex-1">
                            <div className="flex items-center justify-between">
                              <h3 className="font-medium">{notification.title}</h3>
                              <div className="flex items-center gap-2">
                                <Badge variant={getNotificationBadgeVariant(notification.type)}>
                                  {notification.type}
                                </Badge>
                                <Button
                                  variant="ghost"
                                  size="sm"
                                  onClick={(e) => {
                                    e.stopPropagation()
                                    handleDeleteNotification(notification.id)
                                  }}
                                >
                                  <Trash2 className="h-4 w-4 text-gray-500" />
                                </Button>
                              </div>
                            </div>
                            <p className="text-sm text-gray-500 mt-1">{notification.message}</p>
                            <div className="flex items-center justify-between mt-2">
                              <span className="text-xs text-gray-400">
                                {new Date(notification.createdAt).toLocaleString()}
                              </span>
                              {!notification.isRead && (
                                <Button
                                  variant="ghost"
                                  size="sm"
                                  onClick={(e) => {
                                    e.stopPropagation()
                                    handleMarkAsRead(notification.id)
                                  }}
                                >
                                  <Check className="h-4 w-4 mr-1" />
                                  Mark as read
                                </Button>
                              )}
                            </div>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                </ScrollArea>
              )}
            </TabsContent>
          </Tabs>
        </CardContent>
      </Card>
    </div>
  )
}
