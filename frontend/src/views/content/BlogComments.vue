<template>
  <PageHeader title="Blog comments" description="Comments appear under posts once approved.">
    <RouterLink :to="{ name: 'blog' }" class="link-muted">← Back to blog</RouterLink>
  </PageHeader>

  <div class="card overflow-hidden">
    <div class="border-b border-slate-200 p-4">
      <select v-model="filters.status" class="form-input w-auto" aria-label="Status">
        <option value="">All</option>
        <option value="pending">Waiting for approval</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>
    <ul class="divide-y divide-slate-100">
      <li v-for="comment in items" :key="comment.id" class="flex flex-col gap-3 p-4 text-sm sm:flex-row">
        <div class="flex-1 space-y-1">
          <StatusBadge :status="comment.status" />
          <p class="text-slate-700">{{ comment.content }}</p>
          <p class="text-xs text-slate-500">{{ comment.author }} ({{ comment.author_email }}) on "{{ comment.post?.title }}" · {{ $date(comment.created_at, { format: 'DD MMM YYYY' }) }}</p>
        </div>
        <div class="flex shrink-0 gap-2">
          <button v-if="comment.status !== 'approved'" type="button" class="btn btn-primary px-3 py-1.5" @click="setStatus(comment, 'approved')">Approve</button>
          <button v-if="comment.status !== 'rejected'" type="button" class="btn btn-secondary px-3 py-1.5" @click="setStatus(comment, 'rejected')">Reject</button>
          <button type="button" class="btn btn-secondary px-3 py-1.5 text-red-600" @click="remove(comment)">Delete</button>
        </div>
      </li>
      <li v-if="!isLoading && !items.length" class="p-10 text-center text-sm text-slate-500">No comments match.</li>
    </ul>
    <PaginationBar v-model="page" :meta="meta" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageHeader from '@/components/app/PageHeader.vue'
import PaginationBar from '@/components/shop/PaginationBar.vue'
import StatusBadge from '@/components/shop/StatusBadge.vue'
import { usePaginatedList } from '@/composables/use-paginated-list'
import confirm from '@/utils/alert'
import { http } from '@/utils/http'
import type { BlogComment, ModerationStatus } from '@/types/shop'

const { items, filters, page, meta, isLoading, load } = usePaginatedList<BlogComment>('admin/blog-comments', { status: 'pending' })

const setStatus = async (comment: BlogComment, status: ModerationStatus) => {
  await http.put(`admin/blog-comments/${comment.id}`, { status })
  comment.status = status
}

const remove = async (comment: BlogComment) => {
  if (!(await confirm({ title: 'Delete this comment?' }))) return
  await http.delete(`admin/blog-comments/${comment.id}`)
  load()
}

onMounted(load)
</script>
