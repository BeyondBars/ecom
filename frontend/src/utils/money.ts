const rupees = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 })
const rupeesExact = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', minimumFractionDigits: 2 })

/** ₹1,499 (whole rupees) or ₹1,499.50 with `exact`. */
export const formatMoney = (amount: number | string | null | undefined, exact = false): string => {
  if (amount === null || amount === undefined || amount === '') return '-'
  return (exact ? rupeesExact : rupees).format(Number(amount))
}

/** "{ Size: 'M', Color: 'Rose' }" -> "Size: M · Color: Rose" */
export const formatOptions = (options: Record<string, string> | null | undefined): string =>
  Object.entries(options ?? {})
    .map(([name, value]) => `${name}: ${value}`)
    .join(' · ')

/** Laravel validation errors -> first message per field. */
export const validationErrors = (error: unknown): Record<string, string> => {
  const errors = (error as { response?: { data?: { errors?: Record<string, string[]> } } })?.response?.data?.errors
  return Object.fromEntries(Object.entries(errors ?? {}).map(([field, messages]) => [field, messages[0]]))
}

export const errorMessage = (error: unknown, fallback = 'Something went wrong. Please try again.'): string =>
  (error as { response?: { data?: { message?: string } } })?.response?.data?.message ?? fallback
