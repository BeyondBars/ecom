"use client"

import { useEditor, EditorContent, BubbleMenu } from "@tiptap/react"
import StarterKit from "@tiptap/starter-kit"
import Underline from "@tiptap/extension-underline"
import Link from "@tiptap/extension-link"
import Image from "@tiptap/extension-image"
import TextAlign from "@tiptap/extension-text-align"
import Table from "@tiptap/extension-table"
import TableRow from "@tiptap/extension-table-row"
import TableCell from "@tiptap/extension-table-cell"
import TableHeader from "@tiptap/extension-table-header"
import Placeholder from "@tiptap/extension-placeholder"
import { useState, useEffect, useCallback } from "react"
import {
  Bold,
  Italic,
  UnderlineIcon,
  Strikethrough,
  Heading1,
  Heading2,
  Heading3,
  List,
  ListOrdered,
  AlignLeft,
  AlignCenter,
  AlignRight,
  AlignJustify,
  LinkIcon,
  ImageIcon,
  TableIcon,
  Code,
  Quote,
  Undo,
  Redo,
  ChevronDown,
  Check,
} from "lucide-react"

import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Separator } from "@/components/ui/separator"

interface ContentEditorProps {
  content?: string
  onChange: (content: string) => void
  placeholder?: string
  className?: string
  editable?: boolean
}

