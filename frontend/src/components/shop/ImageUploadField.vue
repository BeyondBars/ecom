<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from 'vue-toast-notification'
import { http } from '@/utils/http'
import { errorMessage } from '@/utils/money'

/** Image picker: upload a file (stored on the API's public disk) or paste a URL. */
const props = withDefaults(defineProps<{ label?: string; id?: string }>(), { label: 'Image', id: 'image-url' })
const url = defineModel<string | null>({ default: null })
const isUploading = ref(false)
const toast = useToast()

const upload = async (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (!file) return

  const form = new FormData()
  form.append('file', file)
  isUploading.value = true
  try {
    const { data } = await http.post<{ data: { url: string } }>('admin/uploads', form, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    url.value = data.data.url
  } catch (error) {
    toast.error(errorMessage(error, 'Upload failed. Use a JPG, PNG or WebP image up to 5 MB.'))
  } finally {
    isUploading.value = false
    ;(event.target as HTMLInputElement).value = ''
  }
}
</script>

<template>
  <div>
    <label class="form-label" :for="props.id">{{ label }}</label>
    <div class="flex items-start gap-3">
      <img v-if="url" :src="url" alt="" class="size-16 shrink-0 rounded-lg border border-slate-200 object-cover" />
      <div class="flex-1 space-y-2">
        <input :id="props.id" v-model="url" type="text" class="form-input" placeholder="https://… or upload a file" />
        <label class="btn btn-secondary cursor-pointer px-3 py-1.5" :class="{ 'opacity-50': isUploading }">
          {{ isUploading ? 'Uploading…' : 'Upload file' }}
          <input type="file" accept="image/*" class="sr-only" :disabled="isUploading" @change="upload" />
        </label>
      </div>
    </div>
  </div>
</template>
