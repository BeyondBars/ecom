import { Skeleton } from "@/components/ui/skeleton"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"

export default function CouponsLoading() {
  return (
    <div className="flex flex-col space-y-6 p-1 md:p-8">
      <div className="flex items-center justify-between">
        <div>
          <Skeleton className="h-8 w-[180px]" />
          <Skeleton className="mt-2 h-4 w-[250px]" />
        </div>
        <Skeleton className="h-10 w-[120px]" />
      </div>

      <Card>
        <CardHeader className="pb-3">
          <CardTitle>
            <Skeleton className="h-6 w-[200px]" />
          </CardTitle>
          <Skeleton className="h-4 w-[300px]" />
        </CardHeader>
        <CardContent>
          <div className="flex flex-col space-y-4">
            <div className="flex flex-col space-y-2 md:flex-row md:items-center md:justify-between md:space-y-0">
              <div className="flex flex-1 items-center space-x-2">
                <Skeleton className="h-9 w-[240px]" />
                <Skeleton className="h-9 w-[100px]" />
                <Skeleton className="h-9 w-[100px]" />
              </div>
              <Skeleton className="h-9 w-[100px]" />
            </div>

            <div className="rounded-md border">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-muted/50">
                    <tr className="text-left text-sm">
                      <th className="whitespace-nowrap px-4 py-3 font-medium">
                        <Skeleton className="h-4 w-[80px]" />
                      </th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">
                        <Skeleton className="h-4 w-[80px]" />
                      </th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">
                        <Skeleton className="h-4 w-[80px]" />
                      </th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">
                        <Skeleton className="h-4 w-[80px]" />
                      </th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">
                        <Skeleton className="h-4 w-[80px]" />
                      </th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">
                        <Skeleton className="h-4 w-[80px]" />
                      </th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">
                        <Skeleton className="h-4 w-[80px]" />
                      </th>
                      <th className="whitespace-nowrap px-4 py-3 font-medium">
                        <Skeleton className="h-4 w-[80px]" />
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {Array.from({ length: 5 }).map((_, index) => (
                      <tr key={index} className="border-t">
                        <td className="whitespace-nowrap px-4 py-3">
                          <Skeleton className="h-5 w-[100px]" />
                        </td>
                        <td className="whitespace-nowrap px-4 py-3">
                          <Skeleton className="h-5 w-[60px]" />
                          <Skeleton className="mt-1 h-3 w-[80px]" />
                        </td>
                        <td className="whitespace-nowrap px-4 py-3">
                          <Skeleton className="h-5 w-[60px]" />
                        </td>
                        <td className="whitespace-nowrap px-4 py-3">
                          <Skeleton className="h-5 w-[100px]" />
                        </td>
                        <td className="whitespace-nowrap px-4 py-3">
                          <Skeleton className="h-5 w-[100px]" />
                        </td>
                        <td className="whitespace-nowrap px-4 py-3">
                          <Skeleton className="h-5 w-[60px]" />
                        </td>
                        <td className="whitespace-nowrap px-4 py-3">
                          <Skeleton className="h-5 w-[70px]" />
                        </td>
                        <td className="whitespace-nowrap px-4 py-3">
                          <div className="flex items-center gap-2">
                            <Skeleton className="h-8 w-8 rounded-full" />
                            <Skeleton className="h-8 w-8 rounded-full" />
                            <Skeleton className="h-8 w-8 rounded-full" />
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

            <div className="flex items-center justify-between">
              <Skeleton className="h-5 w-[200px]" />
              <div className="flex items-center space-x-6">
                <Skeleton className="h-9 w-[100px]" />
                <Skeleton className="h-9 w-[100px]" />
                <Skeleton className="h-9 w-[100px]" />
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
