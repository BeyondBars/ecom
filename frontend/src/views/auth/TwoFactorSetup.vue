<script setup lang="ts">
import { isAxiosError } from 'axios'
import * as yup from 'yup'
import { useForm, useField } from 'vee-validate'
import { useRouter } from 'vue-router'
import { useLogout } from '@/composables/use-logout'
import { useAppStore } from '@/stores/app'
import type { TwoFactorMethod } from '@/types/user'

const router = useRouter()
const { logout } = useLogout()
const appStore = useAppStore()

const securityOptions: { value: TwoFactorMethod; label: string }[] = [
  { value: 'email', label: 'Email' },
  { value: 'sms', label: 'SMS' },
]

const { handleSubmit, setErrors, isSubmitting, errors } = useForm({
  validationSchema: yup.object({
    two_factor_required: yup.boolean().required(),
    two_factor_method_preferred: yup
      .string()
      .nullable()
      .when('two_factor_required', {
        is: true,
        then: (schema) => schema.required('Choose how you want to receive codes.'),
      }),
  }),
  initialValues: {
    two_factor_required: appStore.user?.two_factor_required ?? true,
    two_factor_method_preferred: appStore.user?.two_factor_method_preferred ?? 'email',
  },
})

const { value: twoFactorRequired } = useField<boolean>('two_factor_required')
const { value: twoFactorMethodPreferred } = useField<TwoFactorMethod | null>('two_factor_method_preferred')

const onSubmit = handleSubmit(async (values) => {
  const payload = {
    two_factor_required: values.two_factor_required,
    two_factor_method_preferred: values.two_factor_required ? values.two_factor_method_preferred : null,
  }

  try {
    const user = await appStore.updateTwoFactor(payload)

    // Opted in: verify a code for the chosen method. Opted out: carry on to the app.
    if (user.two_factor_required && user.two_factor_method_preferred) {
      router.replace({ name: `two-factor.setup.${user.two_factor_method_preferred}`, query: { change: '1' } })
    } else {
      router.replace({ name: 'dashboard' })
    }
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)
      return
    }
    throw error
  }
})
</script>

<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto">
        <div class="card card-authentication">
          <form class="card-body" novalidate @submit="onSubmit">
            <img class="logo" src="@/assets/img/logo/logo.png" alt="Invoice" />

            <h4 class="text-primary font-weight-bolder mb-4">Set up 2-Step Verification</h4>
            <p class="text-sm">Using a second authentication method helps keep your account secure.</p>

            <div class="form-check form-switch ps-0 mb-3">
              <input
                id="two_factor_required"
                v-model="twoFactorRequired"
                class="form-check-input ms-auto"
                type="checkbox"
              />
              <label class="form-check-label text-body ms-3 mb-0 fw-bold" for="two_factor_required">
                Use 2-step verification when logging in
              </label>
            </div>

            <fieldset v-if="twoFactorRequired">
              <legend class="text-sm fw-bold">Send my codes by</legend>
              <div v-for="option in securityOptions" :key="option.value" class="form-check">
                <input
                  :id="`method-${option.value}`"
                  v-model="twoFactorMethodPreferred"
                  class="form-check-input"
                  type="radio"
                  :value="option.value"
                />
                <label class="form-check-label" :for="`method-${option.value}`">{{ option.label }}</label>
              </div>
              <span v-if="errors.two_factor_method_preferred" class="text-danger text-xs">
                {{ errors.two_factor_method_preferred }}
              </span>
            </fieldset>

            <div class="row mt-4">
              <div class="col-auto d-flex align-items-center">
                <button type="button" class="btn btn-link text-sm text-muted p-0 mb-0" @click="logout">Sign out</button>
              </div>
              <div class="col text-end">
                <button type="submit" class="btn btn-primary mb-0" :disabled="isSubmitting">Next</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
