<template>
  <div>
    <p v-if="label" class="label">
      {{ label }}
    </p>
    <div class="progress">
      <div
        :class="['progress-bar', strengthClass]"
        role="progressbar"
        :style="`width: ${strength}%`"
        :aria-valuenow="strength"
        aria-valuemin="0"
        aria-valuemax="100"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import zxcvbn from 'zxcvbn'

const props = defineProps<{
  label: string
  password?: string
}>()

const strength = computed(() => {
  const { score } = zxcvbn(props.password ?? '')
  switch (score) {
    case 0:
      return 0
    case 1:
      return 25
    case 2:
      return 50
    case 3:
      return 75
    case 4:
      return 100
    default:
      return 0
  }
})

const strengthClass = computed(() => {
  switch (strength.value) {
    case 0:
    case 25:
      return 'bg-danger'
    case 50:
      return 'bg-warning'
    case 75:
    case 100:
      return 'bg-success'
    default:
      return 'bg-danger'
  }
})
</script>
