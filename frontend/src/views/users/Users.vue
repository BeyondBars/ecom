<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card my-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
              <h6 class="text-white text-capitalize ps-3">Users</h6>
            </div>
          </div>
          <div class="card-body px-0">
            <div class="table-responsive p-0 scrolling-component" id="scrolling-component">
              <table class="table align-items-center mb-0" @load="loadMore">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th>Last Login</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in usersStore.users" :key="user.id" @click="go(user.id)" class="cursor-pointer">
                    <td>
                      <p class="mb-0 text-xs">{{ user.name }}</p>
                    </td>
                    <td>
                      <p class="text-xs text-secondary mb-0">{{ user.email }}</p>
                    </td>
                    <td>
                      <p class="text-xs mb-0">{{ user.mobile }}</p>
                    </td>
                    <td>
                      <span class="badge badge-sm bg-gradient-success" v-if="user.status === 'enabled'">Active</span>
                      <span class="badge badge-sm bg-gradient-warning" v-if="user.status === 'pending'">Pending</span>
                      <span class="badge badge-sm bg-gradient-danger" v-if="user.status === 'disabled'">Archived</span>
                    </td>
                    <td class="align-middle">
                      <span class="text-secondary text-xs">{{ user.last_login_at || '-'}}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { onMounted, onUnmounted, nextTick, ref } from 'vue'
import { useUserStore } from '@/stores/users'
import { useRouter } from 'vue-router'
const router = useRouter()
const isLoading = ref(false)
const usersStore = useUserStore()
const { fetchUsers } = usersStore
let scrollContainer: HTMLElement | null = null
let contentEl: HTMLElement | null = null

const go = (id: number) => {
  router.push({ name: 'users.details', params: { id } }) // adjust route name & param as needed
}
const loadUsers = async () => {
  if (isLoading.value || !usersStore.hasMore) return
  isLoading.value = true
  await usersStore.fetchUsers({ currentPage: usersStore.currentPage++ })
  isLoading.value = false
}

// Check if content fills the page — if not, load more until it does
const ensureScrollFills = () => {
  nextTick(() => {
    if (!scrollContainer || !contentEl || isLoading.value || !usersStore.hasMore) return

    const contentIsSmallerThanPage =
      contentEl.scrollHeight <= scrollContainer.clientHeight

    if (contentIsSmallerThanPage) {
      loadUsers().then(() => {
        // Retry fill until scrollable
        setTimeout(ensureScrollFills, 100)
      })
    }
  })
}

// Trigger infinite scroll when user scrolls near bottom
const handleScroll = () => {
  if (!scrollContainer || isLoading.value || !usersStore.hasMore) return

  const nearBottom =
    scrollContainer.scrollTop + scrollContainer.clientHeight >=
    scrollContainer.scrollHeight - 100

  if (nearBottom) {
    loadUsers()
  }
}

onMounted(() => {
  scrollContainer = document.querySelector('#page-content-wrapper')
  contentEl = document.querySelector('#scrolling-component')

  if (scrollContainer) {
    scrollContainer.addEventListener('scroll', handleScroll)
    loadUsers().then(() => ensureScrollFills())
  }
})

onUnmounted(() => {
  scrollContainer?.removeEventListener('scroll', handleScroll)
})
</script>