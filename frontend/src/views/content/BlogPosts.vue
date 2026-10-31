<template>
  <PageHeader title="Blog">
    <RouterLink :to="{ name: 'blog.comments' }" class="btn btn-secondary">Comments</RouterLink>
    <RouterLink :to="{ name: 'blog.create' }" class="btn btn-primary">Write a post</RouterLink>
  </PageHeader>

  <div class="card overflow-hidden">
    <div class="flex flex-wrap gap-3 border-b border-slate-200 p-4">
      <input v-model="filters.search" type="search" class="form-input max-w-xs" placeholder="Search titles" aria-label="Search posts" />
      <select v-model="filters.status" class="form-input w-auto" aria-label="Status">
        <option value="">All</option>
        <option value="published">Published</option>
        <option value="draft">Drafts</option>
      </select>
    </div>
    <table class="min-w-full divide-y divide-slate-200 text-sm">
      <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
        <tr>
          <th class="px-4 py-3">Title</th>
          <th class="px-4 py-3">Author</th>
          <th class="px-4 py-3">Published</th>
          <th class="px-4 py-3 text-right">Comments</th>
          <th class="px-4 py-3">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <tr v-for="post in items" :key="post.id" class="cursor-pointer hover:bg-slate-50" @click="router.push({ name: 'blog.edit', params: { id: post.id } })">
          <td class="px-4 py-3 font-medium text-slate-900">{{ post.title }}</td>
          <td class="px-4 py-3 text-slate-600">{{ post.author ?? '-' }}</td>
          <td class="px-4 py-3 text-slate-500">{{ post.published_at ? $date(post.published_at, { format: 'DD MMM YYYY' }) : '-' }}</td>
          <td class="px-4 py-3 text-right">{{ post.comments_count }}</td>
          <td class="px-4 py-3"><StatusBadge :status="post.status" /></td>
        </tr>
        <tr v-if="!isLoading && !items.length">
          <td colspan="5" class="px-4 py-10 text-center text-slate-500">No posts yet.</td>
        </tr>
      </tbody>
    </table>
    <PaginationBar v-model="page" :meta="meta" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '@/components/app/PageHeader.vue'
import PaginationBar from '@/components/shop/PaginationBar.vue'
import StatusBadge from '@/components/shop/StatusBadge.vue'
import { usePaginatedList } from '@/composables/use-paginated-list'
import type { BlogPost } from '@/types/shop'

const router = useRouter()
const { items, filters, page, meta, isLoading, load } = usePaginatedList<BlogPost>('admin/blog-posts', { search: '', status: '' })

onMounted(load)
</script>
