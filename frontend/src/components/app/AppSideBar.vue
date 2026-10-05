<template>
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 fixed-start bg-gradient-dark bg-white" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand text-center">
      <img class="navbar-brand-img" src="@/assets/img/logo/white-logo.png" alt="She Codes" />
    </a>
  </div>
  <hr class="horizontal light mt-0 mb-2">
  <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <SidebarItem
        :to="{ name: 'dashboard' }"
        icon="home"
      >
        Dashboard
      </SidebarItem>
      <SidebarItem
        :to="{ name: 'users' }"
        icon="manage_accounts"
      >
        User Management
      </SidebarItem>
      <SidebarItem
        :to="{ name: 'blogs' }"
        icon="rss_feed"
      >
        Blog Management
      </SidebarItem>
      <SidebarItem
        :to="{ name: 'settings' }"
        icon="settings"
      >
        Settings
      </SidebarItem>
      <SidebarItem
        :to="{ name: 'account' }"
        icon="account_circle"
      >
        My Account
      </SidebarItem>
      <SidebarItem
        icon="logout"
        @click.prevent="logoutUser"
      >
        Logout
      </SidebarItem>
    </ul>
  </div>
</aside>
</template>

<script lang="ts" setup>
import SidebarItem from './AppSideBarItem.vue'
import { watch, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useLogout } from '@/composables/use-logout'
import { useToast } from 'vue-toast-notification'
import { isAxiosError } from 'axios'
const toast = useToast()
const router = useRouter()

const route = useRoute()
const wrapper = ref<Element | null>(null)
const { logout } = useLogout()

onMounted(() => {
  wrapper.value = document.querySelector('#wrapper')
})
const logoutUser = (async () => {
  try {
    await logout()
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)

      if (error.response.status === 429) return // No need to throw for rate limit errors
    }

    throw error
  }
})

watch(route, () => {
  if (wrapper.value === null) {
    return
  }

  if (wrapper.value.classList.contains('toggled')) {
    wrapper.value.classList.remove('toggled')
  }
})
</script>
