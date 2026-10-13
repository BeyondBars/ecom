import { reactive, ref, watch, type Ref } from 'vue'
import { watchDebounced } from '@vueuse/core'
import { http } from '@/utils/http'
import type { Paginated } from '@/types/user'

/**
 * Page-by-page list backed by a Laravel paginated endpoint with spatie query-builder filters.
 * Filters are sent as filter[name]=value; changing a filter goes back to page 1.
 */
export function usePaginatedList<T>(
  endpoint: string,
  initialFilters: Record<string, string> = {},
  options: { sort?: string; perPage?: number } = {}
) {
  const items = ref([]) as Ref<T[]>
  const filters = reactive<Record<string, string>>({ ...initialFilters })
  const page = ref(1)
  const meta = ref<Paginated<T>['meta'] | null>(null)
  const isLoading = ref(false)
  const sort = ref(options.sort ?? '')

  const load = async () => {
    isLoading.value = true
    try {
      const params: Record<string, string | number> = { page: page.value, per_page: options.perPage ?? 15 }
      for (const [key, value] of Object.entries(filters)) {
        if (value !== '' && value !== null && value !== undefined) params[`filter[${key}]`] = value
      }
      if (sort.value) params.sort = sort.value

      const { data } = await http.get<Paginated<T>>(endpoint, { params })
      items.value = data.data
      meta.value = data.meta
    } finally {
      isLoading.value = false
    }
  }

  watchDebounced(
    () => ({ ...filters }),
    () => {
      page.value = 1
      load()
    },
    { debounce: 300, deep: true }
  )
  watch([page, sort], load)

  return { items, filters, page, meta, sort, isLoading, load }
}
