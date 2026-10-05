<script setup lang="ts">
/* 1️⃣ 📦 Imports */
import EmailVerification from './EmailVerification.vue'
import { isAxiosError } from 'axios'
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { useForm, useField } from 'vee-validate'
import { useAppStore } from '@/stores/app'
import { ref, watch, computed } from 'vue'
import * as yup from 'yup'
import { useValidation } from '@/utils/useValidation'
import HTBFormField from '@/components/HTBFormField.vue'
import { useRoute, useRouter } from 'vue-router'

/* 2️⃣ 🔗 Refs & Reactive Variables */
const termsAndConditionsUrl = ref('https://example.com/terms')
const appStore = useAppStore()
const route = useRoute()
const router = useRouter()
const notificationSent = ref(false)

/* 3️⃣ 📜 Validation Schema */
const schema = yup.object({
  email: yup.string().email('The email must be valid.').required('The email field is required.')
})

/* 4️⃣ 📝 Form Handling */
const { handleSubmit, resetForm, setErrors } = useForm({
  validationSchema: schema,
  initialValues: {
    email: null, // Set initial email value
    agree_terms: false // Checkbox default to unchecked
  }
})

/* 5️⃣ 👀 Watchers & Computed */
const { value: email, errorMessage: emailError } = useField('email')

const { verify } = useAppStore()
const onSubmit = handleSubmit(async (payload) => {
  try {
    await verify(payload)
    notificationSent.value = true
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)
      if (error.response.status === 429) return // No need to throw for rate limit errors
    }
    throw error
  }
})
</script>

<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto">
        <div class="card z-index-0 fadeIn3 fadeInBottom">
          <div class="card-body" v-if="!notificationSent">
            <img class="logo mt-4" src="@/assets/img/logo/logo.png" alt="She Codes" />
            <h4 class="text-primary font-weight-bolder mb-4">Sign Up</h4>
            <form role="form" class="text-start" @submit.prevent="onSubmit">
              <HTBFormField name="email" label="Email Address" type="email" />
              <div class="row mt-4 mb-5">
                <div class="col-auto text-end d-flex align-items-center">
                  <a href="/" class="mb-2 text-sm mx-auto">Back to login.</a>
                </div>
                <div class="col text-end">
                  <button type="submit" class="btn btn-primary">
                    <span class="label">Next</span>
                  </button>
                </div>
              </div>
            </form>
          </div>
          <email-verification :email="email" v-else />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@media (min-width: 1024px) {
  .about {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
</style>
