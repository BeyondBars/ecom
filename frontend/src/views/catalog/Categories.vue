<template>
  <PageHeader title="Categories" description="Sub-categories show up inside their parent on the storefront.">
    <button type="button" class="btn btn-primary" @click="edit()">Add category</button>
  </PageHeader>

  <div class="card overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
      <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
        <tr>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Parent</th>
          <th class="px-4 py-3 text-right">Products</th>
          <th class="px-4 py-3 text-right">Order</th>
          <th class="px-4 py-3">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <tr v-for="category in categories" :key="category.id" class="cursor-pointer hover:bg-slate-50" @click="edit(category)">
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              <img v-if="category.image_url" :src="category.image_url" alt="" class="size-9 rounded-lg object-cover" />
              <span class="font-medium text-slate-900">{{ category.name }}</span>
            </div>
          </td>
          <td class="px-4 py-3 text-slate-600">{{ category.parent?.name ?? '-' }}</td>
          <td class="px-4 py-3 text-right">{{ category.products_count }}</td>
          <td class="px-4 py-3 text-right text-slate-500">{{ category.sort_order }}</td>
          <td class="px-4 py-3"><StatusBadge :status="category.is_active ? 'active' : 'inactive'" /></td>
        </tr>
        <tr v-if="!categories.length">
          <td colspan="5" class="px-4 py-10 text-center text-slate-500">No categories yet.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <AppModal v-if="form" :title="form.id ? 'Edit category' : 'Add category'" @close="form = null">
    <form id="category-form" class="space-y-4" @submit.prevent="save">
      <div>
        <label class="form-label" for="category-name">Name *</label>
        <input id="category-name" v-model="form.name" class="form-input" required />
        <span v-if="errors.name" class="form-error">{{ errors.name }}</span>
      </div>
      <div>
        <label class="form-label" for="category-parent">Parent category</label>
        <select id="category-parent" v-model="form.parent_id" class="form-input">
          <option :value="null">None (top level)</option>
          <option v-for="category in categories.filter((c) => c.id !== form?.id && !c.parent_id)" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>
      <div>
        <label class="form-label" for="category-description">Description</label>
        <textarea id="category-description" v-model="form.description" rows="3" class="form-input" />
      </div>
      <ImageUploadField id="category-image" v-model="form.image_url" />
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="form-label" for="category-order">Display order</label>
          <input id="category-order" v-model.number="form.sort_order" type="number" min="0" class="form-input" />
        </div>
        <label class="mt-6 flex items-center gap-2 text-sm text-slate-700">
          <input v-model="form.is_active" type="checkbox" class="form-check" /> Visible in shop
        </label>
      </div>
      <p v-if="errors.message" class="form-error">{{ errors.message }}</p>
    </form>
    <template #footer>
      <button v-if="form.id" type="button" class="btn btn-danger mr-auto" @click="remove">Delete</button>
      <button type="button" class="btn btn-secondary" @click="form = null">Cancel</button>
      <button type="submit" form="category-form" class="btn btn-primary">Save</button>
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
import { errorMessage, validationErrors } from '@/utils/money'
import type { Category } from '@/types/shop'

type Form = Pick<Category, 'name' | 'parent_id' | 'description' | 'image_url' | 'is_active' | 'sort_order'> & { id?: number }

const toast = useToast()
const categories = ref<Category[]>([])
const form = ref<Form | null>(null)
const errors = ref<Record<string, string>>({})

const load = async () => {
  const { data } = await http.get<{ data: Category[] }>('admin/categories')
  categories.value = data.data
}

const edit = (category?: Category) => {
  errors.value = {}
  form.value = category
    ? { id: category.id, name: category.name, parent_id: category.parent_id, description: category.description, image_url: category.image_url, is_active: category.is_active, sort_order: category.sort_order }
    : { name: '', parent_id: null, description: '', image_url: null, is_active: true, sort_order: categories.value.length }
}

const save = async () => {
  if (!form.value) return
  try {
    const { id, ...payload } = form.value
    await (id ? http.put(`admin/categories/${id}`, payload) : http.post('admin/categories', payload))
    toast.success('Category saved.')
    form.value = null
    load()
  } catch (error) {
    errors.value = validationErrors(error)
  }
}

const remove = async () => {
  if (!form.value?.id || !(await confirm({ title: 'Delete this category?' }))) return
  try {
    await http.delete(`admin/categories/${form.value.id}`, { throwForbiddens: true } as object)
    toast.success('Category deleted.')
    form.value = null
    load()
  } catch (error) {
    errors.value = { message: errorMessage(error) }
  }
}

onMounted(load)
</script>
