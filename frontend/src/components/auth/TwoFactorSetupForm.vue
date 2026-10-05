<script setup lang="ts">
import { computed, ref } from 'vue'
import { isAxiosError } from 'axios'
import { useToast } from 'vue-toast-notification'
import { useAppStore } from '@/stores/app'
import { useLogout } from '@/composables/use-logout'
import { useTwoFactorSetup } from '@/composables/use-two-factor-setup'
import TwoFactorCodeInput from '@/components/TwoFactorCodeInput.vue'
import type { TwoFactorMethod } from '@/types/user'

/**
 * Shared body of the "set up email/SMS 2-step verification" screens.
 */
const props = defineProps<{ method: TwoFactorMethod }>()

const toast = useToast()
const appStore = useAppStore()
const { logout } = useLogout()
const { verify, changeMethod, isSubmitting } = useTwoFactorSetup(props.method)

const label = computed(() => (props.method === 'sms' ? 'SMS' : 'Email'))
const destination = computed(() =>
  props.method === 'sms' ? `your mobile (${appStore.user?.mobile ?? ''})` : `your email address (${appStore.user?.email ?? ''})`
)
const otherMethod = computed(() => (props.method === 'sms' ? 'email' : 'SMS'))
const sending = ref(false)

async function sendCode(showToast = true) {
  sending.value = true

  try {
    await appStore.sendTwoFactorCode(props.method)
    if (showToast) {
      toast.success(`A verification code has been sent to ${destination.value}. It expires in 10 minutes.`)
    }
  } catch (error) {
    if (isAxiosError(error) && error.response?.status === 429) {
      toast.error('Please wait a minute before requesting another code.')
      return
    }
    if (isAxiosError(error) && error.response?.data?.message) {
      toast.error(error.response.data.message)
      return
    }
    throw error
  } finally {
    sending.value = false
  }
}

// Send the first code as soon as the screen opens.
await sendCode(false)
</script>

<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto">
        <div class="card card-authentication">
          <form class="card-body" novalidate @submit="verify">
            <img class="logo" src="@/assets/img/logo/logo.png" alt="Invoice" />

            <h4 class="text-primary font-weight-bolder mb-4">Set up {{ label }} 2-Step Verification</h4>
            <p class="text-sm">We've sent a code to {{ destination }}.</p>
            <p class="text-sm">Enter it below to finish setting up.</p>

            <TwoFactorCodeInput />

            <p class="text-muted text-sm">
              Code hasn't arrived?<br />
              <button
                type="button"
                class="btn btn-link text-primary text-sm p-0 text-decoration-underline"
                :disabled="sending"
                @click="sendCode()"
              >
                Resend code
              </button>
              &middot;
              <button type="button" class="btn btn-link text-primary text-sm p-0" @click="changeMethod">
                Use {{ otherMethod }} instead
              </button>
            </p>

            <div class="row mt-4">
              <div class="col-auto d-flex align-items-center">
                <button type="button" class="btn btn-link text-sm text-muted p-0 mb-0" @click="logout">Sign out</button>
              </div>
              <div class="col text-end">
                <button type="submit" class="btn btn-primary mb-0" :disabled="isSubmitting">Verify</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
