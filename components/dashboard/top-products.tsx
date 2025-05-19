"use client"

import { formatCurrency } from "@/lib/utils"
import { ScrollArea } from "@/components/ui/scroll-area"
import { Progress } from "@/components/ui/progress"

// Mock data for top products
const topProducts = [
  {
    id: "1",
    name: "iPhone 13 Pro",
    sales: 120,
    revenue: 119998.8,
    growth: 15,
  },
  {
    id: "3",
    name: 'MacBook Pro 14"',
    sales: 85,
    revenue: 169999.15,
    growth: 22,
  },
  {
    id: "2",
    name: "Samsung Galaxy S22",
    sales: 78,
    revenue: 70199.22,
    growth: 10,
  },
  {
    id: "5",
    name: "iPad Air",
    sales: 65,
    revenue: 38999.35,
    growth: 8,
  },
  {
    id: "4",
    name: "Sony WH-1000XM4",
    sales: 52,
    revenue: 18199.48,
    growth: 5,
  },
]

export function TopProducts() {
  // Calculate the maximum sales for scaling
  const maxSales = Math.max(...topProducts.map((product) => product.sales))

  return (
    <ScrollArea className="h-[300px]">
      <div className="space-y-6">
        {topProducts.map((product) => (
          <div key={product.id} className="space-y-2">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium leading-none">{product.name}</p>
                <p className="text-sm text-muted-foreground">{product.sales} units sold</p>
              </div>
              <div className="text-right">
                <p className="text-sm font-medium">{formatCurrency(product.revenue)}</p>
                <p className={`text-xs ${product.growth > 0 ? "text-green-500" : "text-red-500"}`}>
                  {product.growth > 0 ? "+" : ""}
                  {product.growth}%
                </p>
              </div>
            </div>
            <Progress value={(product.sales / maxSales) * 100} className="h-2" />
          </div>
        ))}
      </div>
    </ScrollArea>
  )
}
