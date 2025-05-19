import { Skeleton } from "@/components/ui/skeleton"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"

export default function LikesLoading() {
  return (
    <div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
      <div className="flex items-center justify-between">
        <Skeleton className="h-8 w-[200px]" />
        <div className="flex items-center gap-2">
          <Skeleton className="h-9 w-[100px]" />
        </div>
      </div>

      <Card>
        <CardHeader className="p-4">
          <CardTitle>
            <Skeleton className="h-6 w-[100px]" />
          </CardTitle>
          <Skeleton className="h-4 w-[250px]" />
        </CardHeader>
        <CardContent className="p-4 pt-0">
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4">
            {Array.from({ length: 4 }).map((_, i) => (
              <div key={i} className="space-y-2">
                <Skeleton className="h-4 w-[100px]" />
                <Skeleton className="h-10 w-full" />
              </div>
            ))}
          </div>

          <div className="mt-4 flex justify-end gap-2">
            <Skeleton className="h-9 w-[80px]" />
            <Skeleton className="h-9 w-[120px]" />
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader className="p-4">
          <div className="flex items-center justify-between">
            <Skeleton className="h-6 w-[150px]" />
            <div className="flex items-center gap-2">
              <Skeleton className="h-9 w-[160px]" />
              <Skeleton className="h-9 w-[100px]" />
            </div>
          </div>
        </CardHeader>
        <CardContent className="p-0">
          <div className="rounded-md border">
            <div className="h-[400px] w-full p-4">
              <div className="grid gap-4">
                <div className="flex items-center gap-4">
                  {Array.from({ length: 6 }).map((_, i) => (
                    <Skeleton key={i} className="h-6 w-full" />
                  ))}
                </div>
                {Array.from({ length: 5 }).map((_, i) => (
                  <div key={i} className="flex items-center gap-4">
                    {Array.from({ length: 6 }).map((_, j) => (
                      <Skeleton key={j} className="h-10 w-full" />
                    ))}
                  </div>
                ))}
              </div>
            </div>
          </div>
          <div className="p-4">
            <div className="flex items-center justify-between">
              <Skeleton className="h-9 w-[200px]" />
              <Skeleton className="h-9 w-[200px]" />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
