import { Skeleton } from "@/components/ui/skeleton"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Plus } from "lucide-react"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"

export default function Loading() {
  return (
    <div className="h-full flex-1 flex-col space-y-8 p-8 md:flex">
      <div className="flex items-center justify-between space-y-2">
        <div>
          <h2 className="text-2xl font-bold tracking-tight">Wishlists</h2>
          <p className="text-muted-foreground">Manage customer wishlists and their items</p>
        </div>
        <div className="flex items-center space-x-2">
          <Button disabled>
            <Plus className="mr-2 h-4 w-4" />
            Add Wishlist
          </Button>
        </div>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>All Wishlists</CardTitle>
          <CardDescription>View and manage all customer wishlists in your store.</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="mb-4 flex items-center gap-4">
            <Skeleton className="h-10 w-[300px]" />
            <Skeleton className="ml-auto h-8 w-[70px]" />
          </div>
          <div className="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>
                    <Skeleton className="h-4 w-[40px]" />
                  </TableHead>
                  <TableHead>
                    <Skeleton className="h-4 w-[100px]" />
                  </TableHead>
                  <TableHead>
                    <Skeleton className="h-4 w-[150px]" />
                  </TableHead>
                  <TableHead>
                    <Skeleton className="h-4 w-[60px]" />
                  </TableHead>
                  <TableHead>
                    <Skeleton className="h-4 w-[80px]" />
                  </TableHead>
                  <TableHead>
                    <Skeleton className="h-4 w-[100px]" />
                  </TableHead>
                  <TableHead>
                    <Skeleton className="h-4 w-[40px]" />
                  </TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {Array.from({ length: 5 }).map((_, index) => (
                  <TableRow key={index}>
                    <TableCell>
                      <Skeleton className="h-4 w-[40px]" />
                    </TableCell>
                    <TableCell>
                      <Skeleton className="h-4 w-[150px]" />
                    </TableCell>
                    <TableCell>
                      <div className="space-y-2">
                        <Skeleton className="h-4 w-[120px]" />
                        <Skeleton className="h-3 w-[150px]" />
                      </div>
                    </TableCell>
                    <TableCell>
                      <Skeleton className="h-4 w-[30px] mx-auto" />
                    </TableCell>
                    <TableCell>
                      <Skeleton className="h-6 w-[60px]" />
                    </TableCell>
                    <TableCell>
                      <Skeleton className="h-4 w-[80px]" />
                    </TableCell>
                    <TableCell>
                      <Skeleton className="h-8 w-8 rounded-full" />
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
          <div className="mt-4 flex items-center justify-end space-x-2">
            <Skeleton className="h-8 w-[200px]" />
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
