<script setup lang="ts">
import { useField } from 'vee-validate'

/**
 * 6-digit code input bound to the surrounding vee-validate form's "code" field.
 * Strips spaces (codes are emailed as "123 456") and works with OS one-time-code autofill.
 */
const props = withDefaults(defineProps<{ name?: string; label?: string }>(), {
  name: 'code',
  label: 'Verification code',
})

const { value, errorMessage, handleBlur } = useField<string>(() => props.name)

function onInput(event: Event) {
  value.value = (event.target as HTMLInputElement).value.replace(/\s+/g, '')
}
</script>

<template>
  <label class="text-dark text-bolder form-label mb-0" :for="name">{{ label }}</label>
  <div class="input-group input-group-outline mb-3">
    <input
      :id="name"
      :name="name"
      :value="value"
      class="form-control d-block w-100"
      :class="{ 'is-invalid': errorMessage }"
      type="text"
      inputmode="numeric"
      autocomplete="one-time-code"
      maxlength="7"
      placeholder="123456"
      autofocus
      @input="onInput"
      @blur="handleBlur"
    />
    <span v-if="errorMessage" class="text-danger text-xs mt-1">{{ errorMessage }}</span>
  </div>
</template>
