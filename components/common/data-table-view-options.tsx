"use client"

import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Button } from "@/components/ui/button"
import { SlidersHorizontal } from "lucide-react"
import { useState } from "react"

interface Column {
  id: string
  label: string
}

const defaultColumns: Column[] = [
  { id: "select", label: "Select" },
  { id: "id", label: "ID" },
  { id: "customer", label: "Customer" },
  { id: "status", label: "Status" },
  { id: "date", label: "Date" },
  { id: "total", label: "Total" },
  { id: "actions", label: "Actions" },
]

export function DataTableViewOptions() {
  const [columns, setColumns] = useState<Column[]>(defaultColumns)
  const [hiddenColumns, setHiddenColumns] = useState<string[]>([])

  const toggleColumn = (id: string) => {
    if (hiddenColumns.includes(id)) {
      setHiddenColumns(hiddenColumns.filter((columnId) => columnId !== id))
    } else {
      setHiddenColumns([...hiddenColumns, id])
    }
  }

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button variant="outline" size="sm" className="ml-auto h-8 gap-1">
          <SlidersHorizontal className="h-3.5 w-3.5" />
          <span className="sr-only sm:not-sr-only sm:whitespace-nowrap">View</span>
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end" className="w-[150px]">
        <DropdownMenuLabel>Toggle columns</DropdownMenuLabel>
        <DropdownMenuSeparator />
        {columns.map((column) => (
          <DropdownMenuCheckboxItem
            key={column.id}
            className="capitalize"
            checked={!hiddenColumns.includes(column.id)}
            onCheckedChange={() => toggleColumn(column.id)}
          >
            {column.label}
          </DropdownMenuCheckboxItem>
        ))}
      </DropdownMenuContent>
    </DropdownMenu>
  )
}
