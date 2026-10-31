<template>
  <PageHeader :title="order ? `Order ${order.number}` : 'Order'">
    <template #description>
      <span v-if="order">Placed {{ $date(order.created_at, { format: 'DD MMM YYYY [at] h:mm a' }) }}</span>
    </template>
    <div class="flex gap-2 print:hidden">
      <button v-if="order" type="button" class="btn btn-secondary" @click="printInvoice">Print invoice</button>
      <RouterLink :to="{ name: 'orders' }" class="btn btn-secondary">Back to orders</RouterLink>
    </div>
  </PageHeader>

  <div v-if="order" class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
      <!-- Actions -->
      <section class="card space-y-4 p-5 print:hidden">
        <div class="flex flex-wrap items-center gap-2">
          <StatusBadge :status="order.status" />
          <StatusBadge :status="order.payment_status" :label="`Payment: ${order.payment_status.replace('_', ' ')}`" />
          <StatusBadge v-if="order.return_status" :status="order.return_status" :label="`Return ${order.return_status}`" />
        </div>

        <div v-if="awaitingPayment" class="rounded-lg bg-amber-50 p-3 text-sm text-amber-800">
          Waiting for the customer to finish paying online. Unpaid orders are cancelled automatically after 30 minutes.
        </div>

        <div v-else-if="order.next_statuses?.length" class="space-y-3">
          <p class="text-sm font-medium text-slate-700">Update status</p>
          <div v-if="nextStatus === 'shipped'" class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <input v-model="courier" class="form-input" placeholder="Courier (e.g. Delhivery)" aria-label="Courier" />
            <input v-model="trackingNumber" class="form-input" placeholder="Tracking number *" aria-label="Tracking number" />
          </div>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="status in order.next_statuses"
              :key="status"
              type="button"
              class="btn"
              :class="status === 'cancelled' ? 'btn-secondary text-red-600' : nextStatus === status ? 'btn-primary' : 'btn-secondary'"
              :disabled="isSaving"
              @click="changeStatus(status)"
            >
              {{ statusAction[status] }}
            </button>
          </div>
          <p v-if="errors.status || errors.tracking_number" class="form-error">{{ errors.status || errors.tracking_number }}</p>
        </div>

        <div v-if="order.payment_method === 'cod' && order.payment_status === 'pending' && order.status !== 'cancelled'" class="flex items-center justify-between rounded-lg bg-slate-50 p-3 text-sm">
          <span>Cash on Delivery: {{ formatMoney(order.total) }} to collect.</span>
          <button type="button" class="link" @click="post('mark-paid', {}, 'Marked as paid.')">Mark as paid</button>
        </div>

        <div v-if="order.return_status === 'requested' || order.return_status === 'approved'" class="space-y-2 rounded-lg bg-amber-50 p-3 text-sm">
          <p class="font-medium text-amber-900">Return {{ order.return_status }}: "{{ order.return_reason }}"</p>
          <div class="flex flex-wrap gap-2">
            <template v-if="order.return_status === 'requested'">
              <button type="button" class="btn btn-primary px-3 py-1.5" @click="post('return', { decision: 'approved' }, 'Return approved. The customer has been emailed.')">Approve return</button>
              <button type="button" class="btn btn-secondary px-3 py-1.5" @click="post('return', { decision: 'rejected' }, 'Return rejected.')">Reject</button>
            </template>
            <button v-else type="button" class="btn btn-primary px-3 py-1.5" @click="post('return', { decision: 'completed' }, 'Items received and restocked. Refund is now due.')">
              Items received: restock and refund
            </button>
          </div>
        </div>

        <div v-if="order.payment_status === 'refund_pending'" class="flex flex-wrap items-center gap-2 rounded-lg bg-purple-50 p-3 text-sm">
          <span class="flex-1 text-purple-900">A refund of {{ formatMoney(order.total) }} is due{{ order.payment_method === 'razorpay' ? ' (refund it in the Razorpay dashboard)' : '' }}.</span>
          <input v-model="refundReference" class="form-input w-48" placeholder="Refund reference" aria-label="Refund reference" />
          <button type="button" class="btn btn-primary px-3 py-1.5" @click="post('refunded', { reference: refundReference || null }, 'Refund recorded.')">Mark refunded</button>
        </div>
      </section>

      <!-- Items -->
      <section class="card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
            <tr>
              <th class="px-4 py-3">Item</th>
              <th class="px-4 py-3 text-right">Price</th>
              <th class="px-4 py-3 text-right">Qty</th>
              <th class="px-4 py-3 text-right">Total</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in order.items" :key="item.id">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <img v-if="item.image_url" :src="item.image_url" alt="" class="size-12 rounded-lg object-cover" />
                  <div>
                    <RouterLink v-if="item.product_id" :to="{ name: 'products.edit', params: { id: item.product_id } }" class="font-medium text-slate-900 hover:text-brand-600">{{ item.name }}</RouterLink>
                    <p v-else class="font-medium text-slate-900">{{ item.name }}</p>
                    <p class="text-xs text-slate-500">{{ [formatOptions(item.options), item.sku].filter(Boolean).join(' · ') }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 text-right">{{ formatMoney(item.price) }}</td>
              <td class="px-4 py-3 text-right">{{ item.quantity }}</td>
              <td class="px-4 py-3 text-right font-medium">{{ formatMoney(item.total) }}</td>
            </tr>
          </tbody>
          <tfoot class="bg-slate-50 text-sm">
            <tr><td colspan="3" class="px-4 py-2 text-right text-slate-500">Subtotal</td><td class="px-4 py-2 text-right">{{ formatMoney(order.subtotal, true) }}</td></tr>
            <tr v-if="order.discount"><td colspan="3" class="px-4 py-2 text-right text-slate-500">Discount ({{ order.coupon_code }})</td><td class="px-4 py-2 text-right text-emerald-600">-{{ formatMoney(order.discount, true) }}</td></tr>
            <tr><td colspan="3" class="px-4 py-2 text-right text-slate-500">Shipping ({{ order.shipping_method }})</td><td class="px-4 py-2 text-right">{{ order.shipping ? formatMoney(order.shipping, true) : 'Free' }}</td></tr>
            <tr><td colspan="3" class="px-4 py-2 text-right font-semibold">Total</td><td class="px-4 py-2 text-right font-semibold">{{ formatMoney(order.total, true) }}</td></tr>
          </tfoot>
        </table>
      </section>

      <!-- Timeline -->
      <section class="card p-5 print:hidden">
        <h2 class="text-base font-semibold text-slate-900">Timeline</h2>
        <ol class="mt-4 space-y-3 border-l-2 border-slate-200 pl-4 text-sm">
          <li v-for="(event, index) in order.events" :key="index" class="relative">
            <span class="absolute top-1.5 -left-[1.4rem] size-2.5 rounded-full" :class="event.is_internal ? 'bg-slate-400' : 'bg-brand-600'" />
            <p class="font-medium text-slate-900">
              {{ eventLabel(event.type) }}
              <span v-if="event.is_internal" class="ml-1 text-xs font-normal text-slate-500">(internal note)</span>
            </p>
            <p v-if="event.note" class="text-slate-600">{{ event.note }}</p>
            <p class="text-xs text-slate-400">{{ $date(event.created_at, { format: 'DD MMM YYYY, h:mm a' }) }}{{ event.by ? ` · ${event.by}` : '' }}</p>
          </li>
        </ol>
        <form class="mt-4 flex gap-2" @submit.prevent="addNote">
          <input v-model="note" class="form-input" placeholder="Add an internal note (not shown to the customer)" aria-label="Internal note" />
          <button type="submit" class="btn btn-secondary" :disabled="!note.trim()">Add</button>
        </form>
      </section>
    </div>

    <div class="space-y-6">
      <section class="card space-y-1 p-5 text-sm">
        <h2 class="mb-2 text-base font-semibold text-slate-900">Customer</h2>
        <RouterLink v-if="order.customer" :to="{ name: 'users.details', params: { id: order.customer.id } }" class="link">{{ order.customer.name }}</RouterLink>
        <p class="text-slate-600">{{ order.email }}</p>
        <p v-if="order.customer?.mobile" class="text-slate-600">{{ order.customer.mobile }}</p>
        <p v-if="order.customer_note" class="mt-3 rounded-lg bg-slate-50 p-2 text-slate-700">"{{ order.customer_note }}"</p>
      </section>
      <section class="card space-y-0.5 p-5 text-sm text-slate-600">
        <h2 class="mb-2 text-base font-semibold text-slate-900">Ship to</h2>
        <p class="text-slate-900">{{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}</p>
        <p>{{ order.shipping_address.line1 }}</p>
        <p v-if="order.shipping_address.line2">{{ order.shipping_address.line2 }}</p>
        <p>{{ order.shipping_address.city }}, {{ order.shipping_address.state }} {{ order.shipping_address.postal_code }}</p>
        <p>{{ order.shipping_address.phone }}</p>
      </section>
      <section class="card space-y-1 p-5 text-sm text-slate-600">
        <h2 class="mb-2 text-base font-semibold text-slate-900">Payment & delivery</h2>
        <p>{{ order.payment_method === 'cod' ? 'Cash on Delivery' : 'Online (Razorpay)' }}</p>
        <p v-if="order.payment_reference">Reference: <span class="font-mono">{{ order.payment_reference }}</span></p>
        <p>{{ order.shipping_method === 'express' ? 'Express' : 'Standard' }} shipping</p>
        <p v-if="order.tracking_number">{{ order.courier ?? 'Courier' }}: <span class="font-mono">{{ order.tracking_number }}</span></p>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useToast } from 'vue-toast-notification'
import PageHeader from '@/components/app/PageHeader.vue'
import StatusBadge from '@/components/shop/StatusBadge.vue'
import confirm from '@/utils/alert'
import { http } from '@/utils/http'
import { errorMessage, formatMoney, formatOptions, validationErrors } from '@/utils/money'
import type { Order, OrderStatus } from '@/types/shop'

const props = defineProps<{ number: string }>()
const toast = useToast()
const order = ref<Order | null>(null)
const errors = ref<Record<string, string>>({})
const isSaving = ref(false)
const courier = ref('')
const trackingNumber = ref('')
const refundReference = ref('')
const note = ref('')

const statusAction: Record<OrderStatus, string> = {
  pending: 'Pending',
  processing: 'Confirm & start packing',
  shipped: 'Mark shipped',
  delivered: 'Mark delivered',
  cancelled: 'Cancel order',
  returned: 'Returned',
}

const eventLabels: Record<string, string> = {
  placed: 'Order placed',
  paid: 'Payment received',
  processing: 'Packing',
  shipped: 'Shipped',
  delivered: 'Delivered',
  cancelled: 'Cancelled',
  return_requested: 'Return requested',
  return_approved: 'Return approved',
  return_rejected: 'Return rejected',
  return_completed: 'Return received',
  refunded: 'Refunded',
  note: 'Note',
}
const eventLabel = (type: string) => eventLabels[type] ?? type

const awaitingPayment = computed(() => order.value?.payment_method === 'razorpay' && order.value.payment_status === 'pending' && order.value.status === 'pending')
const nextStatus = computed(() => order.value?.next_statuses?.find((status) => status !== 'cancelled'))

const load = async () => {
  const { data } = await http.get<{ data: Order }>(`admin/orders/${props.number}`)
  order.value = data.data
  courier.value = data.data.courier ?? ''
  trackingNumber.value = data.data.tracking_number ?? ''
}

const run = async (request: () => Promise<{ data: { data: Order } }>, success: string) => {
  isSaving.value = true
  errors.value = {}
  try {
    order.value = (await request()).data.data
    toast.success(success)
  } catch (error) {
    errors.value = validationErrors(error)
    toast.error(errorMessage(error))
  } finally {
    isSaving.value = false
  }
}

const changeStatus = async (status: OrderStatus) => {
  if (status === 'cancelled' && !(await confirm({ title: 'Cancel this order?', text: 'Stock is put back and the customer is emailed. Paid orders will need a refund.' }))) return
  await run(
    () => http.put(`admin/orders/${props.number}/status`, {
      status,
      courier: courier.value || null,
      tracking_number: trackingNumber.value || null,
    }),
    status === 'cancelled' ? 'Order cancelled.' : `Order marked ${status}.`
  )
}

const post = (action: string, payload: object, success: string) => run(() => http.post(`admin/orders/${props.number}/${action}`, payload), success)

const addNote = async () => {
  await post('notes', { note: note.value }, 'Note added.')
  note.value = ''
}

const printInvoice = () => window.print()

onMounted(load)
</script>
