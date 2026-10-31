<template>
  <PageHeader title="Products" description="Everything in the shop. Drafts and archived products are hidden from customers.">
    <RouterLink :to="{ name: 'products.create' }" class="btn btn-primary">Add product</RouterLink>
  </PageHeader>

  <div class="card overflow-hidden">
    <div class="flex flex-wrap gap-3 border-b border-slate-200 p-4">
      <input v-model="filters.search" type="search" class="form-input max-w-xs" placeholder="Search name or SKU" aria-label="Search products" />
      <select v-model="filters.status" class="form-input w-auto" aria-label="Status">
        <option value="">All statuses</option>
        <option value="active">Active</option>
        <option value="draft">Draft</option>
        <option value="archived">Archived</option>
      </select>
      <select v-model="filters.category_id" class="form-input w-auto" aria-label="Category">
        <option value="">All categories</option>
        <option v-for="category in categories" :key="category.id" :value="String(category.id)">{{ category.name }}</option>
      </select>
      <label class="flex items-center gap-2 text-sm text-slate-700">
        <input v-model="lowStock" type="checkbox" class="form-check" /> Low stock only
      </label>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
          <tr>
            <th class="px-4 py-3">Product</th>
            <th class="px-4 py-3">Category</th>
            <th class="px-4 py-3 text-right">Price</th>
            <th class="px-4 py-3 text-right">Stock</th>
            <th class="px-4 py-3">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="product in items" :key="product.id" class="cursor-pointer hover:bg-slate-50" @click="router.push({ name: 'products.edit', params: { id: product.id } })">
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <img v-if="product.images[0]" :src="product.images[0].url" alt="" class="size-10 rounded-lg object-cover" />
                <div v-else class="size-10 rounded-lg bg-slate-100" />
                <div>
                  <p class="font-medium text-slate-900">{{ product.name }} <span v-if="product.is_featured" class="text-xs text-brand-600">★ Featured</span></p>
                  <p class="text-xs text-slate-500">{{ product.sku }}</p>
                </div>
              </div>
            </td>
            <td class="px-4 py-3 text-slate-600">{{ product.category?.name }}</td>
            <td class="px-4 py-3 text-right">
              <span class="font-medium">{{ formatMoney(product.current_price) }}</span>
              <span v-if="product.current_price < product.price" class="ml-1 text-xs text-slate-400 line-through">{{ formatMoney(product.price) }}</span>
            </td>
            <td class="px-4 py-3 text-right" :class="product.stock === 0 ? 'font-semibold text-red-600' : product.stock <= 5 ? 'font-semibold text-amber-600' : ''">
              {{ product.stock }}
            </td>
            <td class="px-4 py-3"><StatusBadge :status="product.status" /></td>
          </tr>
          <tr v-if="!isLoading && !items.length">
            <td colspan="5" class="px-4 py-10 text-center text-slate-500">No products found.</td>
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
import { http } from '@/utils/http'
import { formatMoney } from '@/utils/money'
import type { Category, Product } from '@/types/shop'

const router = useRouter()
const route = useRoute()
const categories = ref<Category[]>([])
const lowStock = ref(route.query.low_stock === '1')

const { items, filters, page, meta, isLoading, load } = usePaginatedList<Product>('admin/products', {
  search: '',
  status: '',
  category_id: '',
  low_stock: lowStock.value ? '1' : '',
})

watch(lowStock, (value) => (filters.low_stock = value ? '1' : ''))

onMounted(async () => {
  load()
  const { data } = await http.get<{ data: Category[] }>('admin/categories')
  categories.value = data.data
})
</script>
