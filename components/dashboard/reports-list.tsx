import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { FileText, Download, Eye, BarChart, PieChart, LineChart } from "lucide-react"
import { Badge } from "@/components/ui/badge"

export function ReportsList() {
  const reports = [
    {
      id: 1,
      title: "Monthly Sales Report",
      description: "Detailed breakdown of sales for the current month",
      date: "May 1, 2023",
      icon: BarChart,
      status: "Ready",
    },
    {
      id: 2,
      title: "Customer Acquisition Report",
      description: "Analysis of new customer acquisition channels",
      date: "April 28, 2023",
      icon: LineChart,
      status: "Ready",
    },
    {
      id: 3,
      title: "Product Performance Analysis",
      description: "Performance metrics for all products",
      date: "April 25, 2023",
      icon: PieChart,
      status: "Ready",
    },
    {
      id: 4,
      title: "Quarterly Financial Report",
      description: "Financial overview for Q1 2023",
      date: "April 15, 2023",
      icon: FileText,
      status: "Ready",
    },
    {
      id: 5,
      title: "Inventory Status Report",
      description: "Current inventory levels and restock recommendations",
      date: "April 10, 2023",
      icon: FileText,
      status: "Ready",
    },
  ]

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h3 className="text-lg font-medium">Available Reports</h3>
        <Button variant="outline">Generate New Report</Button>
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        {reports.map((report) => (
          <Card key={report.id}>
            <CardHeader className="flex flex-row items-start justify-between space-y-0 pb-2">
              <div>
                <CardTitle className="text-base">{report.title}</CardTitle>
                <CardDescription className="text-sm">{report.description}</CardDescription>
              </div>
              <report.icon className="h-5 w-5 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-sm text-muted-foreground">Generated: {report.date}</div>
              <Badge className="mt-2">{report.status}</Badge>
            </CardContent>
            <CardFooter className="flex justify-between">
              <Button variant="outline" size="sm">
                <Eye className="mr-2 h-4 w-4" />
                View
              </Button>
              <Button variant="outline" size="sm">
                <Download className="mr-2 h-4 w-4" />
                Download
              </Button>
            </CardFooter>
          </Card>
        ))}
      </div>
    </div>
  )
}
