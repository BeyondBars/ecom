<script>
import { useField } from 'vee-validate'
import { computed } from 'vue'

export default {
  name: 'HTBFormField',
  props: {
    name: { type: String, required: true },
    label: { type: String, default: '' },
    type: { type: String, default: 'text' }, // Default is 'text'
    formGroup: { type: Boolean, default: true },
    className: { type: String, default: '' },
    placeholder: { type: String, default: '' }
  },
  setup(props) {
    // Get field data from VeeValidate
    const { value, errorMessage, handleBlur, handleChange } = useField(props.name)

    // Compute classes dynamically
    const fieldClasses = computed(() => ({
      'is-invalid': errorMessage.value, // Red border if invalid
      'is-valid': !errorMessage.value && value.value, // Green border if valid
      [props.className]: props.className // Custom class support
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
  <!-- Checkbox Field -->
  <div class="ps-0 form-check form-check-info text-start">
    <input 
      class="form-check-input ms-0" 
      type="checkbox" 
      v-model="value"
      @blur="handleBlur"
    />
    <label class="form-check-label">
      <slot>{{ label }}</slot>
    </label>
    <span class="text-danger text-xs mt-1 d-block" v-if="errorMessage">{{ errorMessage }}</span>
  </div>
</template>
