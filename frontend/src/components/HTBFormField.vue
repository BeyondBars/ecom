<script>
import { useField } from 'vee-validate'
import { computed } from 'vue'

export default {
  name: 'HTBFormField',
  props: {
    name: { type: String, required: true },
    label: { type: String, default: '' },
    type: { type: String, default: 'text' },  // Default type is 'text'
    formGroup: { type: Boolean, default: true },
    className: { type: String, default: '' },
    placeholder: { type: String, default: '' }
  },
  setup(props) {
    // Get field data from VeeValidate
    const { value, errorMessage, handleBlur, handleChange } = useField(props.name)

    // Compute the classes dynamically based on validation state
    const fieldClasses = computed(() => ({
      'is-invalid': errorMessage.value,  // Red border on error
      'is-valid': !errorMessage.value && value.value, // Green border when valid
      [props.className]: props.className, // Allow custom classes
    }))

    return {
      fieldClasses,
      value,
      errorMessage,
      handleBlur,
      handleChange
    }
  }
}
</script>

<template>
  <!-- Label display -->
  <label class="text-dark text-bolder form-label mb-0" v-if="label">{{ label }}</label>
  <div class="input-group input-group-outline mb-3" :class="{ 'form-group-outline': formGroup }">
    <!-- Bind input fields with vee-validate properties -->
    <input
      :class="`form-control d-block w-100 ${className}`"
      v-bind="{
        value,
        type,
        class: fieldClasses,
        placeholder: placeholder
      }"
      @input="handleChange"
      @blur="handleBlur"
    />
    <span class="text-danger text-xs mt-1" v-if="errorMessage">{{ errorMessage }}</span>
  </div>
</template>
