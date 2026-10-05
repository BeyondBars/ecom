<script setup lang="ts">
import VerificationResend from './AccountVerificationResend.vue'

withDefaults(defineProps<{ error?: string; showResendButton?: boolean }>(), {
  error: undefined,
  showResendButton: false,
})
</script>

<template>
  <div class="card card-authentication">
    <div class="card-body">
      <img class="logo" src="@/assets/img/logo/logo.png" alt="Invoice" />
      <h1 class="mb-4">Verification Failed</h1>
      <p class="mb-4">
        <template v-if="error">{{ error }}.</template>
        <template v-else>We were unable to verify your email. The verification link has probably expired.</template>
      </p>
      <div v-if="showResendButton" class="text-end">
        <VerificationResend />
      </div>
      <RouterLink v-else :to="{ name: 'login' }" class="btn btn-primary">Back to sign in</RouterLink>
    </div>
  </div>
</template>
