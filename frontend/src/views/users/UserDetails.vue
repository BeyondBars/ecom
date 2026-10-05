<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card my-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
              <h6 class="text-white text-capitalize ps-3">Edit User</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-xl-6 p-4">
              <form role="form" class="text-start" @submit="onSubmit">
                <HTBFormField name="first_name" label="First Name" type="text" />
                <HTBFormField name="last_name" label="Last Name" type="text" />
                <HTBPhoneInput name="mobile" label="Mobile" type="text" />
                <label class="text-dark text-bolder form-label mb-0">Email</label>
                <div class="input-group input-group-outline mb-3 form-group-outline">
                  <!-- Bind input fields with vee-validate properties -->
                  <input
                    :class="`form-control d-block w-100`"
                    v-model="email"
                    type="email"
                    :disabled="true"
                  />
                  <!-- <span class="text-danger text-xs mt-1" v-if="errorMessage">{{ errorMessage }}</span> -->
                </div>
                <button type="submit" class="btn btn-primary float-end">
                  <span class="label">Update Details</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import HTBFormField from '@/components/HTBFormField.vue'
import HTBPhoneInput from '@/components/HTBPhoneInput.vue'

import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'vue-toast-notification'
import { isAxiosError } from 'axios'

import { useUserStore } from '@/stores/users'
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'

// Toast instance
const toast = useToast()

// Router and Store
const route = useRoute()
const usersStore = useUserStore()
const { fetchUser, updateUser, resetUser } = usersStore

// Loading state
const isLoading = ref(false)

// Yup validation schema
const schema = yup.object({
  first_name: yup.string(),
  last_name: yup.string(),
  email: yup.string(),
  mobile: yup.string()
})

// Setup vee-validate form
const { handleSubmit, setErrors, resetForm, setFieldValue } = useForm({
  validationSchema: schema,
  initialValues: {
    first_name: usersStore.user?.first_name,
    last_name: usersStore.user?.last_name,
    email: usersStore.user?.email,
    mobile: usersStore.user?.mobile
  }
})

// Bind form fields
const { value: first_name, errorMessage: firstNameError } = useField('first_name')
const { value: last_name, errorMessage: lastNameError } = useField('last_name')
const { value: email, errorMessage: emailError } = useField('email')
const { value: mobile, errorMessage: mobileError } = useField('mobile')

// Load user details from store
const loadUser = async () => {
  await fetchUser(route.params.id)
  const user = usersStore.user

  if (user) {
    setFieldValue('first_name', user.first_name)
    setFieldValue('last_name', user.last_name)
    setFieldValue('email', user.email)
    setFieldValue('mobile', user.mobile)
  }
}

// Run on component mount
onMounted(loadUser)

// Clean up when component unmounts
onUnmounted(() => {
  resetUser()
})

// Form submit handler
const onSubmit = handleSubmit(async (payload) => {
  try {
    await updateUser(route.params.id, payload)
    toast.success('Account details updated successfully!')
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)
      if (error.response.status === 429) return
    }

    throw error
  }
})
</script>
