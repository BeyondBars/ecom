<template>
  <PageHeader title="Brands">
    <button type="button" class="btn btn-primary" @click="edit()">Add brand</button>
  </PageHeader>

  <div class="card overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
      <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
        <tr>
          <th class="px-4 py-3">Brand</th>
          <th class="px-4 py-3">Website</th>
          <th class="px-4 py-3 text-right">Products</th>
          <th class="px-4 py-3">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <tr v-for="brand in brands" :key="brand.id" class="cursor-pointer hover:bg-slate-50" @click="edit(brand)">
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              <img v-if="brand.logo_url" :src="brand.logo_url" alt="" class="size-9 rounded-lg object-contain" />
              <span class="font-medium text-slate-900">{{ brand.name }}</span>
            </div>
          </td>
          <td class="px-4 py-3 text-slate-600">{{ brand.website ?? '-' }}</td>
          <td class="px-4 py-3 text-right">{{ brand.products_count }}</td>
          <td class="px-4 py-3"><StatusBadge :status="brand.is_active ? 'active' : 'inactive'" /></td>
        </tr>
        <tr v-if="!brands.length">
          <td colspan="4" class="px-4 py-10 text-center text-slate-500">No brands yet.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <AppModal v-if="form" :title="form.id ? 'Edit brand' : 'Add brand'" @close="form = null">
    <form id="brand-form" class="space-y-4" @submit.prevent="save">
      <div>
        <label class="form-label" for="brand-name">Name *</label>
        <input id="brand-name" v-model="form.name" class="form-input" required />
        <span v-if="errors.name" class="form-error">{{ errors.name }}</span>
      </div>
      <div>
        <label class="form-label" for="brand-website">Website</label>
        <input id="brand-website" v-model="form.website" type="url" class="form-input" placeholder="https://" />
        <span v-if="errors.website" class="form-error">{{ errors.website }}</span>
      </div>
      <div>
        <label class="form-label" for="brand-description">Description</label>
        <textarea id="brand-description" v-model="form.description" rows="3" class="form-input" />
      </div>
      <ImageUploadField id="brand-logo" v-model="form.logo_url" label="Logo" />
      <label class="flex items-center gap-2 text-sm text-slate-700">
        <input v-model="form.is_active" type="checkbox" class="form-check" /> Visible in shop
      </label>
    </form>
    <template #footer>
      <button v-if="form.id" type="button" class="btn btn-danger mr-auto" @click="remove">Delete</button>
      <button type="button" class="btn btn-secondary" @click="form = null">Cancel</button>
      <button type="submit" form="brand-form" class="btn btn-primary">Save</button>
    </template>
  </AppModal>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useToast } from 'vue-toast-notification'
import AppModal from '@/components/app/AppModal.vue'
import PageHeader from '@/components/app/PageHeader.vue'
import ImageUploadField from '@/components/shop/ImageUploadField.vue'
import StatusBadge from '@/components/shop/StatusBadge.vue'
import confirm from '@/utils/alert'
import { http } from '@/utils/http'
import { validationErrors } from '@/utils/money'
import type { Brand } from '@/types/shop'

type Form = Pick<Brand, 'name' | 'website' | 'description' | 'logo_url' | 'is_active'> & { id?: number }

const toast = useToast()
const brands = ref<Brand[]>([])
const form = ref<Form | null>(null)
const errors = ref<Record<string, string>>({})

const load = async () => {
  const { data } = await http.get<{ data: Brand[] }>('admin/brands')
  brands.value = data.data
}

const edit = (brand?: Brand) => {
  errors.value = {}
  form.value = brand
    ? { id: brand.id, name: brand.name, website: brand.website, description: brand.description, logo_url: brand.logo_url, is_active: brand.is_active }
    : { name: '', website: null, description: null, logo_url: null, is_active: true }
}

const save = async () => {
  if (!form.value) return
  try {
    const { id, ...payload } = form.value
    await (id ? http.put(`admin/brands/${id}`, { ...payload, website: payload.website || null }) : http.post('admin/brands', { ...payload, website: payload.website || null }))
    toast.success('Brand saved.')
    form.value = null
    load()
  } catch (error) {
    errors.value = validationErrors(error)
  }
}

const remove = async () => {
  if (!form.value?.id || !(await confirm({ title: 'Delete this brand?', text: 'Its products stay in the shop without a brand.' }))) return
  await http.delete(`admin/brands/${form.value.id}`)
  toast.success('Brand deleted.')
  form.value = null
  load()
}

onMounted(load)
</script>
