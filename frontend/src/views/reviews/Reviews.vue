<template>
  <PageHeader title="Reviews" description="Reviews appear on the storefront once approved. Featured reviews are shown first." />

  <div class="card overflow-hidden">
    <div class="flex flex-wrap gap-3 border-b border-slate-200 p-4">
      <select v-model="filters.status" class="form-input w-auto" aria-label="Status">
        <option value="">All</option>
        <option value="pending">Waiting for approval</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
      </select>
      <select v-model="filters.rating" class="form-input w-auto" aria-label="Rating">
        <option value="">Any rating</option>
        <option v-for="stars in [5, 4, 3, 2, 1]" :key="stars" :value="String(stars)">{{ stars }} stars</option>
      </select>
      <input v-model="filters.search" type="search" class="form-input max-w-xs" placeholder="Search text" aria-label="Search reviews" />
    </div>

    <ul class="divide-y divide-slate-100">
      <li v-for="review in items" :key="review.id" class="flex flex-col gap-3 p-4 sm:flex-row">
        <div class="flex-1 space-y-1 text-sm">
          <div class="flex flex-wrap items-center gap-2">
            <span class="text-amber-500" :aria-label="`${review.rating} out of 5 stars`">{{ '★'.repeat(review.rating) }}<span class="text-slate-300">{{ '★'.repeat(5 - review.rating) }}</span></span>
            <StatusBadge :status="review.status" />
            <span v-if="review.verified_purchase" class="text-xs font-semibold text-emerald-600">✓ Verified purchase</span>
            <span v-if="review.is_featured" class="text-xs font-semibold text-brand-600">★ Featured</span>
          </div>
          <p v-if="review.title" class="font-semibold text-slate-900">{{ review.title }}</p>
          <p class="text-slate-700">{{ review.comment }}</p>
          <p class="text-xs text-slate-500">
            {{ review.author }} ({{ review.author_email }}) on
            <RouterLink v-if="review.product" :to="{ name: 'products.edit', params: { id: review.product.id } }" class="link">{{ review.product.name }}</RouterLink>
            · {{ $date(review.created_at, { format: 'DD MMM YYYY' }) }}
          </p>
        </div>
        <div class="flex shrink-0 flex-wrap items-start gap-2">
          <button v-if="review.status !== 'approved'" type="button" class="btn btn-primary px-3 py-1.5" @click="update(review, { status: 'approved' })">Approve</button>
          <button v-if="review.status !== 'rejected'" type="button" class="btn btn-secondary px-3 py-1.5" @click="update(review, { status: 'rejected' })">Reject</button>
          <button type="button" class="btn btn-secondary px-3 py-1.5" @click="update(review, { is_featured: !review.is_featured })">{{ review.is_featured ? 'Unfeature' : 'Feature' }}</button>
          <button type="button" class="btn btn-secondary px-3 py-1.5 text-red-600" @click="remove(review)">Delete</button>
        </div>
      </li>
      <li v-if="!isLoading && !items.length" class="p-10 text-center text-sm text-slate-500">No reviews match.</li>
    </ul>
    <PaginationBar v-model="page" :meta="meta" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useToast } from 'vue-toast-notification'
import PageHeader from '@/components/app/PageHeader.vue'
import PaginationBar from '@/components/shop/PaginationBar.vue'
import StatusBadge from '@/components/shop/StatusBadge.vue'
import { usePaginatedList } from '@/composables/use-paginated-list'
import confirm from '@/utils/alert'
import { http } from '@/utils/http'
import type { Review } from '@/types/shop'

const toast = useToast()
const { items, filters, page, meta, isLoading, load } = usePaginatedList<Review>('admin/reviews', { status: 'pending', rating: '', search: '' })

const update = async (review: Review, changes: Partial<Review>) => {
  const { data } = await http.put<{ data: Review }>(`admin/reviews/${review.id}`, changes)
  Object.assign(review, data.data)
  toast.success('Review updated.')
}

const remove = async (review: Review) => {
  if (!(await confirm({ title: 'Delete this review?' }))) return
  await http.delete(`admin/reviews/${review.id}`)
  load()
}

onMounted(load)
</script>
