<script setup lang="ts">
import { computed } from 'vue'

/** Coloured pill for order, payment, review and other statuses. */
const props = defineProps<{ status: string | null | undefined; label?: string }>()

const tones: Record<string, string> = {
  // positive
  active: 'bg-emerald-100 text-emerald-700',
  approved: 'bg-emerald-100 text-emerald-700',
  delivered: 'bg-emerald-100 text-emerald-700',
  paid: 'bg-emerald-100 text-emerald-700',
  published: 'bg-emerald-100 text-emerald-700',
  completed: 'bg-emerald-100 text-emerald-700',
  replied: 'bg-emerald-100 text-emerald-700',
  // in progress
  processing: 'bg-sky-100 text-sky-700',
  shipped: 'bg-indigo-100 text-indigo-700',
  read: 'bg-sky-100 text-sky-700',
  // waiting
  pending: 'bg-amber-100 text-amber-700',
  requested: 'bg-amber-100 text-amber-700',
  refund_pending: 'bg-amber-100 text-amber-700',
  draft: 'bg-slate-200 text-slate-600',
  new: 'bg-brand-100 text-brand-700',
  // negative / closed
  cancelled: 'bg-red-100 text-red-700',
  rejected: 'bg-red-100 text-red-700',
  failed: 'bg-red-100 text-red-700',
  returned: 'bg-purple-100 text-purple-700',
  refunded: 'bg-purple-100 text-purple-700',
  archived: 'bg-slate-200 text-slate-600',
  inactive: 'bg-slate-200 text-slate-600',
}

const text = computed(() => props.label ?? (props.status ?? '-').replace(/_/g, ' '))
</script>

<template>
  <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold whitespace-nowrap capitalize" :class="tones[status ?? ''] ?? 'bg-slate-100 text-slate-600'">
    {{ text }}
  </span>
</template>
