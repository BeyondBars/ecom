<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { useLogout } from '@/composables/use-logout'
import VerificationResend from './AccountVerificationResend.vue'

const appStore = useAppStore()
const { logout } = useLogout()
</script>

<template>
  <div class="card card-authentication">
    <div class="card-body">
      <img class="logo" src="@/assets/img/logo/logo.png" alt="Invoice" />
      <h1 class="mb-4">Verify Your Email</h1>
      <p>
        We've sent an email to <strong>{{ appStore.user?.email }}</strong> to verify your account. Please check your
        inbox and verify your address before continuing.
      </p>
      <p class="text-muted">
        If you can't find it, check your junk folder or request a new email with the button below.
      </p>
      <div class="row mt-4">
        <div class="col-auto d-flex align-items-center">
          <button type="button" class="btn btn-link text-sm text-muted p-0 mb-0" @click="logout">Sign out</button>
        </div>
        <div class="col text-end">
          <VerificationResend />
        </div>
      </div>
    </div>
  </div>
</template>
