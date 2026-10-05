<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto">
        <div class="card z-index-0 fadeIn3 fadeInBottom">
          <div class="card-body">
            <router-link :to="{ name: 'login' }">
              <img class="logo" src="@/assets/img/logo/logo.png" alt="She Codes" />
            </router-link>
            <form role="form" class="card-body" @submit="onSubmit">

              <h4 class="text-primary font-weight-bolder mb-4">Set Password</h4>

              
              <HTBFormField name="password" label="Password" type="password" />
              <HTBFormField name="password_confirmation" label="Confirm Password" type="password" />
              
              <PasswordStrengthBar class="mb-4" :password="password" label="Password Strength" />

              <div class="row">
                <div class="col text-end">
                  <button class="btn btn-primary">
                    <span class="label">Set Password</span>
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

<script lang="ts" setup>
import { http } from '@/utils'
import HTBFormField from '@/components/HTBFormField.vue'
import PasswordStrengthBar from '@/components/auth/PasswordStrengthBar.vue'
import { useToast } from 'vue-toast-notification'
import { ref, watch, computed } from 'vue'
import * as yup from 'yup'
import { useValidation } from '@/utils/useValidation'
import { useForm, useField } from 'vee-validate'
import { useAppStore } from '@/stores/app'
import { useRouteQuery } from '@vueuse/router'
import { isAxiosError } from 'axios'
import { useRouter } from 'vue-router'

const toast = useToast()
const router = useRouter()
const props = defineProps<{
  token: string
}>()
const email = useRouteQuery<string | null>('email')

/* 3️⃣ 📜 Validation Schema */
const schema = yup.object({
  password: yup.string().required(),
  password_confirmation: yup.string().required()
})

/* 4️⃣ 📝 Form Handling */
const { handleSubmit, resetForm, setErrors } = useForm({
  validationSchema: schema,
  initialValues: {
    email: email.value,
    token: props.token,
    password: null,
    password_confirmation: null
  }
})
const { value: password, errorMessage: passwordError } = useField('password')
const { value: password_confirmation, errorMessage: confirmPasswordError } = useField('password_confirmation')

const { accountSetup, verifySignature } = useAppStore()
const { resetPassword } = useAppStore()

const onSubmit = handleSubmit(async (payload) => {
  try {
    await resetPassword(payload)
    toast.success('Password reset successfully')

    router.replace({ name: 'login' })
  } catch (error) {
    if (
      isAxiosError(error) &&
      error.response &&
      error.response.status === 422 &&
      error.response.data.errors.generic
    ) {
      toast.error(error.response.data.errors.generic[0])
      router.replace({ name: 'login' })
    }
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)
      if (error.response.status === 429) return // No need to throw for rate limit errors
    }
    throw error
  }
})
</script>
