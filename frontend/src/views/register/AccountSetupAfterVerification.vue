<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto">
        <div class="card z-index-0 fadeIn3 fadeInBottom">
          <div class="card-body">
            <img class="logo mt-4" src="@/assets/img/logo/logo.png" alt="She Codes" />
            <h4 class="text-primary font-weight-bolder mb-4">Account Setup</h4>
            <form role="form" class="text-start" @submit="onSubmit">
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
                  <a class="text-decoration-underline" href="#" target="_blank">privacy policy</a> 
                  and 
                  <a class="text-decoration-underline" :href="termsAndConditionsUrl" target="_blank">terms and conditions</a> of use.
                </HTBFormCheckbox>
              </div>

              <div class="row mt-4 mb-5">
                <div class="col-auto text-end d-flex align-items-center">
                  <a href="/" class="mb-2 text-sm mx-auto">Back to login.</a>
                </div>
                <div class="col text-end">
                  <button class="btn btn-primary">
                    <span class="label">Verify</span>
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
import HTBFormField from '@/components/HTBFormField.vue'
import HTBPhoneInput from '@/components/HTBPhoneInput.vue'
import HTBFormCheckbox from '@/components/HTBFormCheckbox.vue'
import { isAxiosError } from 'axios'
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { useForm, useField } from 'vee-validate'
import { useAppStore } from '@/stores/app'
import { ref, watch, computed } from 'vue'
import * as yup from 'yup'
import { useValidation } from '@/utils/useValidation'
import { useRoute, useRouter } from 'vue-router'
import PasswordStrengthBar from '@/components/auth/PasswordStrengthBar.vue'
import { useToast } from 'vue-toast-notification'
const toast = useToast()
const router = useRouter()

const appStore = useAppStore()
const route = useRoute()
const termsAndConditionsUrl = ref('#')


/* 3️⃣ 📜 Validation Schema */
const schema = yup.object({
  first_name: yup.string().required(),
  last_name: yup.string().required(),
  mobile: yup.string().required(),
  password: yup.string().required(),
  password_confirmation: yup.string().required(),
  agree_terms: yup.boolean().oneOf([true], 'You must accept the terms and conditions.')
})

/* 4️⃣ 📝 Form Handling */
const { handleSubmit, resetForm, setErrors } = useForm({
  validationSchema: schema,
  initialValues: {
    first_name: null,
    last_name: null,
    mobile: null,
    password: null,
    password_confirmation: null,
    agree_terms: false
  }
})

const { value: first_name, errorMessage: firstNameError } = useField('first_name')
const { value: last_name, errorMessage: lastNameError } = useField('last_name')
const { value: password, errorMessage: passwordError } = useField('password')
const { value: password_confirmation, errorMessage: confirmPasswordError } = useField('password_confirmation')
const { value: agree_terms, errorMessage: agreeTermsError } = useField('agree_terms')
const { accountSetup, verifySignature } = useAppStore()
const verifyOnLoad = async () => {
  try {
    if (route.params.signature && route.query.email) {
      await verifySignature({ signature: route.params.signature, expires: route.query.expires, email: route.query.email })
    }
  } catch (error) {
    toast.error(error.response.data.message)
    router.replace({ name: 'login' })
  }
}
verifyOnLoad()

const onSubmit = handleSubmit(async (payload) => {
  try {
    await accountSetup({...payload, email: route.query.email, token: route.query.token})
    toast.success('Account created successfully')

    if (route.query.redirect) {
      router.replace(route.query.redirect as string)
    } else {
      router.replace({ name: 'dashboard' })
    }
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)
      if (error.response.status === 429) return // No need to throw for rate limit errors
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
