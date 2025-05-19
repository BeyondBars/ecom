import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { DollarSign, ShoppingCart, Package, Users, CreditCard } from "lucide-react"
import { SalesChart } from "@/components/dashboard/sales-chart"
import { RecentOrders } from "@/components/dashboard/recent-orders"
import { TopProducts } from "@/components/dashboard/top-products"
import { AnalyticsOverview } from "@/components/dashboard/analytics-overview"
import { AnalyticsCharts } from "@/components/dashboard/analytics-charts"
import { ReportsList } from "@/components/dashboard/reports-list"
import { NotificationsList } from "@/components/dashboard/notifications-list"

export default function DashboardPage() {
  return (
    <div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight">Dashboard</h2>
      </div>
      <Tabs defaultValue="overview" className="space-y-4">
        <TabsList>
          <TabsTrigger value="overview">Overview</TabsTrigger>
          <TabsTrigger value="analytics">Analytics</TabsTrigger>
          <TabsTrigger value="reports">Reports</TabsTrigger>
          <TabsTrigger value="notifications">Notifications</TabsTrigger>
        </TabsList>
        <TabsContent value="overview" className="space-y-4">
          <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Total Revenue</CardTitle>
                <DollarSign className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">$45,231.89</div>
                <p className="text-xs text-muted-foreground">+20.1% from last month</p>
              </CardContent>
            </Card>
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Total Orders</CardTitle>
                <ShoppingCart className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">+2350</div>
                <p className="text-xs text-muted-foreground">+10.5% from last month</p>
              </CardContent>
            </Card>
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Total Products</CardTitle>
                <Package className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">1,245</div>
                <p className="text-xs text-muted-foreground">+12.3% from last month</p>
              </CardContent>
            </Card>
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Active Customers</CardTitle>
                <Users className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">+573</div>
                <p className="text-xs text-muted-foreground">+7.4% from last month</p>
              </CardContent>
            </Card>
          </div>
          <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
            <Card className="col-span-4">
              <CardHeader>
                <CardTitle>Sales Overview</CardTitle>
              </CardHeader>
              <CardContent className="pl-2">
                <SalesChart />
              </CardContent>
            </Card>
            <Card className="col-span-3">
              <CardHeader>
                <CardTitle>Recent Orders</CardTitle>
              </CardHeader>
              <CardContent>
                <RecentOrders />
              </CardContent>
            </Card>
          </div>
          <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
            <Card className="col-span-3">
              <CardHeader>
                <CardTitle>Top Products</CardTitle>
              </CardHeader>
              <CardContent>
                <TopProducts />
              </CardContent>
            </Card>
            <Card className="col-span-4">
              <CardHeader>
                <CardTitle>Recent Sales</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-8">
                  <div className="flex items-center">
                    <div className="flex items-center justify-center rounded-full bg-gray-100 p-2 dark:bg-gray-800">
                      <CreditCard className="h-5 w-5 text-gray-600 dark:text-gray-400" />
                    </div>
                    <div className="ml-4 space-y-1">
                      <p className="text-sm font-medium leading-none">John Doe purchased iPhone 13 Pro</p>
                      <p className="text-sm text-muted-foreground">5 minutes ago</p>
                    </div>
                    <div className="ml-auto font-medium">+$1,999.00</div>
                  </div>
                  <div className="flex items-center">
                    <div className="flex items-center justify-center rounded-full bg-gray-100 p-2 dark:bg-gray-800">
                      <CreditCard className="h-5 w-5 text-gray-600 dark:text-gray-400" />
                    </div>
                    <div className="ml-4 space-y-1">
                      <p className="text-sm font-medium leading-none">Jane Smith purchased MacBook Pro</p>
                      <p className="text-sm text-muted-foreground">12 minutes ago</p>
                    </div>
                    <div className="ml-auto font-medium">+$2,499.00</div>
                  </div>
                  <div className="flex items-center">
                    <div className="flex items-center justify-center rounded-full bg-gray-100 p-2 dark:bg-gray-800">
                      <CreditCard className="h-5 w-5 text-gray-600 dark:text-gray-400" />
                    </div>
                    <div className="ml-4 space-y-1">
                      <p className="text-sm font-medium leading-none">Robert Johnson purchased AirPods Pro</p>
                      <p className="text-sm text-muted-foreground">42 minutes ago</p>
                    </div>
                    <div className="ml-auto font-medium">+$249.00</div>
                  </div>
                  <div className="flex items-center">
                    <div className="flex items-center justify-center rounded-full bg-gray-100 p-2 dark:bg-gray-800">
                      <CreditCard className="h-5 w-5 text-gray-600 dark:text-gray-400" />
                    </div>
                    <div className="ml-4 space-y-1">
                      <p className="text-sm font-medium leading-none">Emily Davis purchased iPad Mini</p>
                      <p className="text-sm text-muted-foreground">1 hour ago</p>
                    </div>
                    <div className="ml-auto font-medium">+$499.00</div>
                  </div>
                  <div className="flex items-center">
                    <div className="flex items-center justify-center rounded-full bg-gray-100 p-2 dark:bg-gray-800">
                      <CreditCard className="h-5 w-5 text-gray-600 dark:text-gray-400" />
                    </div>
                    <div className="ml-4 space-y-1">
                      <p className="text-sm font-medium leading-none">Michael Wilson purchased Apple Watch</p>
                      <p className="text-sm text-muted-foreground">3 hours ago</p>
                    </div>
                    <div className="ml-auto font-medium">+$399.00</div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <TabsContent value="analytics" className="space-y-4">
          <AnalyticsOverview />
          <AnalyticsCharts />
        </TabsContent>

        <TabsContent value="reports" className="space-y-4">
          <ReportsList />
        </TabsContent>

        <TabsContent value="notifications" className="space-y-4">
          <NotificationsList />
        </TabsContent>
      </Tabs>
    </div>
  )
}
