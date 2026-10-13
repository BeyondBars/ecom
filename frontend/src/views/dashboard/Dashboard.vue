<template>
  <PageHeader :title="`Welcome back${loggedInUser.first_name ? `, ${loggedInUser.first_name}` : ''}`">
    <select v-if="canViewReports" v-model.number="days" class="form-input w-auto" aria-label="Period">
      <option :value="7">Last 7 days</option>
      <option :value="30">Last 30 days</option>
      <option :value="90">Last 90 days</option>
      <option :value="365">Last 12 months</option>
    </select>
  </PageHeader>

  <!-- Customers can sign in here too (same accounts as the storefront) but have nothing to manage -->
  <div v-if="!canViewReports" class="card p-6">
    <h2 class="text-base font-semibold text-slate-900">This is the store's back office</h2>
    <p class="mt-1 text-sm text-slate-600">Your account doesn't have access to shop management. You can update your details under My Account.</p>
    <RouterLink :to="{ name: 'account' }" class="btn btn-secondary mt-4">My account</RouterLink>
  </div>

  <template v-else-if="stats">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div v-for="tile in tiles" :key="tile.label" class="card p-5">
        <p class="text-sm text-slate-500">{{ tile.label }}</p>
        <p class="mt-1 text-2xl font-bold text-slate-900">{{ tile.value }}</p>
        <p v-if="tile.change !== null" class="mt-1 text-xs" :class="tile.change >= 0 ? 'text-emerald-600' : 'text-red-600'">
          {{ tile.change >= 0 ? '▲' : '▼' }} {{ Math.abs(tile.change) }}% vs previous {{ stats.period_days }} days
        </p>
      </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
      <div class="card p-5 xl:col-span-2">
        <h2 class="text-base font-semibold text-slate-900">Revenue</h2>
        <div class="mt-4 flex h-48 items-end gap-px" role="img" :aria-label="`Daily revenue for the last ${stats.period_days} days`">
          <div
            v-for="point in chartPoints"
            :key="point.date"
            class="group relative flex-1 rounded-t bg-brand-500/80 hover:bg-brand-600"
            :style="{ height: `${point.height}%`, minHeight: point.revenue > 0 ? '2px' : '0' }"
          >
            <span class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-1 hidden -translate-x-1/2 rounded bg-slate-900 px-2 py-1 text-xs whitespace-nowrap text-white group-hover:block">
              {{ point.label }}: {{ formatMoney(point.revenue) }} · {{ point.orders }} orders
            </span>
          </div>
        </div>
        <div class="mt-2 flex justify-between text-xs text-slate-400">
          <span>{{ chartPoints[0]?.label }}</span>
          <span>{{ chartPoints[chartPoints.length - 1]?.label }}</span>
        </div>
      </div>

      <div class="card p-5">
        <h2 class="text-base font-semibold text-slate-900">To do</h2>
        <ul class="mt-3 divide-y divide-slate-100 text-sm">
          <li v-for="item in todo" :key="item.label">
            <RouterLink :to="item.to" class="flex items-center justify-between py-2.5 hover:text-brand-600">
              <span>{{ item.label }}</span>
              <span class="rounded-full px-2 py-0.5 text-xs font-semibold" :class="item.count ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-500'">
                {{ item.count }}
              </span>
            </RouterLink>
          </li>
        </ul>
      </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
      <div class="card overflow-hidden xl:col-span-2">
        <div class="flex items-center justify-between px-5 pt-5">
          <h2 class="text-base font-semibold text-slate-900">Recent orders</h2>
          <RouterLink :to="{ name: 'orders' }" class="link text-sm">View all</RouterLink>
        </div>
        <table class="mt-3 min-w-full divide-y divide-slate-200 text-sm">
          <tbody class="divide-y divide-slate-100">
            <tr v-for="order in stats.recent_orders" :key="order.id" class="cursor-pointer hover:bg-slate-50" @click="router.push({ name: 'orders.details', params: { number: order.number } })">
              <td class="px-5 py-3 font-medium text-slate-900">{{ order.number }}</td>
              <td class="px-5 py-3 text-slate-600">{{ order.email }}</td>
              <td class="px-5 py-3"><StatusBadge :status="order.status" /></td>
              <td class="px-5 py-3 text-right font-medium">{{ formatMoney(order.total) }}</td>
            </tr>
            <tr v-if="!stats.recent_orders.length">
              <td class="px-5 py-6 text-center text-slate-500">No orders yet.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card p-5">
        <h2 class="text-base font-semibold text-slate-900">Best sellers</h2>
        <ol class="mt-3 space-y-3 text-sm">
          <li v-for="(product, index) in stats.top_products" :key="product.name" class="flex justify-between gap-3">
            <span class="truncate"><span class="text-slate-400">{{ index + 1 }}.</span> {{ product.name }}</span>
            <span class="shrink-0 text-slate-500">{{ product.units }} sold</span>
          </li>
          <li v-if="!stats.top_products.length" class="text-slate-500">No sales in this period.</li>
        </ol>
      </div>
    </div>
  </template>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/app/PageHeader.vue'
import StatusBadge from '@/components/shop/StatusBadge.vue'
import { useLoggedInUser } from '@/composables/use-logged-in-user'
import { http } from '@/utils/http'
import { formatMoney } from '@/utils/money'
import type { DashboardStats } from '@/types/shop'

const { loggedInUser, can } = useLoggedInUser()
const router = useRouter()
const canViewReports = computed(() => can('view reports'))
const days = ref(30)
const stats = ref<DashboardStats | null>(null)

const load = async () => {
  if (!canViewReports.value) return
  const { data } = await http.get<{ data: DashboardStats }>('admin/dashboard', { params: { days: days.value } })
  stats.value = data.data
}

const change = (current: number, previous: number) => (previous ? Math.round(((current - previous) / previous) * 100) : null)

const tiles = computed(() => {
  const s = stats.value!
  return [
    { label: 'Revenue', value: formatMoney(s.revenue), change: change(s.revenue, s.revenue_previous) },
    { label: 'Orders', value: s.orders.toLocaleString('en-IN'), change: change(s.orders, s.orders_previous) },
    { label: 'Average order', value: formatMoney(s.average_order_value), change: null },
    { label: 'New customers', value: s.new_customers.toLocaleString('en-IN'), change: null },
  ]
})

const chartPoints = computed(() => {
  const series = stats.value?.series ?? []
  const max = Math.max(...series.map((p) => p.revenue), 1)
  return series.map((p) => ({
    ...p,
    height: (p.revenue / max) * 100,
    label: new Date(p.date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' }),
  }))
})

const todo = computed(() => {
  const t = stats.value!.to_do
  return [
    { label: 'Orders to ship', count: t.to_ship, to: { name: 'orders', query: { status: 'processing' } } },
    { label: 'Return requests', count: t.returns, to: { name: 'orders', query: { return_status: 'requested' } } },
    { label: 'Refunds due', count: t.refunds, to: { name: 'orders', query: { payment_status: 'refund_pending' } } },
    { label: 'Reviews to moderate', count: t.reviews, to: { name: 'reviews' } },
    { label: 'Low stock products', count: t.low_stock, to: { name: 'products', query: { low_stock: '1' } } },
  ]
})

watch(days, load)
onMounted(load)
</script>
