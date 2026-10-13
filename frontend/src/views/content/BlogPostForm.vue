<template>
  <PageHeader :title="isNew ? 'Write a post' : 'Edit post'">
    <RouterLink :to="{ name: 'blog' }" class="link-muted">← Back to blog</RouterLink>
  </PageHeader>

  <p v-if="!isLoaded" class="text-sm text-slate-500">Loading…</p>
  <form v-else class="grid grid-cols-1 gap-6 xl:grid-cols-3" @submit.prevent="save">
    <section class="card space-y-4 p-5 xl:col-span-2">
      <div>
        <label class="form-label" for="post-title">Title *</label>
        <input id="post-title" v-model="form.title" class="form-input" required />
        <span v-if="errors.title" class="form-error">{{ errors.title }}</span>
      </div>
      <div>
        <label class="form-label" for="post-excerpt">Summary</label>
        <textarea id="post-excerpt" v-model="form.excerpt" rows="2" class="form-input" placeholder="Shown on the blog list" />
      </div>
      <div>
        <label class="form-label" for="post-content">Content *</label>
        <textarea id="post-content" v-model="form.content" rows="16" class="form-input" required placeholder="Separate paragraphs with a blank line." />
        <span v-if="errors.content" class="form-error">{{ errors.content }}</span>
      </div>
    </section>

    <div class="space-y-6">
      <section class="card space-y-4 p-5">
        <div>
          <label class="form-label" for="post-status">Status</label>
          <select id="post-status" v-model="form.status" class="form-input">
            <option value="draft">Draft</option>
            <option value="published">Published</option>
          </select>
        </div>
        <div>
          <label class="form-label" for="post-date">Publish date</label>
          <input id="post-date" v-model="form.published_at" type="date" class="form-input" />
          <p class="mt-1 text-xs text-slate-500">Leave empty to publish now. A future date schedules the post.</p>
        </div>
        <div>
          <label class="form-label" for="post-slug">URL slug</label>
          <input id="post-slug" v-model="form.slug" class="form-input" placeholder="Generated from the title" />
          <span v-if="errors.slug" class="form-error">{{ errors.slug }}</span>
        </div>
        <ImageUploadField id="post-cover" v-model="form.cover_image_url" label="Cover image" />
      </section>
      <div class="flex gap-3">
        <button type="submit" class="btn btn-primary flex-1" :disabled="isSaving">{{ isSaving ? 'Saving…' : 'Save post' }}</button>
        <button v-if="!isNew" type="button" class="btn btn-danger" @click="remove">Delete</button>
      </div>
    </div>
  </form>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toast-notification'
import PageHeader from '@/components/app/PageHeader.vue'
import ImageUploadField from '@/components/shop/ImageUploadField.vue'
import confirm from '@/utils/alert'
import { http } from '@/utils/http'
import { validationErrors } from '@/utils/money'
import type { BlogPost } from '@/types/shop'

const props = defineProps<{ id?: string }>()
const isNew = computed(() => !props.id)
const router = useRouter()
const toast = useToast()
const errors = ref<Record<string, string>>({})
const isSaving = ref(false)
// Edit forms stay hidden until the record loads, so typing isn't overwritten by the load
const isLoaded = ref(!props.id)

const form = reactive({
  title: '',
  slug: '' as string | null,
  excerpt: '' as string | null,
  content: '',
  cover_image_url: null as string | null,
  status: 'draft' as BlogPost['status'],
  published_at: '' as string | null,
})

const save = async () => {
  isSaving.value = true
  errors.value = {}
  const payload = { ...form, slug: form.slug || null, published_at: form.published_at || null }
  try {
    const { data } = isNew.value
      ? await http.post<{ data: BlogPost }>('admin/blog-posts', payload)
      : await http.put<{ data: BlogPost }>(`admin/blog-posts/${props.id}`, payload)
    toast.success('Post saved.')
    if (isNew.value) router.replace({ name: 'blog.edit', params: { id: data.data.id } })
  } catch (error) {
    errors.value = validationErrors(error)
  } finally {
    isSaving.value = false
  }
}

const remove = async () => {
  if (!(await confirm({ title: 'Delete this post?', text: 'Its comments are deleted too.' }))) return
  await http.delete(`admin/blog-posts/${props.id}`)
  router.push({ name: 'blog' })
}

onMounted(async () => {
  if (!props.id) return
  const { data } = await http.get<{ data: BlogPost }>(`admin/blog-posts/${props.id}`)
  const post = data.data
  Object.assign(form, { ...post, published_at: post.published_at?.slice(0, 10) ?? '' })
  isLoaded.value = true
})
</script>
