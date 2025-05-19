import { Skeleton } from "@/components/ui/skeleton"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"

export default function ReviewsLoading() {
  return (
    <div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
      <div className="flex items-center justify-between">
        <Skeleton className="h-8 w-36" />
      </div>

      <Card>
        <CardHeader className="p-4">
          <CardTitle>
            <Skeleton className="h-6 w-24" />
          </CardTitle>
          <Skeleton className="h-4 w-64" />
        </CardHeader>
        <CardContent className="p-4 pt-0">
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4">
            {Array.from({ length: 4 }).map((_, i) => (
              <div key={i} className="space-y-2">
                <Skeleton className="h-4 w-24" />
                <Skeleton className="h-10 w-full" />
              </div>
            ))}
          </div>

          <div className="mt-4 flex justify-end gap-2">
            <Skeleton className="h-9 w-24" />
            <Skeleton className="h-9 w-32" />
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader className="p-4">
          <div className="flex items-center justify-between">
            <Skeleton className="h-6 w-24" />
            <div className="flex items-center gap-2">
              <Skeleton className="h-9 w-32" />
              <Skeleton className="h-9 w-32" />
            </div>
          </div>
        </CardHeader>
        <CardContent className="p-0">
          <div className="rounded-md border">
            <div className="p-4">
              <div className="flex items-center gap-4 py-4">
                <Skeleton className="h-5 w-5" />
                <Skeleton className="h-10 w-10 rounded-md" />
                <Skeleton className="h-10 w-32" />
                <Skeleton className="h-5 w-24" />
                <Skeleton className="h-10 w-40" />
                <Skeleton className="h-6 w-20" />
                <Skeleton className="h-6 w-24" />
                <div className="ml-auto flex gap-2">
                  <Skeleton className="h-8 w-8" />
                  <Skeleton className="h-8 w-8" />
                  <Skeleton className="h-8 w-8" />
                </div>
              </div>
              {Array.from({ length: 4 }).map((_, i) => (
                <div key={i} className="flex items-center gap-4 py-4">
                  <Skeleton className="h-5 w-5" />
                  <Skeleton className="h-10 w-10 rounded-md" />
                  <Skeleton className="h-10 w-32" />
                  <Skeleton className="h-5 w-24" />
                  <Skeleton className="h-10 w-40" />
                  <Skeleton className="h-6 w-20" />
                  <Skeleton className="h-6 w-24" />
                  <div className="ml-auto flex gap-2">
                    <Skeleton className="h-8 w-8" />
                    <Skeleton className="h-8 w-8" />
                    <Skeleton className="h-8 w-8" />
                  </div>
                </div>
              ))}
            </div>
          </div>
          <div className="p-4">
            <div className="flex items-center justify-between">
              <Skeleton className="h-8 w-40" />
              <Skeleton className="h-8 w-40" />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
