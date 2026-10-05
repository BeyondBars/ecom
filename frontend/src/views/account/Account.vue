<template>
  <div class="container-fluid px-2 px-md-4">
    <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('./../../assets/img/unspash-header.png');">
      <span class="mask  bg-gradient-primary  opacity-6"></span>
    </div>
    <div class="card card-body mx-3 mx-md-4 mt-n6">
      <div class="row gx-4 mb-2">
        <div class="col-auto">
          <div class="avatar avatar-xl position-relative">
            <i class="material-icons opacity-10">account_circle</i>
          </div>
        </div>
        <div class="col-auto my-auto">
          <div class="h-100">
            <h5 class="mb-1">
              {{ loggedInUser.name }}
            <span class="mb-0 font-weight-normal text-sm text-success">
              (Last login: {{ $date(loggedInUser.last_login_at, { format: 'DD/MM/YYYY [at] h:mmA' }) }} from {{ loggedInUser.last_login_ip }})
            </span>
            </h5>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-xl-12 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
          <div class="nav-wrapper position-relative end-0">
            <ul class="nav nav-pills nav-fill p-1" role="tablist">
              <li class="nav-item" @click="setActive(1)">
                <a :class="`nav-link cursor-pointer mb-0 px-0 py-1 ${activeTab === 1 ? 'active' : ''}`">
                  <i class="material-icons text-lg text-primary position-relative">home</i>
                  <span class="ms-1">Account Details</span>
                </a>
              </li>
              <li class="nav-item" @click="setActive(2)">
                <a :class="`nav-link cursor-pointer mb-0 px-0 py-1 ${activeTab === 2 ? 'active' : ''}`">
                  <i class="material-icons text-lg text-primary position-relative">fingerprint</i>
                  <span class="ms-1">Account Security</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-12 col-xl-12">
          <div class="card card-plain h-100" v-if="activeTab === 1">
            <div class="row">
              <div class="col-12 col-xl-6">
                <div class="card-body p-3">
                  <div class="card-header ps-0 p-3">
                    <h6 class="mb-0">Account Details</h6>
                  </div>
                  <form role="form" class="text-start" @submit="onSubmit">
                    <label class="text-dark text-bolder form-label mb-0">First Name</label>
                    <div class="input-group input-group-outline mb-3 form-group-outline">
                      <!-- Bind input fields with vee-validate properties -->
                      <input
                        :class="`form-control d-block w-100`"
                        v-model="first_name"
                        type="text"
                      />
                      <!-- <span class="text-danger text-xs mt-1" v-if="errorMessage">{{ errorMessage }}</span> -->
                    </div>
                    <label class="text-dark text-bolder form-label mb-0">Last Name</label>
                    <div class="input-group input-group-outline mb-3 form-group-outline">
                      <!-- Bind input fields with vee-validate properties -->
                      <input
                        :class="`form-control d-block w-100`"
                        v-model="last_name"
                        type="text"
                      />
                      <!-- <span class="text-danger text-xs mt-1" v-if="errorMessage">{{ errorMessage }}</span> -->
                    </div>

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
          <div class="card card-plain h-100" v-if="activeTab === 2">
            <div class="row">
              <div class="col-12 col-xl-6">
                <div class="card-body p-3">
                  <div class="card-header ps-0 p-3">
                    <h6 class="mb-0">Change Password</h6>
                  </div>
                  <form role="form" class="text-start" @submit="onPasswordSubmit">
                    <HTBFormField name="current_password" label="Current Password" type="password" />
                    <HTBFormField name="password" label="Password" type="password" />
                    <HTBFormField name="repeat_password" label="Repeat Password" type="password" />
                    <button type="submit" class="btn btn-primary float-end">
                      <span class="label">Change Password</span>
                    </button>
                  </form>
                </div>
              </div>
              <div class="col-12 col-xl-6">
                <div class="card-body p-3">
                  <div class="card-header ps-0 p-3">
                    <h6 class="mb-0">2-Factor Authentication</h6>
                  </div>
                  <form role="form" class="text-start" @submit="onAuthSubmit">
                    <ul class="list-group">
                      <li class="list-group-item border-0 px-0">
                        <div class="form-check form-switch ps-0">
                          <input class="form-check-input ms-auto" v-model="two_factor_required" name="two_factor_required" type="checkbox" checked />
                          <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0 fw-bold">Use 2-factor when logging in?</label>
                        </div>
                      </li>
                    </ul>
                    <div class="flex items-center space-x-6" v-if="two_factor_required">
                      <label
                        v-for="option in options"
                        :key="option.value"
                        class="flex items-center cursor-pointer me-4"
                      >
                        <input
                          type="radio"
                          v-model="two_factor_method_preferred"
                          :value="option.value"
                          class="hidden peer me-1"
                        />
                        <span
                          class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center peer-checked:border-blue-900 peer-checked:bg-blue-900 transition"
                        >
                          <span class="w-2.5 h-2.5 bg-white rounded-full peer-checked:block hidden"></span>
                        </span>
                        <span class="text-gray-700">{{ option.label }}</span>
                      </label>
                    </div>
                    <button type="submit" class="btn btn-primary float-end">
                      <span class="label">Update</span>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useUserStore } from '@/stores/users'
