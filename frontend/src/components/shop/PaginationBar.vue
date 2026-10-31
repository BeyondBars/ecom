<script setup lang="ts">
import type { Paginated } from '@/types/user'

defineProps<{ meta: Paginated<unknown>['meta'] | null }>()
const page = defineModel<number>({ required: true })
</script>

<template>
  <div v-if="meta && meta.last_page > 1" class="flex items-center justify-between border-t border-slate-200 px-4 py-3 text-sm">
    <span class="text-slate-500">Page {{ meta.current_page }} of {{ meta.last_page }} · {{ meta.total }} total</span>
    <div class="flex gap-2">
      <button type="button" class="btn btn-secondary px-3 py-1.5" :disabled="page <= 1" @click="page--">Previous</button>
      <button type="button" class="btn btn-secondary px-3 py-1.5" :disabled="page >= meta.last_page" @click="page++">Next</button>
    </div>
  </div>
</template>
