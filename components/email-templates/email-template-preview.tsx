"use client"

import { useState } from "react"
import { Send } from "lucide-react"

import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useToast } from "@/components/ui/use-toast"

interface EmailTemplatePreviewProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  template: any
  onSendTest: () => void
}

export function EmailTemplatePreview({ open, onOpenChange, template, onSendTest }: EmailTemplatePreviewProps) {
  const { toast } = useToast()
  const [testEmail, setTestEmail] = useState("")
  const [isSending, setIsSending] = useState(false)

  if (!template) return null

  // Replace variables with sample values
  const processContent = (content: string) => {
    return content
      .replace(/{{user\.name}}/g, "John Doe")
      .replace(/{{order\.id}}/g, "#ORD-12345")
      .replace(/{{product\.name}}/g, "Premium Headphones")
  }

  const handleSendTest = async () => {
    if (!testEmail) {
      toast({
        title: "Email Required",
        description: "Please enter an email address to send the test to.",
        variant: "destructive",
      })
      return
    }

    setIsSending(true)

    // In a real app, you would call an API to send the test email
    setTimeout(() => {
      setIsSending(false)
      onSendTest()
      toast({
        title: "Test Email Sent",
        description: `A test email has been sent to ${testEmail}.`,
      })
    }, 1500)
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-3xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>Email Template Preview</DialogTitle>
          <DialogDescription>Preview how your email template will look to recipients</DialogDescription>
        </DialogHeader>

        <div className="space-y-6">
          <div className="space-y-2">
            <div className="flex justify-between">
              <Label>Template Name</Label>
              <span className="text-muted-foreground">{template.name}</span>
            </div>
            <div className="flex justify-between">
              <Label>Subject</Label>
              <span className="text-muted-foreground">{processContent(template.subject)}</span>
            </div>
            <div className="flex justify-between">
              <Label>Type</Label>
              <span className="text-muted-foreground capitalize">{template.type}</span>
            </div>
          </div>

          <div className="border rounded-md p-4">
            <div className="text-sm font-medium mb-2">Email Preview</div>
            <div className="border-t pt-4">
              <div className="font-bold mb-4">{processContent(template.subject)}</div>
              <div
                className="prose dark:prose-invert max-w-none"
                dangerouslySetInnerHTML={{ __html: processContent(template.body) }}
              />
            </div>
          </div>

          <div className="space-y-2">
            <Label htmlFor="testEmail">Send Test Email To</Label>
            <div className="flex gap-2">
              <Input
                id="testEmail"
                type="email"
                placeholder="Enter email address"
                value={testEmail}
                onChange={(e) => setTestEmail(e.target.value)}
              />
              <Button onClick={handleSendTest} disabled={isSending}>
                <Send className="mr-2 h-4 w-4" />
                {isSending ? "Sending..." : "Send Test"}
              </Button>
            </div>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" onClick={() => onOpenChange(false)}>
            Close
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}
