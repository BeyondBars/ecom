"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Copy, Edit, Eye, MoreHorizontal, Plus, Search, Trash2, Send, CheckCircle2, XCircle } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Input } from "@/components/ui/input"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Badge } from "@/components/ui/badge"
import { DeleteDialog } from "@/components/common/delete-dialog"
import { EmailTemplateDialog } from "@/components/email-templates/email-template-dialog"
import { EmailTemplatePreview } from "@/components/email-templates/email-template-preview"
import { useToast } from "@/components/ui/use-toast"
import { emailTemplates } from "@/lib/data/email-templates"

export default function EmailTemplatesPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [searchQuery, setSearchQuery] = useState("")
  const [templateType, setTemplateType] = useState("all")
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false)
  const [isTemplateDialogOpen, setIsTemplateDialogOpen] = useState(false)
  const [isPreviewOpen, setIsPreviewOpen] = useState(false)
  const [selectedTemplate, setSelectedTemplate] = useState<any>(null)

  // Filter templates based on search query and type
  const filteredTemplates = emailTemplates.filter((template) => {
    const matchesSearch =
      template.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      template.subject.toLowerCase().includes(searchQuery.toLowerCase())

    const matchesType = templateType === "all" || template.type === templateType

    return matchesSearch && matchesType
  })

  const handleCreateTemplate = () => {
    setSelectedTemplate(null)
    setIsTemplateDialogOpen(true)
  }

  const handleEditTemplate = (template: any) => {
    setSelectedTemplate(template)
    setIsTemplateDialogOpen(true)
  }

  const handlePreviewTemplate = (template: any) => {
    setSelectedTemplate(template)
    setIsPreviewOpen(true)
  }

  const handleDeleteTemplate = (template: any) => {
    setSelectedTemplate(template)
    setIsDeleteDialogOpen(true)
  }

  const handleDuplicateTemplate = (template: any) => {
    const duplicatedTemplate = {
      ...template,
      id: `temp-${Date.now()}`,
      name: `${template.name} (Copy)`,
    }

    toast({
      title: "Template duplicated",
      description: `${template.name} has been duplicated.`,
    })

    // In a real app, you would save this to the database
    console.log("Duplicated template:", duplicatedTemplate)
  }

  const handleSendTestEmail = (template: any) => {
    // In a real app, you would call an API to send a test email
    toast({
      title: "Test email sent",
      description: `A test email has been sent using the "${template.name}" template.`,
    })
  }

  const confirmDelete = async () => {
    if (!selectedTemplate) return

    // In a real app, you would call an API to delete the template
    toast({
      title: "Template deleted",
      description: `${selectedTemplate.name} has been deleted.`,
    })

    setIsDeleteDialogOpen(false)
  }

  const handleToggleStatus = (template: any) => {
    const newStatus = !template.isActive

    // In a real app, you would call an API to update the template status
    toast({
      title: newStatus ? "Template activated" : "Template deactivated",
      description: `${template.name} has been ${newStatus ? "activated" : "deactivated"}.`,
    })
  }

  const getTypeColor = (type: string) => {
    switch (type) {
      case "transactional":
        return "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300"
      case "marketing":
        return "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300"
      case "notification":
        return "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300"
      default:
        return "bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300"
    }
  }

  return (
    <div className="flex flex-col gap-8 p-4 md:p-8">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold tracking-tight">Email Templates</h1>
          <p className="text-muted-foreground">Manage email templates for your eCommerce store</p>
        </div>
        <Button onClick={handleCreateTemplate}>
          <Plus className="mr-2 h-4 w-4" />
          New Template
        </Button>
      </div>

      <Card>
        <CardHeader className="pb-3">
          <CardTitle>Email Templates</CardTitle>
          <CardDescription>Create and manage email templates for various purposes</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex flex-col gap-4">
            <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
              <div className="relative w-full md:w-96">
                <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input
                  type="search"
                  placeholder="Search templates..."
                  className="w-full pl-8"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                />
              </div>
              <div className="flex items-center gap-2">
                <Select value={templateType} onValueChange={setTemplateType}>
                  <SelectTrigger className="w-[180px]">
                    <SelectValue placeholder="Filter by type" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All Types</SelectItem>
                    <SelectItem value="transactional">Transactional</SelectItem>
                    <SelectItem value="marketing">Marketing</SelectItem>
                    <SelectItem value="notification">Notification</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            <div className="rounded-md border">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>Subject</TableHead>
                    <TableHead>Type</TableHead>
                    <TableHead>Triggered By</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {filteredTemplates.length === 0 ? (
                    <TableRow>
                      <TableCell colSpan={6} className="h-24 text-center">
                        No templates found.
                      </TableCell>
                    </TableRow>
                  ) : (
                    filteredTemplates.map((template) => (
                      <TableRow key={template.id}>
                        <TableCell className="font-medium">{template.name}</TableCell>
                        <TableCell>{template.subject}</TableCell>
                        <TableCell>
                          <Badge variant="outline" className={getTypeColor(template.type)}>
                            {template.type.charAt(0).toUpperCase() + template.type.slice(1)}
                          </Badge>
                        </TableCell>
                        <TableCell>{template.triggeredBy}</TableCell>
                        <TableCell>
                          <Badge
                            variant={template.isActive ? "default" : "secondary"}
                            className="cursor-pointer"
                            onClick={() => handleToggleStatus(template)}
                          >
                            {template.isActive ? (
                              <CheckCircle2 className="mr-1 h-3 w-3" />
                            ) : (
                              <XCircle className="mr-1 h-3 w-3" />
                            )}
                            {template.isActive ? "Active" : "Inactive"}
                          </Badge>
                        </TableCell>
                        <TableCell className="text-right">
                          <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                              <Button variant="ghost" className="h-8 w-8 p-0">
                                <span className="sr-only">Open menu</span>
                                <MoreHorizontal className="h-4 w-4" />
                              </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                              <DropdownMenuLabel>Actions</DropdownMenuLabel>
                              <DropdownMenuItem onClick={() => handlePreviewTemplate(template)}>
                                <Eye className="mr-2 h-4 w-4" />
                                Preview
                              </DropdownMenuItem>
                              <DropdownMenuItem onClick={() => handleEditTemplate(template)}>
                                <Edit className="mr-2 h-4 w-4" />
                                Edit
                              </DropdownMenuItem>
                              <DropdownMenuItem onClick={() => handleDuplicateTemplate(template)}>
                                <Copy className="mr-2 h-4 w-4" />
                                Duplicate
                              </DropdownMenuItem>
                              <DropdownMenuItem onClick={() => handleSendTestEmail(template)}>
                                <Send className="mr-2 h-4 w-4" />
                                Send Test
                              </DropdownMenuItem>
                              <DropdownMenuSeparator />
                              <DropdownMenuItem
                                className="text-destructive focus:text-destructive"
                                onClick={() => handleDeleteTemplate(template)}
                              >
                                <Trash2 className="mr-2 h-4 w-4" />
                                Delete
                              </DropdownMenuItem>
                            </DropdownMenuContent>
                          </DropdownMenu>
                        </TableCell>
                      </TableRow>
                    ))
                  )}
                </TableBody>
              </Table>
            </div>
          </div>
        </CardContent>
      </Card>

      <EmailTemplateDialog
        open={isTemplateDialogOpen}
        onOpenChange={setIsTemplateDialogOpen}
        template={selectedTemplate}
      />

      <EmailTemplatePreview
        open={isPreviewOpen}
        onOpenChange={setIsPreviewOpen}
        template={selectedTemplate}
        onSendTest={() => selectedTemplate && handleSendTestEmail(selectedTemplate)}
      />

      <DeleteDialog
        open={isDeleteDialogOpen}
        onOpenChange={setIsDeleteDialogOpen}
        onConfirm={confirmDelete}
        title="Delete Email Template"
        description={`Are you sure you want to delete "${selectedTemplate?.name}"? This action cannot be undone.`}
      />
    </div>
  )
}
