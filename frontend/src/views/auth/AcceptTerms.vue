<script setup lang="ts">
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'
import { useRoute, useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { useLogout } from '@/composables/use-logout'

const termsAndConditionsUrl = import.meta.env.VITE_TERMS_URL || '#'

const appStore = useAppStore()
const route = useRoute()
const router = useRouter()
const { logout } = useLogout()

const { handleSubmit, isSubmitting } = useForm({
  validationSchema: yup.object({
    agree_terms: yup.boolean().oneOf([true], 'You must accept the terms and conditions.'),
  }),
  initialValues: { agree_terms: false },
})

const { value: agreeTerms, errorMessage } = useField<boolean>('agree_terms')

const onSubmit = handleSubmit(async (payload) => {
  await appStore.acceptTerms(payload)
  router.replace((route.query.redirect as string) || { name: 'dashboard' })
})
</script>

<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto">
        <div class="card card-authentication">
          <form class="card-body" novalidate @submit="onSubmit">
            <img class="logo" src="@/assets/img/logo/logo.png" alt="Invoice" />
            <h4 class="text-primary font-weight-bolder mb-4">Terms and Conditions</h4>
            <p class="text-sm">Please review and accept our terms to continue.</p>

            <div class="form-check ps-0 mb-2">
              <input id="agree_terms" v-model="agreeTerms" class="form-check-input" type="checkbox" />
              <label class="form-check-label" for="agree_terms">
                I agree to the
                <a :href="termsAndConditionsUrl" target="_blank" rel="noopener" class="text-primary">terms and conditions</a>
              </label>
            </div>
            <span v-if="errorMessage" class="text-danger text-xs">{{ errorMessage }}</span>

            <div class="row mt-4">
              <div class="col-auto d-flex align-items-center">
                <button type="button" class="btn btn-link text-sm text-muted p-0 mb-0" @click="logout">Sign out</button>
              </div>
              <div class="col text-end">
                <button type="submit" class="btn btn-primary mb-0" :disabled="isSubmitting">Continue</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
