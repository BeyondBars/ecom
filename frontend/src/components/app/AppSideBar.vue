<template>
  <!-- Backdrop (mobile only) -->
  <div v-if="open" class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden" @click="emit('close')" />

  <aside
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col overflow-y-auto bg-slate-900 print:hidden text-slate-300 transition-transform lg:static lg:translate-x-0"
    :class="open ? 'translate-x-0' : '-translate-x-full'"
  >
    <div class="flex items-center justify-between px-5 py-5">
      <img class="h-9 w-auto" src="@/assets/img/logo/white-logo.png" alt="Ladies First Couture" />
      <button type="button" class="rounded-md p-2 hover:bg-slate-800 lg:hidden" aria-label="Close menu" @click="emit('close')">
        <FontAwesomeIcon icon="xmark" />
      </button>
    </div>

    <nav class="flex flex-1 flex-col gap-1 px-3">
      <SidebarItem :to="{ name: 'dashboard' }" icon="house">Dashboard</SidebarItem>

      <template v-if="can('manage orders') || can('manage coupons') || can('manage reviews')">
        <p class="mt-4 px-3 text-xs font-semibold tracking-wide text-slate-500 uppercase">Sales</p>
        <SidebarItem v-if="can('manage orders')" :to="{ name: 'orders' }" icon="bag-shopping">Orders</SidebarItem>
        <SidebarItem v-if="can('manage coupons')" :to="{ name: 'coupons' }" icon="tag">Coupons</SidebarItem>
        <SidebarItem v-if="can('manage reviews')" :to="{ name: 'reviews' }" icon="star">Reviews</SidebarItem>
      </template>

      <template v-if="can('manage catalog')">
        <p class="mt-4 px-3 text-xs font-semibold tracking-wide text-slate-500 uppercase">Catalogue</p>
        <SidebarItem :to="{ name: 'products' }" icon="shirt">Products</SidebarItem>
        <SidebarItem :to="{ name: 'categories' }" icon="layer-group">Categories</SidebarItem>
        <SidebarItem :to="{ name: 'brands' }" icon="copyright">Brands</SidebarItem>
      </template>

      <template v-if="can('manage content')">
        <p class="mt-4 px-3 text-xs font-semibold tracking-wide text-slate-500 uppercase">Content</p>
        <SidebarItem :to="{ name: 'banners' }" icon="images">Banners</SidebarItem>
        <SidebarItem :to="{ name: 'blog' }" icon="newspaper">Blog</SidebarItem>
        <SidebarItem :to="{ name: 'inbox' }" icon="inbox">Inbox</SidebarItem>
      </template>

      <p class="mt-4 px-3 text-xs font-semibold tracking-wide text-slate-500 uppercase">Admin</p>
      <SidebarItem v-if="can('read users')" :to="{ name: 'users' }" icon="users">Users & Customers</SidebarItem>
      <SidebarItem v-if="can('manage email templates')" :to="{ name: 'email-templates' }" icon="envelope">Email Templates</SidebarItem>
      <SidebarItem :to="{ name: 'settings' }" icon="gear">Settings</SidebarItem>
      <SidebarItem :to="{ name: 'account' }" icon="circle-user">My Account</SidebarItem>

      <div class="mt-auto mb-4 pt-4">
        <SidebarItem icon="right-from-bracket" @click="logoutUser">Logout</SidebarItem>
      </div>
    </nav>
  </aside>
</template>

<script lang="ts" setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { useToast } from 'vue-toast-notification'
import { isAxiosError } from 'axios'
import SidebarItem from './AppSideBarItem.vue'
import { useLogout } from '@/composables/use-logout'
import { useLoggedInUser } from '@/composables/use-logged-in-user'

defineProps<{ open: boolean }>()
const emit = defineEmits<{ close: [] }>()

const toast = useToast()
const { logout } = useLogout()
const { can } = useLoggedInUser()

const logoutUser = async () => {
  try {
    await logout()
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.message) {
      toast.error(error.response.data.message)
      return
    }

    throw error
  }
}
</script>
