<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card my-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
              <h6 class="text-white text-capitalize ps-3">Settings</h6>
            </div>
          </div>
          <div class="card-body px-0">
            <div class="table-responsive p-0 scrolling-component" id="scrolling-component-1">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th>Role</th>
                    <th class="ps-2">Description</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="role in rolesStore.roles" :key="role.id">
                    <td>
                      <div class="d-flex py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <p class="mb-0 text-sm">{{ role.title }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                      <p class="text-xs text-secondary mb-0">{{ role.description }}</p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { onMounted } from 'vue'
import { useRoleStore } from '@/stores/roles'

const rolesStore = useRoleStore()

onMounted(() => {
  rolesStore.fetchRoles()
})
</script>
