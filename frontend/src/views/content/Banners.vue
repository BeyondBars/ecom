<template>
  <PageHeader title="Banners" description="Promotional slides on the storefront home page.">
    <button type="button" class="btn btn-primary" @click="edit()">Add banner</button>
  </PageHeader>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
    <button v-for="banner in banners" :key="banner.id" type="button" class="card overflow-hidden text-left hover:border-brand-500" @click="edit(banner)">
      <img :src="banner.image_url" alt="" class="aspect-[16/7] w-full bg-slate-100 object-cover" />
      <div class="space-y-1 p-4">
        <div class="flex items-center justify-between gap-2">
          <p class="font-semibold text-slate-900">{{ banner.title }}</p>
          <StatusBadge :status="banner.is_active ? 'active' : 'inactive'" />
        </div>
        <p class="text-sm text-slate-500">{{ placements[banner.placement] }} · position {{ banner.sort_order }}</p>
        <p v-if="banner.starts_at || banner.ends_at" class="text-xs text-slate-500">
          {{ banner.starts_at ? $date(banner.starts_at, { format: 'DD MMM' }) : 'Now' }} – {{ banner.ends_at ? $date(banner.ends_at, { format: 'DD MMM YYYY' }) : 'no end' }}
        </p>
      </div>
    </button>
    <p v-if="!banners.length" class="text-slate-500">No banners yet.</p>
  </div>

  <AppModal v-if="form" :title="form.id ? 'Edit banner' : 'Add banner'" @close="form = null">
    <form id="banner-form" class="space-y-4" @submit.prevent="save">
      <div>
        <label class="form-label" for="banner-title">Title *</label>
        <input id="banner-title" v-model="form.title" class="form-input" required />
      </div>
      <div>
        <label class="form-label" for="banner-subtitle">Subtitle</label>
        <input id="banner-subtitle" v-model="form.subtitle" class="form-input" />
      </div>
      <ImageUploadField id="banner-image" v-model="form.image_url" />
      <span v-if="errors.image_url" class="form-error">{{ errors.image_url }}</span>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="form-label" for="banner-link">Link</label>
          <input id="banner-link" v-model="form.link_url" class="form-input" placeholder="/sale" />
        </div>
        <div>
          <label class="form-label" for="banner-button">Button text</label>
          <input id="banner-button" v-model="form.button_text" class="form-input" placeholder="Shop now" />
        </div>
        <div>
          <label class="form-label" for="banner-placement">Placement</label>
          <select id="banner-placement" v-model="form.placement" class="form-input">
            <option v-for="(label, value) in placements" :key="value" :value="value">{{ label }}</option>
          </select>
        </div>
        <div>
          <label class="form-label" for="banner-order">Position</label>
          <input id="banner-order" v-model.number="form.sort_order" type="number" min="0" class="form-input" />
        </div>
        <div>
          <label class="form-label" for="banner-starts">Show from</label>
          <input id="banner-starts" v-model="form.starts_at" type="date" class="form-input" />
        </div>
        <div>
          <label class="form-label" for="banner-ends">Show until</label>
          <input id="banner-ends" v-model="form.ends_at" type="date" class="form-input" />
          <span v-if="errors.ends_at" class="form-error">{{ errors.ends_at }}</span>
        </div>
      </div>
      <label class="flex items-center gap-2 text-sm text-slate-700">
        <input v-model="form.is_active" type="checkbox" class="form-check" /> Active
      </label>
    </form>
    <template #footer>
      <button v-if="form.id" type="button" class="btn btn-danger mr-auto" @click="remove">Delete</button>
      <button type="button" class="btn btn-secondary" @click="form = null">Cancel</button>
      <button type="submit" form="banner-form" class="btn btn-primary">Save</button>
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
import type { Banner } from '@/types/shop'

type Form = Omit<Banner, 'id' | 'image_url'> & { id?: number; image_url: string | null }

const placements: Record<Banner['placement'], string> = {
  home_hero: 'Home page slider',
  home_promo: 'Home page promo',
  category_top: 'Top of category pages',
}

const toast = useToast()
const banners = ref<Banner[]>([])
const form = ref<Form | null>(null)
const errors = ref<Record<string, string>>({})

const load = async () => {
  const { data } = await http.get<{ data: Banner[] }>('admin/banners')
  banners.value = data.data
}

const edit = (banner?: Banner) => {
  errors.value = {}
  form.value = banner
    ? { ...banner, starts_at: banner.starts_at?.slice(0, 10) ?? null, ends_at: banner.ends_at?.slice(0, 10) ?? null }
    : { title: '', subtitle: null, image_url: null, link_url: null, button_text: null, placement: 'home_hero', sort_order: banners.value.length, is_active: true, starts_at: null, ends_at: null }
}

const save = async () => {
  if (!form.value) return
  const { id, ...payload } = form.value
  try {
    await (id ? http.put(`admin/banners/${id}`, payload) : http.post('admin/banners', payload))
    toast.success('Banner saved.')
    form.value = null
    load()
  } catch (error) {
    errors.value = validationErrors(error)
  }
}

const remove = async () => {
  if (!form.value?.id || !(await confirm({ title: 'Delete this banner?' }))) return
  await http.delete(`admin/banners/${form.value.id}`)
  toast.success('Banner deleted.')
  form.value = null
  load()
}

onMounted(load)
</script>
