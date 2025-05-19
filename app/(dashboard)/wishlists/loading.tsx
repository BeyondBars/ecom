import { Skeleton } from "@/components/ui/skeleton"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"

export default function WishlistsLoading() {
  return (
    <div className="space-y-4 p-8 pt-6">
      <div className="flex items-center justify-between">
        <Skeleton className="h-10 w-[150px]" />
        <Skeleton className="h-10 w-[150px]" />
      </div>
      <div className="flex items-center py-4">
        <Skeleton className="h-10 w-[300px]" />
      </div>
      <Card>
        <CardHeader>
          <CardTitle>
            <Skeleton className="h-6 w-[200px]" />
          </CardTitle>
          <Skeleton className="h-4 w-[300px]" />
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>
                  <Skeleton className="h-4 w-[100px]" />
                </TableHead>
                <TableHead>
                  <Skeleton className="h-4 w-[150px]" />
                </TableHead>
                <TableHead>
                  <Skeleton className="h-4 w-[100px]" />
                </TableHead>
                <TableHead>
                  <Skeleton className="h-4 w-[80px]" />
                </TableHead>
                <TableHead>
                  <Skeleton className="h-4 w-[60px]" />
                </TableHead>
                <TableHead>
                  <Skeleton className="h-4 w-[100px]" />
                </TableHead>
                <TableHead>
                  <Skeleton className="h-4 w-[120px]" />
                </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {Array.from({ length: 5 }).map((_, index) => (
                <TableRow key={index}>
                  <TableCell>
                    <Skeleton className="h-4 w-[100px]" />
                  </TableCell>
                  <TableCell>
                    <Skeleton className="h-4 w-[150px]" />
                  </TableCell>
                  <TableCell>
                    <Skeleton className="h-4 w-[100px]" />
                  </TableCell>
                  <TableCell>
                    <Skeleton className="h-4 w-[80px]" />
                  </TableCell>
                  <TableCell>
                    <Skeleton className="h-4 w-[60px]" />
                  </TableCell>
                  <TableCell>
                    <Skeleton className="h-4 w-[100px]" />
                  </TableCell>
                  <TableCell>
                    <div className="flex space-x-2">
                      <Skeleton className="h-8 w-[60px]" />
                      <Skeleton className="h-8 w-[60px]" />
                    </div>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  )
}
