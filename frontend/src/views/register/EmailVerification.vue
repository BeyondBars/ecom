<template>
  <div class="card-body">
    <h4 class="text-primary font-weight-bolder mt-5 mb-4">Sign Up</h4>
    <form role="form" class="text-start">
      <p class="text-muted text-sm">
        An email has been sent to your email address with instructions for creating an account.
      </p>
      <div class="row mt-4 mb-5">
        <div class="col text-end">
          <a href="/" class="btn btn-primary">
            <span class="label">Back to login.</span>
          </a>
        </div>
      </div>
    </form>
  </div>  
</template>

<script setup lang="ts">
  import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
  import { watchEffect } from 'vue'
</script>
