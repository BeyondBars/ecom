import { faker } from "@faker-js/faker"

export type InvoiceStatus = "paid" | "pending" | "overdue" | "draft"

export interface InvoiceItem {
  id: string
  description: string
  quantity: number
  unitPrice: number
  total: number
}

export interface Invoice {
  id: string
  invoiceNumber: string
  customerId: string
  customerName: string
  customerEmail: string
  customerAddress: string
  date: string
  dueDate: string
  status: InvoiceStatus
  items: InvoiceItem[]
  subtotal: number
  tax: number
  total: number
  notes?: string
  createdAt: string
  updatedAt: string
}

// Generate 20 random invoices
export const invoices: Invoice[] = Array.from({ length: 20 }, (_, i) => {
  const id = faker.string.uuid()
  const customerId = faker.string.uuid()
  const customerName = faker.person.fullName()
  const customerEmail = faker.internet.email()
  const customerAddress = `${faker.location.streetAddress()}, ${faker.location.city()}, ${faker.location.state()}, ${faker.location.zipCode()}`

  const date = faker.date.recent({ days: 30 }).toISOString().split("T")[0]
  const dueDate = faker.date
    .soon({ days: 14, refDate: new Date(date) })
    .toISOString()
    .split("T")[0]

  const statuses: InvoiceStatus[] = ["paid", "pending", "overdue", "draft"]
  const status = statuses[Math.floor(Math.random() * statuses.length)]

  // Generate 1-5 items for each invoice
  const items: InvoiceItem[] = Array.from({ length: Math.floor(Math.random() * 5) + 1 }, (_, j) => {
    const quantity = Math.floor(Math.random() * 5) + 1
    const unitPrice = Number.parseFloat(faker.commerce.price({ min: 10, max: 500 }))
    return {
      id: faker.string.uuid(),
      description: faker.commerce.productName(),
      quantity,
      unitPrice,
      total: quantity * unitPrice,
    }
  })

  const subtotal = items.reduce((sum, item) => sum + item.total, 0)
  const tax = subtotal * 0.1 // 10% tax
  const total = subtotal + tax

  return {
    id,
    invoiceNumber: `INV-${(i + 1).toString().padStart(5, "0")}`,
    customerId,
    customerName,
    customerEmail,
    customerAddress,
    date,
    dueDate,
    status,
    items,
    subtotal,
    tax,
    total,
    notes: Math.random() > 0.7 ? faker.lorem.paragraph() : undefined,
    createdAt: faker.date.recent({ days: 60 }).toISOString(),
    updatedAt: faker.date.recent({ days: 30 }).toISOString(),
  }
})
