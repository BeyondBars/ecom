<template>
  <RouterLink
    v-if="to"
    v-slot="{ href, isActive, navigate }"
    v-bind="{ to }"
    custom
  >
    <li
      class="nav-item"
      @click="navigate"
    >
      <a
        class="nav-link text-white"
        :class="{ active: isActive || isChildActive, 'bg-gradient-primary': isActive }"
        v-bind="{ href }"
      >
        <i class="material-icons opacity-10">
          {{ icon }}
        </i>

        <span class="nav-link-text ms-1">
          <slot />
        </span>
      </a>
    </li>
  </RouterLink>
  <li
    v-else
    class="nav-item"
  >
    <a
      class="nav-link text-white"
      :class="{ active: isActive || isChildActive, 'bg-gradient-primary': isActive }"
      href=""
    >
      <i class="material-icons opacity-10">
        {{ icon }}
      </i>

      <span class="nav-link-text ms-1"><slot /></span>
    </a>
  </li>
</template>

<script lang="ts" setup>
import { useRoute, useRouter, type RouteLocationRaw } from 'vue-router'
import { computed } from 'vue'

const props = defineProps<{
  icon: string
  to?: RouteLocationRaw
}>()

const currentRoute = useRoute()
const router = useRouter()

// Get the resolved route for this items to location so we can compare the path.
const route = computed(() => (props.to ? router.resolve(props.to) : null))

// Determine if a child route is active based on path match.
const isChildActive = computed(() => {
  return route.value ? currentRoute.path.startsWith(route.value.path) : false
})

const isActive = computed(() => {
  return route.value ? currentRoute.path.startsWith(route.value.path) : false
})
</script>
