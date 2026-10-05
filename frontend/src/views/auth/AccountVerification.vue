<script setup lang="ts">
import { useRoute } from 'vue-router'
import { useIsLoggedIn } from '@/composables/use-is-logged-in'
import { useEmailVerification } from '@/composables/use-email-verification'
import VerificationSuccess from '@/components/auth/AccountVerificationSuccess.vue'
import VerificationFailed from '@/components/auth/AccountVerificationFailed.vue'
import VerificationPending from '@/components/auth/AccountVerificationPending.vue'

/**
 * /verification/:signature? - with a signature it verifies the emailed link, without one it tells
 * a logged in user to check their inbox (they're sent here by the "verified" middleware).
 */
const props = defineProps<{ signature?: string }>()

const route = useRoute()
const { isLoggedIn } = useIsLoggedIn()
const { verify, verifying, success, failed, errorMessage } = useEmailVerification()

if (props.signature) {
  await verify({
    signature: props.signature,
    expires: String(route.query.expires ?? ''),
    identifier: String(route.query.identifier ?? ''),
  })
}
</script>

<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto">
        <div v-if="verifying" class="card card-authentication">
          <div class="card-body">
            <img class="logo" src="@/assets/img/logo/logo.png" alt="Invoice" />
            <h1>Verifying Email</h1>
            <p class="text-muted mb-0">Please wait while we verify your email.</p>
          </div>
        </div>
        <VerificationSuccess v-else-if="success" :logged-in="isLoggedIn" />
        <VerificationFailed v-else-if="failed" :error="errorMessage" :show-resend-button="isLoggedIn" />
        <VerificationPending v-else-if="isLoggedIn" />
        <VerificationFailed v-else error="This verification link is incomplete" />
      </div>
    </div>
  </div>
</template>
