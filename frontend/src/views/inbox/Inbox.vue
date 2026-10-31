<template>
  <PageHeader title="Inbox" description="Contact form messages, newsletter sign-ups and back-in-stock requests from the storefront." />

  <div class="mb-4 flex gap-2" role="tablist">
    <button v-for="(label, key) in tabs" :key="key" type="button" role="tab" :aria-selected="tab === key" class="btn" :class="tab === key ? 'btn-primary' : 'btn-secondary'" @click="tab = key">
      {{ label }}
    </button>
  </div>

  <!-- Messages -->
  <div v-if="tab === 'messages'" class="card overflow-hidden">
    <div class="flex flex-wrap gap-3 border-b border-slate-200 p-4">
      <select v-model="messages.filters.status" class="form-input w-auto" aria-label="Status">
        <option value="">All</option>
        <option value="new">New</option>
        <option value="read">Read</option>
        <option value="replied">Replied</option>
        <option value="archived">Archived</option>
      </select>
      <input v-model="messages.filters.search" type="search" class="form-input max-w-xs" placeholder="Search" aria-label="Search messages" />
    </div>
    <ul class="divide-y divide-slate-100">
      <li v-for="message in messages.items.value" :key="message.id" class="space-y-2 p-4 text-sm">
        <div class="flex flex-wrap items-center gap-2">
          <StatusBadge :status="message.status" />
          <span class="font-semibold text-slate-900">{{ message.subject }}</span>
          <span class="ml-auto text-xs text-slate-500">{{ $date(message.created_at, { format: 'DD MMM YYYY, h:mm a' }) }}</span>
        </div>
        <p class="whitespace-pre-line text-slate-700">{{ message.message }}</p>
        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
          <span>{{ message.name }} · <a :href="`mailto:${message.email}?subject=Re: ${encodeURIComponent(message.subject)}`" class="link" @click="setStatus(message, 'replied')">{{ message.email }}</a><template v-if="message.phone"> · {{ message.phone }}</template></span>
          <span class="ml-auto flex gap-2">
            <button v-if="message.status === 'new'" type="button" class="link-muted" @click="setStatus(message, 'read')">Mark read</button>
            <button v-if="message.status !== 'archived'" type="button" class="link-muted" @click="setStatus(message, 'archived')">Archive</button>
          </span>
        </div>
      </li>
      <li v-if="!messages.isLoading.value && !messages.items.value.length" class="p-10 text-center text-sm text-slate-500">No messages.</li>
    </ul>
    <PaginationBar v-model="messages.page.value" :meta="messages.meta.value" />
  </div>

  <!-- Subscribers -->
  <div v-else-if="tab === 'subscribers'" class="card overflow-hidden">
    <div class="flex flex-wrap items-center gap-3 border-b border-slate-200 p-4">
      <input v-model="subscriberSearch" type="search" class="form-input max-w-xs" placeholder="Search email" aria-label="Search subscribers" />
      <button type="button" class="btn btn-secondary ml-auto" @click="exportCsv">Export CSV</button>
    </div>
    <table class="min-w-full divide-y divide-slate-200 text-sm">
      <tbody class="divide-y divide-slate-100">
        <tr v-for="subscriber in subscribers" :key="subscriber.id">
          <td class="px-4 py-3 text-slate-900">{{ subscriber.email }}</td>
          <td class="px-4 py-3 text-slate-500">{{ subscriber.source }}</td>
          <td class="px-4 py-3 text-slate-500">{{ $date(subscriber.created_at, { format: 'DD MMM YYYY' }) }}</td>
          <td class="px-4 py-3 text-right"><button type="button" class="link-muted" @click="unsubscribe(subscriber.id)">Remove</button></td>
        </tr>
        <tr v-if="!subscribers.length">
          <td class="px-4 py-10 text-center text-slate-500">No subscribers yet.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Stock alerts -->
  <div v-else class="card overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
      <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
        <tr>
          <th class="px-4 py-3">Product</th>
          <th class="px-4 py-3 text-right">In stock</th>
          <th class="px-4 py-3 text-right">Shoppers waiting</th>
          <th class="px-4 py-3">Latest request</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <tr v-for="alert in stockAlerts" :key="alert.product_id">
          <td class="px-4 py-3"><RouterLink :to="{ name: 'products.edit', params: { id: alert.product_id } }" class="link">{{ alert.product?.name }}</RouterLink></td>
          <td class="px-4 py-3 text-right">{{ alert.product?.stock }}</td>
          <td class="px-4 py-3 text-right font-semibold">{{ alert.waiting }}</td>
          <td class="px-4 py-3 text-slate-500">{{ $date(alert.latest_request, { format: 'DD MMM YYYY' }) }}</td>
        </tr>
        <tr v-if="!stockAlerts.length">
          <td colspan="4" class="px-4 py-10 text-center text-slate-500">Nobody is waiting for a restock. Shoppers are emailed automatically when stock goes back above zero.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { watchDebounced } from '@vueuse/core'
import PageHeader from '@/components/app/PageHeader.vue'
import PaginationBar from '@/components/shop/PaginationBar.vue'
import StatusBadge from '@/components/shop/StatusBadge.vue'
import { usePaginatedList } from '@/composables/use-paginated-list'
import { http } from '@/utils/http'
import type { ContactMessage } from '@/types/shop'

type Subscriber = { id: number; email: string; source: string | null; created_at: string }
type StockAlertRow = { product_id: number; waiting: number; latest_request: string; product: { id: number; name: string; stock: number } | null }

const tabs = { messages: 'Messages', subscribers: 'Newsletter', alerts: 'Back-in-stock requests' } as const
const tab = ref<keyof typeof tabs>('messages')

const messages = usePaginatedList<ContactMessage>('admin/messages', { status: 'new', search: '' })
const subscribers = ref<Subscriber[]>([])
const subscriberSearch = ref('')
const stockAlerts = ref<StockAlertRow[]>([])

const setStatus = async (message: ContactMessage, status: ContactMessage['status']) => {
  await http.put(`admin/messages/${message.id}`, { status })
  message.status = status
}

const loadSubscribers = async () => {
  const { data } = await http.get<{ data: Subscriber[] }>('admin/subscribers', { params: { 'filter[search]': subscriberSearch.value || undefined, per_page: 100 } })
  subscribers.value = data.data
}

const unsubscribe = async (id: number) => {
  await http.delete(`admin/subscribers/${id}`)
  loadSubscribers()
}

const exportCsv = async () => {
  const { data } = await http.get('admin/subscribers/export', { responseType: 'blob' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(data)
  link.download = 'newsletter-subscribers.csv'
  link.click()
  URL.revokeObjectURL(link.href)
}

const loadStockAlerts = async () => {
  const { data } = await http.get<{ data: StockAlertRow[] }>('admin/stock-alerts')
  stockAlerts.value = data.data
}

watch(tab, (value) => (value === 'subscribers' ? loadSubscribers() : value === 'alerts' ? loadStockAlerts() : messages.load()))
watchDebounced(subscriberSearch, loadSubscribers, { debounce: 300 })
onMounted(messages.load)
</script>
