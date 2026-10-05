export interface Blog {
  id: number
  title: string
  slug: string
  content: string
  meta_description?: string
  meta_keywords?: string
  status: 'enabled' | 'pending' | 'disabled'
  created_at: string
  updated_at: string
} 