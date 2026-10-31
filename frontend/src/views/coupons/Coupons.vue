<template>
  <PageHeader title="Coupons" description="Codes customers enter in the cart or at checkout.">
    <button type="button" class="btn btn-primary" @click="edit()">Add coupon</button>
  </PageHeader>

  <div class="card overflow-hidden">
    <div class="flex flex-wrap gap-3 border-b border-slate-200 p-4">
      <input v-model="filters.search" type="search" class="form-input max-w-xs" placeholder="Search code" aria-label="Search coupons" />
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
          <tr>
            <th class="px-4 py-3">Code</th>
            <th class="px-4 py-3">Discount</th>
            <th class="px-4 py-3">Minimum</th>
            <th class="px-4 py-3">Used</th>
            <th class="px-4 py-3">Expires</th>
            <th class="px-4 py-3">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="coupon in items" :key="coupon.id" class="cursor-pointer hover:bg-slate-50" @click="edit(coupon)">
            <td class="px-4 py-3">
              <p class="font-mono font-semibold text-slate-900">{{ coupon.code }}</p>
              <p class="text-xs text-slate-500">{{ coupon.description }}</p>
            </td>
            <td class="px-4 py-3">{{ describe(coupon) }}</td>
            <td class="px-4 py-3">{{ coupon.min_subtotal ? formatMoney(coupon.min_subtotal) : '-' }}</td>
            <td class="px-4 py-3">{{ coupon.used_count }}{{ coupon.usage_limit ? ` / ${coupon.usage_limit}` : '' }}</td>
            <td class="px-4 py-3 text-slate-500">{{ coupon.expires_at ? $date(coupon.expires_at, { format: 'DD MMM YYYY' }) : 'Never' }}</td>
            <td class="px-4 py-3"><StatusBadge :status="status(coupon)" /></td>
          </tr>
          <tr v-if="!isLoading && !items.length">
            <td colspan="6" class="px-4 py-10 text-center text-slate-500">No coupons yet.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <PaginationBar v-model="page" :meta="meta" />
  </div>

  <AppModal v-if="form" :title="form.id ? `Edit ${form.code}` : 'Add coupon'" @close="form = null">
    <form id="coupon-form" class="space-y-4" @submit.prevent="save">
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="form-label" for="coupon-code">Code *</label>
          <input id="coupon-code" v-model="form.code" class="form-input font-mono uppercase" required />
          <span v-if="errors.code" class="form-error">{{ errors.code }}</span>
        </div>
        <div>
          <label class="form-label" for="coupon-type">Type</label>
          <select id="coupon-type" v-model="form.type" class="form-input">
            <option value="percent">Percentage off</option>
            <option value="fixed">Fixed amount off</option>
            <option value="free_shipping">Free shipping</option>
          </select>
        </div>
      </div>
      <div>
        <label class="form-label" for="coupon-description">Description (shown to customers)</label>
        <input id="coupon-description" v-model="form.description" class="form-input" placeholder="10% off your order" />
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div v-if="form.type !== 'free_shipping'">
          <label class="form-label" for="coupon-value">{{ form.type === 'percent' ? 'Percent off *' : 'Amount off (₹) *' }}</label>
          <input id="coupon-value" v-model.number="form.value" type="number" min="0" :max="form.type === 'percent' ? 100 : undefined" class="form-input" required />
          <span v-if="errors.value" class="form-error">{{ errors.value }}</span>
        </div>
        <div v-if="form.type === 'percent'">
          <label class="form-label" for="coupon-max">Maximum discount (₹)</label>
          <input id="coupon-max" v-model.number="form.max_discount" type="number" min="0" class="form-input" placeholder="No limit" />
        </div>
        <div>
          <label class="form-label" for="coupon-min">Minimum order (₹)</label>
          <input id="coupon-min" v-model.number="form.min_subtotal" type="number" min="0" class="form-input" />
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="form-label" for="coupon-starts">Starts</label>
          <input id="coupon-starts" v-model="form.starts_at" type="date" class="form-input" />
        </div>
        <div>
          <label class="form-label" for="coupon-expires">Expires</label>
          <input id="coupon-expires" v-model="form.expires_at" type="date" class="form-input" />
          <span v-if="errors.expires_at" class="form-error">{{ errors.expires_at }}</span>
        </div>
        <div>
          <label class="form-label" for="coupon-limit">Total uses allowed</label>
          <input id="coupon-limit" v-model.number="form.usage_limit" type="number" min="1" class="form-input" placeholder="Unlimited" />
        </div>
        <div>
          <label class="form-label" for="coupon-limit-user">Uses per customer</label>
          <input id="coupon-limit-user" v-model.number="form.usage_limit_per_user" type="number" min="1" class="form-input" placeholder="Unlimited" />
        </div>
      </div>
      <label class="flex items-center gap-2 text-sm text-slate-700">
        <input v-model="form.is_active" type="checkbox" class="form-check" /> Active
      </label>
    </form>
    <template #footer>
      <button v-if="form.id" type="button" class="btn btn-danger mr-auto" @click="remove">Delete</button>
      <button type="button" class="btn btn-secondary" @click="form = null">Cancel</button>
      <button type="submit" form="coupon-form" class="btn btn-primary">Save</button>
    </template>
  </AppModal>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useToast } from 'vue-toast-notification'
