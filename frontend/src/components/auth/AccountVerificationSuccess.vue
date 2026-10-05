<script setup lang="ts">
defineProps<{ loggedIn: boolean }>()
</script>

<template>
  <div class="card card-authentication">
    <div class="card-body">
      <img class="logo" src="@/assets/img/logo/logo.png" alt="Invoice" />
      <h1 class="mb-4">Email Verified</h1>
      <p class="text-muted">Thank you for verifying your email address. This helps us keep your account secure.</p>
      <RouterLink :to="{ name: loggedIn ? 'dashboard' : 'login' }" class="btn btn-primary">
        {{ loggedIn ? 'Continue' : 'Sign in' }}
      </RouterLink>
    </div>
  </div>
</template>
