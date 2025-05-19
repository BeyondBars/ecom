import { Loader2 } from "lucide-react"

export default function InvoicesLoading() {
  return (
    <div className="flex h-[calc(100vh-10rem)] w-full items-center justify-center">
      <div className="flex flex-col items-center gap-2">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <h3 className="text-xl font-semibold">Loading invoices...</h3>
        <p className="text-sm text-muted-foreground">This may take a few seconds</p>
      </div>
    </div>
  )
}
