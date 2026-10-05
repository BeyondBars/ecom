<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto">
        <div class="card z-index-0 fadeIn3 fadeInBottom">
          <div class="card-body">
            <img class="logo mt-4" src="@/assets/img/logo/logo.png" alt="Invoice" />
            <h4 class="text-primary font-weight-bolder mb-4">Account Setup</h4>
            <form role="form" class="text-start" novalidate @submit="onSubmit">
              <div class="mt-4 mb-4">
                <HTBFormField name="first_name" label="First Name" type="text" />
                <HTBFormField name="last_name" label="Last Name" type="text" />
                <HTBPhoneInput name="mobile" label="Mobile" type="text" />
                <HTBFormField name="password" label="Password" type="password" />
                <HTBFormField name="password_confirmation" label="Confirm Password" type="password" />
                <PasswordStrengthBar class="mb-4" :password="password" label="Password Strength" />
                <HTBFormCheckbox
                  name="agree_terms" 
                  type="checkbox"
                  className="mt-3 text-danger"
                >
                  I agree to the 
                  <a class="text-decoration-underline" href="#" target="_blank" rel="noopener">privacy policy</a> 
                  and 
                  <a class="text-decoration-underline" :href="termsAndConditionsUrl" target="_blank" rel="noopener">terms and conditions</a> of use.
                </HTBFormCheckbox>
              </div>

              <div class="row mt-4 mb-5">
                <div class="col-auto text-end d-flex align-items-center">
                  <RouterLink :to="{ name: 'login' }" class="mb-2 text-sm mx-auto">Back to login.</RouterLink>
                </div>
                <div class="col text-end">
                  <button type="submit" class="btn btn-primary" :disabled="!verified || isSubmitting">
                    <span class="label">Create Account</span>
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
<script setup lang="ts">
import { ref } from 'vue'
import { isAxiosError } from 'axios'
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toast-notification'
import { useAppStore } from '@/stores/app'
import HTBFormField from '@/components/HTBFormField.vue'
import HTBPhoneInput from '@/components/HTBPhoneInput.vue'
import HTBFormCheckbox from '@/components/HTBFormCheckbox.vue'
import PasswordStrengthBar from '@/components/auth/PasswordStrengthBar.vue'

/**
 * /verify/:signature?email=...&expires=... - registration step 3. The signed link is validated on
 * load, then the account is created and the API logs the new user straight in.
 */
const props = defineProps<{ signature: string }>()

const toast = useToast()
const router = useRouter()
const route = useRoute()
const appStore = useAppStore()

const termsAndConditionsUrl = import.meta.env.VITE_TERMS_URL || '#'
const email = String(route.query.email ?? '')
const verified = ref(false)

const { handleSubmit, setErrors, isSubmitting } = useForm({
  validationSchema: yup.object({
    first_name: yup.string().required('The first name is required.'),
    last_name: yup.string().required('The last name is required.'),
    mobile: yup.string().nullable(),
    password: yup.string().required('The password is required.'),
    password_confirmation: yup
      .string()
      .required('Please confirm the password.')
      .oneOf([yup.ref('password')], 'The passwords do not match.'),
    agree_terms: yup.boolean().oneOf([true], 'You must accept the terms and conditions.'),
  }),
  initialValues: {
    first_name: '',
    last_name: '',
    mobile: '',
    password: '',
    password_confirmation: '',
    agree_terms: false,
  },
})

const { value: password } = useField<string>('password')

const verifyOnLoad = async () => {
  try {
    await appStore.verifySignature({
      signature: props.signature,
      expires: String(route.query.expires ?? ''),
      email,
    })
    verified.value = true
  } catch (error) {
    const message = isAxiosError(error) ? error.response?.data?.message : undefined
    toast.error(message || 'This sign up link is invalid or has expired.')
    router.replace({ name: 'login' })
  }
}
verifyOnLoad()

const onSubmit = handleSubmit(async (payload) => {
  try {
    await appStore.accountSetup({ ...payload, mobile: payload.mobile || null, email })
    toast.success('Account created successfully')

    // The user is now logged in; the router middleware takes care of any remaining setup steps.
    router.replace((route.query.redirect as string) || { name: 'dashboard' })
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)
      return
    }
    if (isAxiosError(error) && error.response?.data?.message) {
      toast.error(error.response.data.message)
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
