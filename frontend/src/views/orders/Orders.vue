<template>
  <PageHeader title="Orders" />

  <div class="card overflow-hidden">
    <div class="flex flex-wrap gap-3 border-b border-slate-200 p-4">
      <input v-model="filters.search" type="search" class="form-input max-w-xs" placeholder="Order number, email, tracking" aria-label="Search orders" />
      <select v-model="filters.status" class="form-input w-auto" aria-label="Order status">
        <option value="">All statuses</option>
        <option v-for="status in statuses" :key="status" :value="status" class="capitalize">{{ status }}</option>
      </select>
      <select v-model="filters.payment_status" class="form-input w-auto" aria-label="Payment status">
        <option value="">Any payment</option>
        <option value="pending">Unpaid</option>
        <option value="paid">Paid</option>
        <option value="refund_pending">Refund due</option>
        <option value="refunded">Refunded</option>
      </select>
      <select v-model="filters.return_status" class="form-input w-auto" aria-label="Return status">
        <option value="">Any return</option>
        <option value="requested">Return requested</option>
        <option value="approved">Return approved</option>
      </select>
      <input v-model="filters.from" type="date" class="form-input w-auto" aria-label="From date" />
      <input v-model="filters.to" type="date" class="form-input w-auto" aria-label="To date" />
      <label class="flex items-center gap-2 text-sm text-slate-700">
        <input v-model="includeUnpaid" type="checkbox" class="form-check" /> Include abandoned online payments
      </label>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
          <tr>
            <th class="px-4 py-3">Order</th>
            <th class="px-4 py-3">Customer</th>
            <th class="px-4 py-3">Placed</th>
            <th class="px-4 py-3">Payment</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="order in items" :key="order.id" class="cursor-pointer hover:bg-slate-50" @click="router.push({ name: 'orders.details', params: { number: order.number } })">
            <td class="px-4 py-3">
              <p class="font-medium text-slate-900">{{ order.number }}</p>
              <p class="text-xs text-slate-500">{{ order.items_count }} item{{ order.items_count === 1 ? '' : 's' }}</p>
            </td>
            <td class="px-4 py-3">
              <p class="text-slate-900">{{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}</p>
              <p class="text-xs text-slate-500">{{ order.email }}</p>
            </td>
            <td class="px-4 py-3 text-slate-500">{{ $date(order.created_at, { format: 'DD MMM YYYY, h:mm a' }) }}</td>
            <td class="px-4 py-3">
              <p class="text-xs text-slate-500">{{ order.payment_method === 'cod' ? 'Cash on Delivery' : 'Online' }}</p>
              <StatusBadge :status="order.payment_status" :label="paymentLabel(order.payment_status)" />
            </td>
            <td class="px-4 py-3">
              <StatusBadge :status="order.status" />
              <StatusBadge v-if="order.return_status" class="ml-1" :status="order.return_status" :label="`Return ${order.return_status}`" />
            </td>
            <td class="px-4 py-3 text-right font-medium">{{ formatMoney(order.total) }}</td>
          </tr>
          <tr v-if="!isLoading && !items.length">
            <td colspan="6" class="px-4 py-10 text-center text-slate-500">No orders match.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <PaginationBar v-model="page" :meta="meta" />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageHeader from '@/components/app/PageHeader.vue'
import PaginationBar from '@/components/shop/PaginationBar.vue'
import StatusBadge from '@/components/shop/StatusBadge.vue'
import { usePaginatedList } from '@/composables/use-paginated-list'
import { formatMoney } from '@/utils/money'
import type { Order, PaymentStatus } from '@/types/shop'

const router = useRouter()
const route = useRoute()
const statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned']
const includeUnpaid = ref(false)
const query = (key: string) => (typeof route.query[key] === 'string' ? (route.query[key] as string) : '')

const { items, filters, page, meta, isLoading, load } = usePaginatedList<Order>('admin/orders', {
  search: '',
  status: query('status'),
  payment_status: query('payment_status'),
  return_status: query('return_status'),
  from: '',
  to: '',
  include_unpaid: '',
})

watch(includeUnpaid, (value) => (filters.include_unpaid = value ? '1' : ''))

const paymentLabel = (status: PaymentStatus) =>
  ({ pending: 'Unpaid', paid: 'Paid', failed: 'Failed', refund_pending: 'Refund due', refunded: 'Refunded' })[status]

onMounted(load)
</script>