export function ContentEditor({
  content = "",
  onChange,
  placeholder = "Start writing...",
  className,
  editable = true,
}: ContentEditorProps) {
  const [linkUrl, setLinkUrl] = useState("")
  const [linkOpen, setLinkOpen] = useState(false)
  const [imageUrl, setImageUrl] = useState("")
  const [imageOpen, setImageOpen] = useState(false)

  const editor = useEditor({
    extensions: [
      StarterKit,
      Underline,
      Link.configure({
        openOnClick: false,
        HTMLAttributes: {
          class: "text-primary underline underline-offset-2",
        },
      }),
      Image.configure({
        HTMLAttributes: {
          class: "rounded-md max-w-full my-4",
        },
      }),
      TextAlign.configure({
        types: ["heading", "paragraph"],
      }),
      Table.configure({
        resizable: true,
        HTMLAttributes: {
          class: "border-collapse table-auto w-full my-4",
        },
      }),
      TableRow,
      TableHeader.configure({
        HTMLAttributes: {
          class: "border border-border px-3 py-2 bg-muted",
        },
      }),
      TableCell.configure({
        HTMLAttributes: {
          class: "border border-border px-3 py-2",
        },
      }),
      Placeholder.configure({
        placeholder,
      }),
    ],
    content,
    editable,
    onUpdate: ({ editor }) => {
      onChange(editor.getHTML())
    },
  })

  useEffect(() => {
    if (editor && content && editor.getHTML() !== content) {
      editor.commands.setContent(content)
    }
  }, [editor, content])

  const setLink = useCallback(() => {
    if (!editor) return

    if (linkUrl === "") {
      editor.chain().focus().extendMarkRange("link").unsetLink().run()
      return
    }

    editor.chain().focus().extendMarkRange("link").setLink({ href: linkUrl }).run()

    setLinkUrl("")
    setLinkOpen(false)
  }, [editor, linkUrl])

  const addImage = useCallback(() => {
    if (!editor) return

    if (imageUrl) {
      editor.chain().focus().setImage({ src: imageUrl }).run()
      setImageUrl("")
      setImageOpen(false)
    }
  }, [editor, imageUrl])

  if (!editor) {
    return null
  }

  return (
    <div className={cn("border rounded-md", className)}>
      <div className="flex flex-wrap gap-1 p-1 border-b bg-muted/50">
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <Button variant="ghost" size="sm" className="h-8">
              <Heading1 className="h-4 w-4 mr-1" />
              Heading
              <ChevronDown className="h-3 w-3 ml-1" />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent>
            <DropdownMenuItem onClick={() => editor.chain().focus().toggleHeading({ level: 1 }).run()}>
              <Heading1 className="h-4 w-4 mr-2" />
              Heading 1{editor.isActive("heading", { level: 1 }) && <Check className="h-4 w-4 ml-auto" />}
            </DropdownMenuItem>
            <DropdownMenuItem onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()}>
              <Heading2 className="h-4 w-4 mr-2" />
              Heading 2{editor.isActive("heading", { level: 2 }) && <Check className="h-4 w-4 ml-auto" />}
            </DropdownMenuItem>
            <DropdownMenuItem onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()}>
              <Heading3 className="h-4 w-4 mr-2" />
              Heading 3{editor.isActive("heading", { level: 3 }) && <Check className="h-4 w-4 ml-auto" />}
            </DropdownMenuItem>
            <DropdownMenuItem onClick={() => editor.chain().focus().setParagraph().run()}>
              Paragraph
              {editor.isActive("paragraph") && <Check className="h-4 w-4 ml-auto" />}
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>

        <Separator orientation="vertical" className="h-8" />

        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive("bold") && "bg-accent")}
          onClick={() => editor.chain().focus().toggleBold().run()}
        >
          <Bold className="h-4 w-4" />
        </Button>
        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive("italic") && "bg-accent")}
          onClick={() => editor.chain().focus().toggleItalic().run()}
        >
          <Italic className="h-4 w-4" />
        </Button>
        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive("underline") && "bg-accent")}
          onClick={() => editor.chain().focus().toggleUnderline().run()}
        >
          <UnderlineIcon className="h-4 w-4" />
        </Button>
        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive("strike") && "bg-accent")}
          onClick={() => editor.chain().focus().toggleStrike().run()}
        >
          <Strikethrough className="h-4 w-4" />
        </Button>

        <Separator orientation="vertical" className="h-8" />

        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive("bulletList") && "bg-accent")}
          onClick={() => editor.chain().focus().toggleBulletList().run()}
        >
          <List className="h-4 w-4" />
        </Button>
        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive("orderedList") && "bg-accent")}
          onClick={() => editor.chain().focus().toggleOrderedList().run()}
        >
          <ListOrdered className="h-4 w-4" />
        </Button>

        <Separator orientation="vertical" className="h-8" />

        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive({ textAlign: "left" }) && "bg-accent")}
          onClick={() => editor.chain().focus().setTextAlign("left").run()}
        >
          <AlignLeft className="h-4 w-4" />
        </Button>
        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive({ textAlign: "center" }) && "bg-accent")}
          onClick={() => editor.chain().focus().setTextAlign("center").run()}
        >
          <AlignCenter className="h-4 w-4" />
        </Button>
        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive({ textAlign: "right" }) && "bg-accent")}
          onClick={() => editor.chain().focus().setTextAlign("right").run()}
        >
          <AlignRight className="h-4 w-4" />
        </Button>
        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive({ textAlign: "justify" }) && "bg-accent")}
          onClick={() => editor.chain().focus().setTextAlign("justify").run()}
        >
          <AlignJustify className="h-4 w-4" />
        </Button>

        <Separator orientation="vertical" className="h-8" />

        <Popover open={linkOpen} onOpenChange={setLinkOpen}>
          <PopoverTrigger asChild>
            <Button variant="ghost" size="sm" className={cn("h-8", editor.isActive("link") && "bg-accent")}>
              <LinkIcon className="h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent className="w-80">
            <div className="flex flex-col gap-2">
              <Label htmlFor="link-url">Link URL</Label>
              <div className="flex gap-2">
                <Input
                  id="link-url"
                  value={linkUrl}
                  onChange={(e) => setLinkUrl(e.target.value)}
                  placeholder="https://example.com"
                />
                <Button size="sm" onClick={setLink}>
                  <Check className="h-4 w-4" />
                </Button>
              </div>
              {editor.isActive("link") && (
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => {
                    editor.chain().focus().unsetLink().run()
                    setLinkOpen(false)
                  }}
                >
                  Remove Link
                </Button>
              )}
            </div>
          </PopoverContent>
        </Popover>

        <Popover open={imageOpen} onOpenChange={setImageOpen}>
          <PopoverTrigger asChild>
            <Button variant="ghost" size="sm" className="h-8">
              <ImageIcon className="h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent className="w-80">
            <div className="flex flex-col gap-2">
              <Label htmlFor="image-url">Image URL</Label>
              <div className="flex gap-2">
                <Input
                  id="image-url"
                  value={imageUrl}
                  onChange={(e) => setImageUrl(e.target.value)}
                  placeholder="https://example.com/image.jpg"
                />
                <Button size="sm" onClick={addImage}>
                  <Check className="h-4 w-4" />
                </Button>
              </div>
            </div>
          </PopoverContent>
        </Popover>

        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive("table") && "bg-accent")}
          onClick={() => editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()}
        >
          <TableIcon className="h-4 w-4" />
        </Button>

        <Separator orientation="vertical" className="h-8" />

        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive("blockquote") && "bg-accent")}
          onClick={() => editor.chain().focus().toggleBlockquote().run()}
        >
          <Quote className="h-4 w-4" />
        </Button>
        <Button
          variant="ghost"
          size="sm"
          className={cn("h-8", editor.isActive("codeBlock") && "bg-accent")}
          onClick={() => editor.chain().focus().toggleCodeBlock().run()}
        >
          <Code className="h-4 w-4" />
        </Button>

        <Separator orientation="vertical" className="h-8" />

        <Button
          variant="ghost"
          size="sm"
          className="h-8"
          onClick={() => editor.chain().focus().undo().run()}
          disabled={!editor.can().undo()}
        >
          <Undo className="h-4 w-4" />
        </Button>
        <Button
          variant="ghost"
          size="sm"
          className="h-8"
          onClick={() => editor.chain().focus().redo().run()}
          disabled={!editor.can().redo()}
        >
          <Redo className="h-4 w-4" />
        </Button>
      </div>

      {editor && (
        <BubbleMenu
          editor={editor}
          tippyOptions={{ duration: 100 }}
          className="bg-background rounded-md shadow-md border overflow-hidden flex"
        >
          <Button
            variant="ghost"
            size="sm"
            className={cn("h-8 rounded-none", editor.isActive("bold") && "bg-accent")}
            onClick={() => editor.chain().focus().toggleBold().run()}
          >
            <Bold className="h-4 w-4" />
          </Button>
          <Button
            variant="ghost"
            size="sm"
            className={cn("h-8 rounded-none", editor.isActive("italic") && "bg-accent")}
            onClick={() => editor.chain().focus().toggleItalic().run()}
          >
            <Italic className="h-4 w-4" />
          </Button>
          <Button
            variant="ghost"
            size="sm"
            className={cn("h-8 rounded-none", editor.isActive("underline") && "bg-accent")}
            onClick={() => editor.chain().focus().toggleUnderline().run()}
          >
            <UnderlineIcon className="h-4 w-4" />
          </Button>
          <Button
            variant="ghost"
            size="sm"
            className={cn("h-8 rounded-none", editor.isActive("link") && "bg-accent")}
            onClick={() => {
              setLinkUrl(editor.isActive("link") ? editor.getAttributes("link").href : "")
              setLinkOpen(true)
            }}
          >
            <LinkIcon className="h-4 w-4" />
          </Button>
        </BubbleMenu>
      )}

      <EditorContent
        editor={editor}
        className="prose prose-sm sm:prose-base max-w-none p-4 focus:outline-none min-h-[200px]"
      />
    </div>
  )
}
