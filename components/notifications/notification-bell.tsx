"use client"

import { useState, useEffect } from "react"
import { Bell } from "lucide-react"
import { Button } from "@/components/ui/button"
import { getUnreadCount } from "@/lib/data/notifications"
import { useRouter } from "next/navigation"

export function NotificationBell() {
  const router = useRouter()
  const [unreadCount, setUnreadCount] = useState(0)

  useEffect(() => {
    const fetchUnreadCount = async () => {
      try {
        const response = await getUnreadCount()
        setUnreadCount(response.count)
      } catch (error) {
        console.error("Failed to fetch unread count:", error)
      }
    }

    fetchUnreadCount()

    // Set up polling to check for new notifications
    const interval = setInterval(fetchUnreadCount, 60000) // Check every minute

    return () => clearInterval(interval)
  }, [])

  return (
    <Button
      variant="ghost"
      size="icon"
      className="relative"
      onClick={() => router.push("/notifications")}
      aria-label={`${unreadCount} unread notifications`}
    >
      <Bell className="h-5 w-5" />
      {unreadCount > 0 && (
        <span className="absolute top-0 right-0 h-4 w-4 rounded-full bg-red-500 text-[10px] font-medium text-white flex items-center justify-center">
          {unreadCount > 9 ? "9+" : unreadCount}
        </span>
      )}
    </Button>
  )
}
