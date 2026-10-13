<template>
  <form v-if="form" class="card space-y-6 p-5" @submit.prevent="save">
    <div>
      <h2 class="text-base font-semibold text-slate-900">Store</h2>
      <p class="text-sm text-slate-500">Shown in the storefront header, footer and emails.</p>
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <div>
        <label class="form-label" for="store_name">Store name</label>
        <input id="store_name" v-model="form.store_name" class="form-input" />
      </div>
      <div>
        <label class="form-label" for="announcement">Announcement bar</label>
        <input id="announcement" v-model="form.announcement" class="form-input" placeholder="Free shipping on orders over ₹999" />
      </div>
      <div>
        <label class="form-label" for="support_email">Support email</label>
        <input id="support_email" v-model="form.support_email" type="email" class="form-input" />
        <span v-if="errors.support_email" class="form-error">{{ errors.support_email }}</span>
      </div>
      <div>
        <label class="form-label" for="support_phone">Support phone</label>
        <input id="support_phone" v-model="form.support_phone" class="form-input" placeholder="+91 …" />
      </div>
      <div class="md:col-span-2">
        <label class="form-label" for="store_address">Address</label>
        <textarea id="store_address" v-model="form.store_address" rows="2" class="form-input" />
      </div>
    </div>

    <div>
      <h2 class="text-base font-semibold text-slate-900">Shipping & payments</h2>
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
      <div>
        <label class="form-label" for="free_shipping_threshold">Free standard shipping from (₹)</label>
        <input id="free_shipping_threshold" v-model.number="form.free_shipping_threshold" type="number" min="0" class="form-input" />
      </div>
      <div>
        <label class="form-label" for="standard_shipping_fee">Standard shipping fee (₹)</label>
        <input id="standard_shipping_fee" v-model.number="form.standard_shipping_fee" type="number" min="0" class="form-input" />
      </div>
      <div>
        <label class="form-label" for="express_shipping_fee">Express shipping fee (₹)</label>
        <input id="express_shipping_fee" v-model.number="form.express_shipping_fee" type="number" min="0" class="form-input" />
      </div>
      <label class="flex items-center gap-2 text-sm text-slate-700 md:mt-6">
        <input v-model="form.cod_enabled" type="checkbox" class="form-check" /> Offer Cash on Delivery
      </label>
      <div>
        <label class="form-label" for="cod_max_order_total">COD limit per order (₹)</label>
        <input id="cod_max_order_total" v-model.number="form.cod_max_order_total" type="number" min="0" class="form-input" :disabled="!form.cod_enabled" placeholder="No limit" />
      </div>
      <div>
        <label class="form-label" for="return_window_days">Return window (days)</label>
        <input id="return_window_days" v-model.number="form.return_window_days" type="number" min="0" max="365" class="form-input" />
      </div>
    </div>

    <div>
      <h2 class="text-base font-semibold text-slate-900">Social links</h2>
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <div v-for="network in socials" :key="network">
        <label class="form-label capitalize" :for="`${network}_url`">{{ network }}</label>
        <input :id="`${network}_url`" v-model="form[`${network}_url`]" type="url" class="form-input" placeholder="https://" />
        <span v-if="errors[`${network}_url`]" class="form-error">{{ errors[`${network}_url`] }}</span>
      </div>
    </div>

    <div class="flex justify-end">
      <button type="submit" class="btn btn-primary" :disabled="isSaving">{{ isSaving ? 'Saving…' : 'Save settings' }}</button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useToast } from 'vue-toast-notification'
import { http } from '@/utils/http'
import { validationErrors } from '@/utils/money'
import type { ShopSettings } from '@/types/shop'

const socials = ['instagram', 'facebook', 'youtube', 'twitter'] as const
const toast = useToast()
const form = ref<ShopSettings | null>(null)
const errors = ref<Record<string, string>>({})
const isSaving = ref(false)

const save = async () => {
  if (!form.value) return
  isSaving.value = true
  errors.value = {}
  // Empty optional fields are saved as "not set"
  const payload = Object.fromEntries(Object.entries(form.value).map(([key, value]) => [key, value === '' ? null : value]))
  try {
    const { data } = await http.put<{ data: ShopSettings }>('admin/settings', payload)
    form.value = data.data
    toast.success('Settings saved.')
  } catch (error) {
    errors.value = validationErrors(error)
  } finally {
    isSaving.value = false
  }
}

onMounted(async () => {
  const { data } = await http.get<{ data: ShopSettings }>('admin/settings')
  form.value = data.data
})
</script>
