<script setup lang="ts">
import { computed, ref } from 'vue'
import { useForm } from 'vee-validate'
import * as yup from 'yup'
import { isAxiosError } from 'axios'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toast-notification'
import { useAppStore } from '@/stores/app'
import { useLogout } from '@/composables/use-logout'
import TwoFactorCodeInput from '@/components/TwoFactorCodeInput.vue'

const appStore = useAppStore()
const route = useRoute()
const router = useRouter()
const toast = useToast()
const { logout } = useLogout()

// The API sends a code automatically on login, so we only send one here on request.
const method = computed(() => appStore.user?.two_factor_method_enabled ?? 'email')
const destination = computed(() => (method.value === 'sms' ? 'your mobile' : 'your email address'))
const resending = ref(false)

const { handleSubmit, setErrors, isSubmitting } = useForm({
  validationSchema: yup.object({
    code: yup.string().trim().required('Enter the code we sent you.'),
  }),
  initialValues: { code: '' },
})

const onSubmit = handleSubmit(async ({ code }) => {
  try {
    await appStore.twoFactorVerify({ code, login: true })
    router.replace((route.query.redirect as string) || { name: 'dashboard' })
  } catch (error) {
    if (isAxiosError(error) && error.response?.status === 429) {
      setErrors({ code: 'Too many attempts. Please wait a minute and try again.' })
      return
    }
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)
      return
    }
    throw error
  }
})

async function resend() {
  resending.value = true

  try {
    await appStore.sendTwoFactorCode(method.value)
    toast.success(`A new code has been sent to ${destination.value}.`)
  } catch (error) {
    if (isAxiosError(error) && error.response?.status === 429) {
      toast.error('Please wait a minute before requesting another code.')
      return
    }
    throw error
  } finally {
    resending.value = false
  }
}
</script>

<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto">
        <div class="card z-index-0 fadeIn3 fadeInBottom">
          <div class="card-body">
            <img class="logo mt-4" src="@/assets/img/logo/logo.png" alt="Invoice" />
            <h4 class="text-primary font-weight-bolder mt-3 mb-4">2-Step Verification</h4>

            <form class="text-start" novalidate @submit="onSubmit">
              <p class="text-muted text-sm">Enter the 6-digit code we sent to {{ destination }}.</p>

              <TwoFactorCodeInput />

              <p class="text-muted text-sm">
                Code hasn't arrived?<br />
                <button
                  type="button"
                  class="btn btn-link text-primary text-sm p-0 text-decoration-underline"
                  :disabled="resending"
                  @click="resend"
                >
                  Resend code
                </button>
              </p>

              <div class="row mt-4 mb-4">
                <div class="col-auto d-flex align-items-center">
                  <button type="button" class="btn btn-link text-sm text-muted p-0 mb-0" @click="logout">
                    Sign out
                  </button>
                </div>
                <div class="col text-end">
                  <button type="submit" class="btn btn-primary mb-0" :disabled="isSubmitting">
                    Verify code
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
