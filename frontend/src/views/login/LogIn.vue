<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto">
        <div class="card z-index-0 fadeIn3 fadeInBottom">
          <div class="card-body">
            <img class="logo mt-4" src="@/assets/img/logo/logo.png" alt="Invoice" />
            <h4 class="text-primary font-weight-bolder mb-4">Sign In</h4>

            <form role="form" class="text-start" novalidate @submit="onSubmit">
              <HTBFormField name="email" label="Email Address" type="email" />
              <HTBFormField name="password" label="Password" type="password" />

              <div class="row mt-4 mb-4">
                <div class="col-auto text-end d-flex align-items-center">
                  <p class="text-sm">
                    <RouterLink :to="{ name: 'forgot-password' }" class="text-primary text-gradient font-weight-bold">
                      Forgot Password?
                    </RouterLink>
                  </p>
                </div>
                <div class="col text-end">
                  <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                    <span class="label">Next</span>
                  </button>
                </div>
              </div>

              <div class="text-center">
                <p class="text-sm text-muted mb-2">or sign in with</p>
                <a :href="`${apiUrl}/auth/google`" class="btn btn-outline-secondary me-2">
                  <FontAwesomeIcon :icon="['fab', 'google']" /> Google
                </a>
                <a :href="`${apiUrl}/auth/facebook`" class="btn btn-outline-secondary">
                  <FontAwesomeIcon :icon="['fab', 'facebook-f']" /> Facebook
                </a>
              </div>

              <div class="row mt-4 mb-4">
                <div class="col-auto mx-auto d-flex align-items-center mt-3 text-center">
                  <p class="text-sm text-center">
                    Don't have an account?
                    <RouterLink :to="{ name: 'register' }" class="text-primary text-gradient font-weight-bold">
                      Sign up
                    </RouterLink>
                  </p>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { isAxiosError } from 'axios'
import { useForm } from 'vee-validate'
import * as yup from 'yup'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toast-notification'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { useAppStore } from '@/stores/app'
import HTBFormField from '@/components/HTBFormField.vue'

const toast = useToast()
const appStore = useAppStore()
const route = useRoute()
const router = useRouter()

// Errors passed back from the social login callback (?error=...).
const SOCIAL_ERRORS: Record<string, string> = {
  social_failed: 'Social sign in failed. Please try again.',
  social_no_email: 'Your social account did not share an email address.',
  social_account_exists: 'An account with that email already exists. Sign in with your password instead.',
  account_disabled: 'This user account has been disabled.',
}

if (typeof route.query.error === 'string' && SOCIAL_ERRORS[route.query.error]) {
  toast.error(SOCIAL_ERRORS[route.query.error])
}

const apiUrl = import.meta.env.VITE_API_URL

const { handleSubmit, setErrors, isSubmitting } = useForm({
  validationSchema: yup.object({
    email: yup.string().email('The email must be valid.').required('The email field is required.'),
    password: yup.string().required('The password is required.'),
  }),
})

const onSubmit = handleSubmit(async (payload) => {
  try {
    await appStore.login(payload)

    // The router middleware sends the user to the 2FA challenge / setup screens when needed.
    router.replace((route.query.redirect as string) || { name: 'dashboard' })
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)
      return
    }
    throw error
  }
})
</script>

<style>
@media (min-width: 1024px) {
  .about {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
</style>
