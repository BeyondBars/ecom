<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto">
        <div class="card card-authentication">
          <form role="form" class="card-body" @submit="onSubmit">
            <router-link :to="{ name: 'login' }">
              <img class="logo" src="@/assets/img/logo/logo.png" alt="She Codes" />
            </router-link>

            <template v-if="sent">
              <h4 class="text-primary font-weight-bolder mb-4">Password Reset Sent</h4>

              <p class="text-muted text-sm">
                An email has been sent to <strong>{{ sent }}</strong> with instructions for resetting your
                password.
              </p>

              <div class="row">
                <div class="col-auto text-right d-flex align-items-center">
                </div>
              </div>
            </template>

            <template v-else>
              <h4 class="text-primary font-weight-bolder mb-4">Forgot Password?</h4>

              <p class="text-muted text-sm">
                If you have forgotten your password, enter your email address below to get your password
                reset.
              </p>
              <HTBFormField name="email" label="Email" type="email" />
            </template>

            <div class="row mt-4">
              <div class="col-auto text-end d-flex align-items-center">
              </div>
              <div class="col text-end">
                <button type="submit" class="btn btn-primary">
                  <span class="label">Next</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { useForm, useField } from 'vee-validate'
import { useAppStore } from '@/stores/app'
import HTBFormField from '@/components/HTBFormField.vue'
import { useFocus } from '@vueuse/core'
import { isAxiosError } from 'axios'
import * as yup from 'yup'
const sent = ref()
const input = ref()

const { emailForgotPassword } = useAppStore()

// Define validation schema
const schema = yup.object({
  email: yup.string().email('The email must be valid.').required('The email field is required.'),
})

const { handleSubmit, setErrors } = useForm({
  validationSchema: schema,
  initialValues: {
    email: ''
  }
})
const { value: email } = useField('email')

const onSubmit = handleSubmit(async () => {
  try {
    const payload = {
      email: email.value
    }
    await emailForgotPassword(payload)
    sent.value = payload.email
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)
    }
    throw error
  }
})
</script>
