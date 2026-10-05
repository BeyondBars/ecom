<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card my-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
              <h6 class="text-white text-capitalize ps-3">
                {{ isEdit ? 'Edit Blog' : 'Create Blog' }}
              </h6>
            </div>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleSubmit">
              <div class="row">
                <div class="col-md-8">
                  <div class="input-group input-group-static mb-4">
                    <label for="title">Title</label>
                    <input
                      type="text"
                      class="form-control"
                      id="title"
                      v-model="form.title"
                      :class="{ 'is-invalid': errors.title }"
                      :data-value="form.title"
                    >
                    <div class="invalid-feedback" v-if="errors.title">{{ errors.title[0] }}</div>
                  </div>

                  <div class="input-group input-group-static mb-4">
                    <label for="slug">Slug</label>
                    <input
                      type="text"
                      class="form-control"
                      id="slug"
                      v-model="form.slug"
                      :class="{ 'is-invalid': errors.slug }"
                      :data-value="form.slug"
                    >
                    <div class="invalid-feedback" v-if="errors.slug">{{ errors.slug[0] }}</div>
                  </div>

                  <div class="input-group input-group-static mb-4">
                    <label for="content">Content</label>
                    <TiptapEditor v-model="form.content" :max-limit="280" />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="input-group input-group-static mb-4">
                    <label for="status">Status</label>
                    <select
                      class="form-control"
                      id="status"
                      v-model="form.status"
                      :class="{ 'is-invalid': errors.status }"
                      :data-value="form.status"
                    >
                      <option value="enabled">Enabled</option>
                      <option value="pending">Pending</option>
                      <option value="disabled">Disabled</option>
                    </select>
                    <div class="invalid-feedback" v-if="errors.status">{{ errors.status[0] }}</div>
                  </div>

                  <div class="input-group input-group-static mb-4">
                    <label for="meta_description">Meta Description</label>
                    <textarea
                      class="form-control"
                      id="meta_description"
                      rows="3"
                      v-model="form.meta_description"
                      :class="{ 'is-invalid': errors.meta_description }"
                      :data-value="form.meta_description"
                    ></textarea>
                    <div class="invalid-feedback" v-if="errors.meta_description">{{ errors.meta_description[0] }}</div>
                  </div>

                  <div class="input-group input-group-static mb-4">
                    <label for="meta_keywords">Meta Keywords</label>
                    <input
                      type="text"
                      class="form-control"
                      id="meta_keywords"
                      v-model="form.meta_keywords"
                      :class="{ 'is-invalid': errors.meta_keywords }"
                      :data-value="form.meta_keywords"
                    >
                    <div class="invalid-feedback" v-if="errors.meta_keywords">{{ errors.meta_keywords[0] }}</div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-12">
                  <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                    {{ isSubmitting ? 'Saving...' : (isEdit ? 'Update Blog' : 'Create Blog') }}
                  </button>
                  <router-link :to="{ name: 'blogs' }" class="btn btn-secondary ms-2">
                    Cancel
                  </router-link>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import TiptapEditor from '@/components/forms/tiptap/TiptapEditor.vue'
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useBlogStore } from '@/stores/blogs'
import { useToast } from 'vue-toast-notification'
import type { Blog } from '@/types/blog'

const route = useRoute()
const router = useRouter()
const blogsStore = useBlogStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)
const isSubmitting = ref(false)
const errors = ref<Record<string, string[]>>({})

const form = ref<Partial<Blog>>({
  title: '',
  slug: '',
  content: '',
  meta_description: '',
  meta_keywords: '',
  status: 'pending'
})

// Load blog data if editing
onMounted(async () => {
  if (isEdit.value) {
    try {
      console.log('Fetching blog with ID:', route.params.id)
      const blogData = await blogsStore.fetchBlog(Number(route.params.id))
      console.log('Received blog data:', blogData)
      if (blogData) {
        // Create a new object to ensure reactivity
        const newFormData = {
          title: blogData.title || '',
          slug: blogData.slug || '',
          content: blogData.content || '',
          meta_description: blogData.meta_description || '',
          meta_keywords: blogData.meta_keywords || '',
          status: blogData.status || 'pending'
        }
        console.log('New form data to be set:', newFormData)
        form.value = newFormData
        console.log('Form data after setting:', form.value)
        
        // Force a reactivity update
        nextTick(() => {
          console.log('Form data after nextTick:', form.value)
        })
      }
    } catch (error) {
      console.error('Error loading blog:', error)
      toast.error('Failed to load blog')
      router.push({ name: 'blogs' })
    }
  }
})

// Handle form submission
const handleSubmit = async () => {
  try {
    isSubmitting.value = true
    errors.value = {}

    if (isEdit.value) {
      await blogsStore.updateBlog(Number(route.params.id), form.value)
      toast.success('Blog updated successfully')
    } else {
      await blogsStore.createBlog(form.value)
      toast.success('Blog created successfully')
    }

    router.push({ name: 'blogs' })
  } catch (error: any) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      toast.error(isEdit.value ? 'Failed to update blog' : 'Failed to create blog')
    }
  } finally {
    isSubmitting.value = false
  }
}
</script> 