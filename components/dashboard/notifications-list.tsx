import { Card, CardContent } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { ShoppingCart, Package, AlertCircle, CheckCircle, User, Settings } from "lucide-react"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"

export function NotificationsList() {
  const notifications = [
    {
      id: 1,
      title: "New Order #1234",
      description: "John Doe placed a new order for $129.99",
      time: "5 minutes ago",
      type: "order",
      read: false,
      icon: ShoppingCart,
    },
    {
      id: 2,
      title: "Low Stock Alert",
      description: "iPhone 13 Pro is running low on stock (5 remaining)",
      time: "1 hour ago",
      type: "inventory",
      read: false,
      icon: Package,
    },
    {
      id: 3,
      title: "Payment Failed",
      description: "Payment for order #1230 has failed",
      time: "2 hours ago",
      type: "payment",
      read: false,
      icon: AlertCircle,
    },
    {
      id: 4,
      title: "New User Registration",
      description: "Jane Smith has registered a new account",
      time: "3 hours ago",
      type: "user",
      read: true,
      icon: User,
    },
    {
      id: 5,
      title: "Order Fulfilled",
      description: "Order #1229 has been shipped",
      time: "5 hours ago",
      type: "order",
      read: true,
      icon: CheckCircle,
    },
    {
      id: 6,
      title: "System Update",
      description: "System will undergo maintenance at 2:00 AM",
      time: "1 day ago",
      type: "system",
      read: true,
      icon: Settings,
    },
  ]

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h3 className="text-lg font-medium">Notifications</h3>
        <Button variant="outline">Mark All as Read</Button>
      </div>

      <Tabs defaultValue="all" className="space-y-4">
        <TabsList>
          <TabsTrigger value="all">All</TabsTrigger>
          <TabsTrigger value="unread">Unread</TabsTrigger>
          <TabsTrigger value="orders">Orders</TabsTrigger>
          <TabsTrigger value="system">System</TabsTrigger>
        </TabsList>

        <TabsContent value="all">
          <Card>
            <CardContent className="p-0">
              <div className="divide-y">
                {notifications.map((notification) => (
                  <div
                    key={notification.id}
                    className={`flex items-start p-4 ${notification.read ? "opacity-70" : ""}`}
                  >
                    <div
                      className={`flex h-10 w-10 items-center justify-center rounded-full ${notification.read ? "bg-muted" : "bg-primary/10"}`}
                    >
                      <notification.icon
                        className={`h-5 w-5 ${notification.read ? "text-muted-foreground" : "text-primary"}`}
                      />
                    </div>
                    <div className="ml-4 flex-1">
                      <div className="flex items-center justify-between">
                        <p className="text-sm font-medium">{notification.title}</p>
                        {!notification.read && <Badge variant="outline">New</Badge>}
                      </div>
                      <p className="text-sm text-muted-foreground">{notification.description}</p>
                      <p className="mt-1 text-xs text-muted-foreground">{notification.time}</p>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="unread">
          <Card>
            <CardContent className="p-0">
              <div className="divide-y">
                {notifications
                  .filter((n) => !n.read)
                  .map((notification) => (
                    <div key={notification.id} className="flex items-start p-4">
                      <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                        <notification.icon className="h-5 w-5 text-primary" />
                      </div>
                      <div className="ml-4 flex-1">
                        <div className="flex items-center justify-between">
                          <p className="text-sm font-medium">{notification.title}</p>
                          <Badge variant="outline">New</Badge>
                        </div>
                        <p className="text-sm text-muted-foreground">{notification.description}</p>
                        <p className="mt-1 text-xs text-muted-foreground">{notification.time}</p>
                      </div>
                    </div>
                  ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="orders">
          <Card>
            <CardContent className="p-0">
              <div className="divide-y">
                {notifications
                  .filter((n) => n.type === "order")
                  .map((notification) => (
                    <div
                      key={notification.id}
                      className={`flex items-start p-4 ${notification.read ? "opacity-70" : ""}`}
                    >
                      <div
                        className={`flex h-10 w-10 items-center justify-center rounded-full ${notification.read ? "bg-muted" : "bg-primary/10"}`}
                      >
                        <notification.icon
                          className={`h-5 w-5 ${notification.read ? "text-muted-foreground" : "text-primary"}`}
                        />
                      </div>
                      <div className="ml-4 flex-1">
                        <div className="flex items-center justify-between">
                          <p className="text-sm font-medium">{notification.title}</p>
                          {!notification.read && <Badge variant="outline">New</Badge>}
                        </div>
                        <p className="text-sm text-muted-foreground">{notification.description}</p>
                        <p className="mt-1 text-xs text-muted-foreground">{notification.time}</p>
                      </div>
                    </div>
                  ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="system">
          <Card>
            <CardContent className="p-0">
              <div className="divide-y">
                {notifications
                  .filter((n) => n.type === "system")
                  .map((notification) => (
                    <div
                      key={notification.id}
                      className={`flex items-start p-4 ${notification.read ? "opacity-70" : ""}`}
                    >
                      <div
                        className={`flex h-10 w-10 items-center justify-center rounded-full ${notification.read ? "bg-muted" : "bg-primary/10"}`}
                      >
                        <notification.icon
                          className={`h-5 w-5 ${notification.read ? "text-muted-foreground" : "text-primary"}`}
                        />
                      </div>
                      <div className="ml-4 flex-1">
                        <div className="flex items-center justify-between">
                          <p className="text-sm font-medium">{notification.title}</p>
                          {!notification.read && <Badge variant="outline">New</Badge>}
                        </div>
                        <p className="text-sm text-muted-foreground">{notification.description}</p>
                        <p className="mt-1 text-xs text-muted-foreground">{notification.time}</p>
                      </div>
                    </div>
                  ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  )
}