import AppModal from '@/components/app/AppModal.vue'
import PageHeader from '@/components/app/PageHeader.vue'
import PaginationBar from '@/components/shop/PaginationBar.vue'
import StatusBadge from '@/components/shop/StatusBadge.vue'
import { usePaginatedList } from '@/composables/use-paginated-list'
import confirm from '@/utils/alert'
import { http } from '@/utils/http'
import { formatMoney, validationErrors } from '@/utils/money'
import type { Coupon, CouponType } from '@/types/shop'

type Nullable<T> = T | null | ''
type Form = {
  id?: number
  code: string
  description: string | null
  type: CouponType
  value: number
  min_subtotal: number
  max_discount: Nullable<number>
  starts_at: string | null
  expires_at: string | null
  usage_limit: Nullable<number>
  usage_limit_per_user: Nullable<number>
  is_active: boolean
}

const toast = useToast()
const { items, filters, page, meta, isLoading, load } = usePaginatedList<Coupon>('admin/coupons', { search: '' })
const form = ref<Form | null>(null)
const errors = ref<Record<string, string>>({})

const describe = (c: Coupon) =>
  c.type === 'free_shipping' ? 'Free shipping' : c.type === 'percent' ? `${c.value}% off${c.max_discount ? ` (max ${formatMoney(c.max_discount)})` : ''}` : `${formatMoney(c.value)} off`

const status = (c: Coupon) => {
  if (!c.is_active) return 'inactive'
  if (c.expires_at && new Date(c.expires_at) < new Date()) return 'archived'
  return 'active'
}

const day = (date: string | null) => (date ? date.slice(0, 10) : null)
const orNull = (value: Nullable<number>) => (value === '' || value === null ? null : value)

const edit = (coupon?: Coupon) => {
  errors.value = {}
  form.value = coupon
    ? { ...coupon, starts_at: day(coupon.starts_at), expires_at: day(coupon.expires_at) }
    : { code: '', description: '', type: 'percent', value: 10, min_subtotal: 0, max_discount: null, starts_at: null, expires_at: null, usage_limit: null, usage_limit_per_user: null, is_active: true }
}

const save = async () => {
  if (!form.value) return
  const { id, ...rest } = form.value
  const payload = {
    ...rest,
    max_discount: orNull(rest.max_discount),
    usage_limit: orNull(rest.usage_limit),
    usage_limit_per_user: orNull(rest.usage_limit_per_user),
    starts_at: rest.starts_at || null,
    expires_at: rest.expires_at ? `${rest.expires_at} 23:59:59` : null,
  }
  try {
    await (id ? http.put(`admin/coupons/${id}`, payload) : http.post('admin/coupons', payload))
    toast.success('Coupon saved.')
    form.value = null
    load()
  } catch (error) {
    errors.value = validationErrors(error)
  }
}

const remove = async () => {
  if (!form.value?.id || !(await confirm({ title: `Delete ${form.value.code}?`, text: 'Past orders keep the discount they received.' }))) return
  await http.delete(`admin/coupons/${form.value.id}`)
  toast.success('Coupon deleted.')
  form.value = null
  load()
}

onMounted(load)
</script>
