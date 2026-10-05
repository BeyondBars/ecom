<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card my-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
              <h6 class="text-white text-capitalize ps-3">Blogs</h6>
              <div class="pe-3">
                <router-link :to="{ name: 'create-blog' }" class="btn btn-xl btn-light">
                  <i class="material-icons text-xl">add</i>
                </router-link>
              </div>
            </div>
          </div>
          <div class="card-body px-0">

            <!-- Blog List -->
            <div class="table-responsive p-0 scrolling-component" id="scrolling-component-1">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="blog in blogsStore.blogs" :key="blog.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ blog.title }}</h6>
                          <p class="text-xs text-secondary mb-0">{{ blog.slug }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="align-middle text-sm">
                      <span :class="getStatusBadgeClass(blog.status)">{{ blog.status }}</span>
                    </td>
                    <td class="align-middle">
                      <span class="text-secondary text-xs font-weight-bold">{{ formatDate(blog.created_at) }}</span>
                    </td>
                    <td class="align-middle">
                      <router-link :to="{ name: 'edit-blog', params: { id: blog.id }}" class="text-secondary font-weight-bold text-xs me-2">
                        Edit
                      </router-link>
                      <a href="javascript:;" class="text-danger font-weight-bold text-xs" @click="handleDelete(blog.id)">
                        Delete
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-if="blogsStore.isLoading" class="text-center p-3">Loading...</div>
              <div v-if="!blogsStore.isLoading && blogsStore.blogs.length === 0" class="text-center p-3">
                No blogs found
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import { useBlogStore } from '@/stores/blogs'
import { useInfiniteScroll } from '@/composables/useInfiniteScroll'
import { formatDate } from '@/utils/date'
import { useToast } from 'vue-toast-notification'

const blogsStore = useBlogStore()
const toast = useToast()
const searchQuery = ref('')
const statusFilter = ref('')
let searchTimeout: NodeJS.Timeout

// Load blogs
const loadBlogs = async () => {
  await blogsStore.fetchBlogs({
    page: blogsStore.currentPage,
    search: searchQuery.value,
    status: statusFilter.value
  })
}

// Handle search with debounce
const handleSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    blogsStore.currentPage = 1
    loadBlogs()
  }, 300)
}

// Handle status filter
const handleFilter = () => {
  blogsStore.currentPage = 1
  loadBlogs()
}

// Handle delete
const handleDelete = async (id: number) => {
  if (confirm('Are you sure you want to delete this blog?')) {
    try {
      await blogsStore.deleteBlog(id)
      toast.success('Blog deleted successfully')
    } catch (error) {
      toast.error('Failed to delete blog')
    }
  }
}

// Get status badge class
const getStatusBadgeClass = (status: string) => {
  const classes = {
    enabled: 'badge badge-sm bg-gradient-success',
    pending: 'badge badge-sm bg-gradient-warning',
    disabled: 'badge badge-sm bg-gradient-danger'
  }
  return classes[status as keyof typeof classes] || 'badge badge-sm bg-gradient-secondary'
}

// Hook into infinite scroll
const { isLoading } = useInfiniteScroll({
  loadFn: loadBlogs,
  hasMore: blogsStore.hasMore,
  scrollTargetSelector: '#page-content-wrapper',
  contentSelector: '#scrolling-component-1',
  offset: 100
})

onMounted(() => {
  loadBlogs()
})
</script>
