import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { SalesChart } from "@/components/dashboard/sales-chart"

export function AnalyticsCharts() {
  return (
    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
      <Card className="col-span-4">
        <CardHeader>
          <CardTitle>Traffic Sources</CardTitle>
          <CardDescription>Where your visitors are coming from</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="h-[300px]">
            <Tabs defaultValue="weekly">
              <div className="flex items-center justify-between">
                <TabsList>
                  <TabsTrigger value="daily">Daily</TabsTrigger>
                  <TabsTrigger value="weekly">Weekly</TabsTrigger>
                  <TabsTrigger value="monthly">Monthly</TabsTrigger>
                </TabsList>
              </div>
              <TabsContent value="daily" className="space-y-4">
                <SalesChart />
              </TabsContent>
              <TabsContent value="weekly" className="space-y-4">
                <SalesChart />
              </TabsContent>
              <TabsContent value="monthly" className="space-y-4">
                <SalesChart />
              </TabsContent>
            </Tabs>
          </div>
        </CardContent>
      </Card>
      <Card className="col-span-3">
        <CardHeader>
          <CardTitle>Customer Demographics</CardTitle>
          <CardDescription>Age and gender distribution</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="h-[300px]">
            <Tabs defaultValue="age">
              <div className="flex items-center justify-between">
                <TabsList>
                  <TabsTrigger value="age">Age</TabsTrigger>
                  <TabsTrigger value="gender">Gender</TabsTrigger>
                  <TabsTrigger value="location">Location</TabsTrigger>
                </TabsList>
              </div>
              <TabsContent value="age" className="space-y-4">
                <SalesChart />
              </TabsContent>
              <TabsContent value="gender" className="space-y-4">
                <SalesChart />
              </TabsContent>
              <TabsContent value="location" className="space-y-4">
                <SalesChart />
              </TabsContent>
            </Tabs>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
