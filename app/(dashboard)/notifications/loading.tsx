import { Skeleton } from "@/components/ui/skeleton"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"

export default function NotificationsLoading() {
  return (
    <div className="container mx-auto py-6">
      <div className="flex items-center justify-between mb-6">
        <Skeleton className="h-10 w-48" />
        <div className="flex items-center gap-2">
          <Skeleton className="h-9 w-24" />
          <Skeleton className="h-9 w-24" />
          <Skeleton className="h-9 w-36" />
        </div>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>
            <Skeleton className="h-6 w-32" />
          </CardTitle>
          <Skeleton className="h-4 w-64" />
        </CardHeader>
        <CardContent>
          <div className="mb-4">
            <Skeleton className="h-10 w-full" />
          </div>
          <div className="space-y-4">
            {Array.from({ length: 5 }).map((_, index) => (
              <div key={index} className="p-4 rounded-lg border">
                <div className="flex items-start gap-4">
                  <Skeleton className="h-8 w-8 rounded-full" />
                  <div className="flex-1">
                    <div className="flex items-center justify-between">
                      <Skeleton className="h-5 w-48" />
                      <div className="flex items-center gap-2">
                        <Skeleton className="h-5 w-16" />
                        <Skeleton className="h-8 w-8" />
                      </div>
                    </div>
                    <Skeleton className="h-4 w-full mt-2" />
                    <Skeleton className="h-4 w-3/4 mt-1" />
                    <div className="flex items-center justify-between mt-2">
                      <Skeleton className="h-3 w-32" />
                      <Skeleton className="h-8 w-24" />
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