import { useLoggedInUser } from '@/composables/use-logged-in-user'
import { useAppStore } from '@/stores/app'
import { isAxiosError } from 'axios'
import { useForm, useField } from 'vee-validate'
import { watch, ref, onMounted, watchEffect } from 'vue'
import * as yup from 'yup'
import HTBFormField from '@/components/HTBFormField.vue'
import { storeToRefs } from 'pinia'
import { useRouter, useRoute } from 'vue-router'
import { useToast } from 'vue-toast-notification'
import { http } from '@/utils/http'
import HTBPhoneInput from '@/components/HTBPhoneInput.vue'

const usersStore = useUserStore()
const { fetchUser, updateUser } = usersStore
const activeTab = ref(1)
const { loggedInUser, getLoggedInUser } = useLoggedInUser()
const { updateTwoFactor } = useAppStore()
const router = useRouter()
const route = useRoute()
const toast = useToast()

// Define validation schema
const schema = yup.object({
  first_name: yup.string(),
  last_name: yup.string(),
  email: yup.string(),
  two_factor_enabled: yup.boolean(),
  two_factor_method_preferred: yup.string(),
  two_factor_required: yup.boolean()
})

const { handleSubmit, setErrors, resetForm, setFieldValue } = useForm({
  validationSchema: schema,
  initialValues: {
    email: usersStore.user?.email, // Set initial email value
    mobile: usersStore.user?.mobile // Checkbox default to unchecked
  }
})

const { value: first_name, errorMessage: firstNameError } = useField('first_name')
const { value: last_name, errorMessage: lastNameError } = useField('last_name')
const { value: email, errorMessage: emailError } = useField('email')
const { value: mobile, errorMessage: mobileError } = useField('mobile')
const { value: two_factor_required, errorMessage: twoFactorRequiredError } = useField('two_factor_required')
const { value: two_factor_enabled, errorMessage: twoFactorEnabledError } = useField('two_factor_enabled')
const { value: two_factor_method_preferred, errorMessage: twoFactorMethodPreferredError } = useField('two_factor_method_preferred')

const loadUser = async () => {
  await fetchUser(loggedInUser.value.id)
  const user = usersStore.user

  // Ensure user data is available before setting values
  if (user) {
    setFieldValue("first_name", user.first_name)
    setFieldValue("last_name", user.last_name)
    setFieldValue("email", user.email)
    setFieldValue("mobile", user.mobile)
    setFieldValue("two_factor_required", user.two_factor_required)
    setFieldValue("two_factor_enabled", user.two_factor_enabled)
    setFieldValue("two_factor_method_preferred", user.two_factor_method_preferred || 'email')
  }
}

// Fetch user on component mount
onMounted(loadUser)

const onSubmit = handleSubmit(async (payload) => {
  try {
    await updateUser(usersStore.user.id, payload)
    toast.success('Account details updated successfully!')
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)

      if (error.response.status === 429) return // No need to throw for rate limit errors
    }

    throw error
  }
})

const onPasswordSubmit = handleSubmit(async (payload) => {
  try {
    await http.put(`/users/${usersStore.user.id}/password`, {
      current_password: payload.current_password,
      password: payload.password,
      password_confirmation: payload.repeat_password
    })

    toast.success('Password updated successfully.')
    resetForm()
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)

      if (error.response.status === 429) return // No need to throw for rate limit errors
    }

    throw error
  }
})




const onAuthSubmit = handleSubmit(async (payload) => {
  try {
    await updateTwoFactor({
      two_factor_required: payload.two_factor_required,
      two_factor_method_preferred: payload.two_factor_method_preferred
    })
    await getLoggedInUser()
    if (payload.two_factor_required) {
      if (payload.two_factor_method_preferred === 'sms') {
        router.replace({ name: 'two-factor.setup.sms' })
      } else if (payload.two_factor_method_preferred === 'email') {
        router.replace({ name: 'two-factor.setup.email' })
      }
    }

    toast.success('Two factor updated successfully.')
    resetForm()
  } catch (error) {
    if (isAxiosError(error) && error.response?.data?.errors) {
      setErrors(error.response.data.errors)

      if (error.response.status === 429) return // No need to throw for rate limit errors
    }

    throw error
  }
})

const setActive = (tab) => {
  activeTab.value = tab
}
const options = [
  { value: 'sms', label: 'SMS' },
  { value: 'email', label: 'Email' }
]

</script>