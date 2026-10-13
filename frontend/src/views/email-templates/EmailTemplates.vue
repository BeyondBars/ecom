<template>
  <PageHeader title="Email Templates" description="Every email this app sends. Preview one or send yourself a test copy." />

  <div class="card overflow-hidden">
    <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
      <div class="relative sm:w-80">
        <FontAwesomeIcon icon="magnifying-glass" class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-slate-400" />
        <input v-model="search" type="search" class="form-input pl-9" placeholder="Search templates..." aria-label="Search templates" />
      </div>
      <select v-model="type" class="form-input sm:w-44" aria-label="Filter by type">
        <option value="">All types</option>
        <option value="transactional">Transactional</option>
        <option value="marketing">Marketing</option>
      </select>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
          <tr>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Subject</th>
            <th class="px-4 py-3">Type</th>
            <th class="px-4 py-3">Triggered by</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="template in filtered" :key="template.key" class="align-top">
            <td class="px-4 py-3">
              <div class="font-medium text-slate-900">{{ template.name }}</div>
              <div class="mt-0.5 max-w-xs text-xs text-slate-500">{{ template.description }}</div>
            </td>
            <td class="px-4 py-3 text-slate-600">{{ template.subject }}</td>
            <td class="px-4 py-3">
              <span class="rounded-full px-2 py-0.5 text-xs font-semibold whitespace-nowrap" :class="typeBadge[template.type]">
                {{ template.type_title }}
              </span>
            </td>
            <td class="px-4 py-3">
              <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs text-slate-700">{{ template.triggered_by }}</code>
            </td>
            <td class="px-4 py-3">
              <div class="flex justify-end gap-2 whitespace-nowrap">
                <button type="button" class="btn btn-secondary px-3 py-1.5" @click="openPreview(template)">
                  <FontAwesomeIcon icon="eye" /> Preview
                </button>
                <button type="button" class="btn btn-secondary px-3 py-1.5" @click="openSendTest(template)">
                  <FontAwesomeIcon icon="paper-plane" /> Send test
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="!store.isLoading && filtered.length === 0">
            <td colspan="5" class="px-4 py-8 text-center text-slate-500">No templates match your search.</td>
          </tr>
          <tr v-if="store.isLoading && store.templates.length === 0">
            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Loading templates...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <AppModal v-if="preview" title="Email template preview" size="xl" @close="preview = null">
    <dl class="mb-4 grid gap-x-4 gap-y-2 text-sm sm:grid-cols-[8rem_1fr]">
      <dt class="font-medium text-slate-900">Template</dt>
      <dd class="text-slate-600">{{ preview.name }}</dd>
      <dt class="font-medium text-slate-900">Subject</dt>
      <dd class="text-slate-600">{{ preview.subject }}</dd>
      <dt class="font-medium text-slate-900">Type</dt>
      <dd class="text-slate-600">{{ preview.type_title }}</dd>
      <dt class="font-medium text-slate-900">Triggered by</dt>
      <dd><code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs text-slate-700">{{ preview.triggered_by }}</code></dd>
    </dl>
    <p v-if="!preview.html" class="py-8 text-center text-sm text-slate-500">Loading preview...</p>
    <!-- Sandboxed with no permissions: the email's HTML can't run scripts or reach the app. -->
    <iframe
      v-else
      :srcdoc="preview.html"
      sandbox=""
      title="Email preview"
      class="h-[50vh] w-full rounded-lg border border-slate-200 bg-white"
    />
    <form class="mt-4" novalidate @submit.prevent="sendTest(preview)">
      <label class="form-label" for="preview-test-email">Send test email to</label>
      <div class="flex flex-col gap-2 sm:flex-row">
        <input
          id="preview-test-email"
          v-model="testEmail"
          type="email"
          class="form-input"
          :class="{ 'is-invalid': testError }"
          placeholder="Enter email address"
          required
        />
        <button type="submit" class="btn btn-primary shrink-0" :disabled="sending">
          <FontAwesomeIcon icon="paper-plane" /> {{ sending ? 'Sending...' : 'Send test' }}
        </button>
      </div>
      <span v-if="testError" class="form-error">{{ testError }}</span>
      <p class="mt-2 text-xs text-slate-500">Sent with sample data and "[TEST]" in the subject. Links in it won't work.</p>
    </form>
  </AppModal>

  <AppModal v-if="testTemplate" :title="`Send test: ${testTemplate.name}`" @close="testTemplate = null">
    <form novalidate @submit.prevent="sendTest(testTemplate)">
      <p class="mb-4 text-sm text-slate-600">
        Sends this email with sample data and "[TEST]" in the subject. Links in it won't work.
      </p>
      <label class="form-label" for="test-email">Send to</label>
      <input id="test-email" v-model="testEmail" type="email" class="form-input" :class="{ 'is-invalid': testError }" required />
      <span v-if="testError" class="form-error">{{ testError }}</span>
      <div class="mt-5 flex justify-end gap-2">
        <button type="button" class="btn btn-secondary" @click="testTemplate = null">Cancel</button>
        <button type="submit" class="btn btn-primary" :disabled="sending">
          {{ sending ? 'Sending...' : 'Send test email' }}
        </button>
      </div>
    </form>
  </AppModal>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'
import { isAxiosError } from 'axios'
import { useToast } from 'vue-toast-notification'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import PageHeader from '@/components/app/PageHeader.vue'
import AppModal from '@/components/app/AppModal.vue'
import { useEmailTemplateStore } from '@/stores/email-templates'
import { useLoggedInUser } from '@/composables/use-logged-in-user'
import type { EmailTemplateResource, EmailTemplateType } from '@/types/email-template'

const store = useEmailTemplateStore()
const toast = useToast()
const { loggedInUser } = useLoggedInUser()

const typeBadge: Record<EmailTemplateType, string> = {
  transactional: 'bg-brand-50 text-brand-700',
  marketing: 'bg-emerald-100 text-emerald-700',
}

const search = ref('')
const type = ref<'' | EmailTemplateType>('')

const filtered = computed(() => {
  const term = search.value.trim().toLowerCase()

  return store.templates.filter(
    (template) =>
      (type.value === '' || template.type === type.value) &&
      (term === '' ||
        [template.name, template.subject, template.triggered_by, template.description].some((value) =>
          value.toLowerCase().includes(term)
        ))
  )
})

const testTemplate = ref<EmailTemplateResource | null>(null)
const testEmail = ref('')
const testError = ref('')
const sending = ref(false)

const resetTestForm = () => {
  testEmail.value = testEmail.value || loggedInUser.value.email
  testError.value = ''
}

const preview = ref<EmailTemplateResource | null>(null)

const openPreview = async (template: EmailTemplateResource) => {
  preview.value = { ...template }
  resetTestForm()

  try {
    const loaded = await store.fetchPreview(template.key)

    // Ignore a slow response for a preview that has since been closed or swapped.
    if (preview.value?.key === template.key) {
      preview.value = loaded
    }
  } catch {
    toast.error('Could not load the preview.')
    preview.value = null
  }
}

const openSendTest = (template: EmailTemplateResource) => {
  testTemplate.value = template
  resetTestForm()
}

const sendTest = async (template: EmailTemplateResource | null) => {
  if (!template || sending.value) return

  sending.value = true
  testError.value = ''

  try {
    toast.success(await store.sendTest(template.key, testEmail.value))
    testTemplate.value = null
  } catch (error) {
    if (isAxiosError(error) && error.response?.status === 422) {
      testError.value = error.response.data.errors?.email?.[0] ?? error.response.data.message
    } else if (isAxiosError(error) && error.response?.status === 429) {
      testError.value = 'Too many test emails. Try again in a minute.'
    } else {
      toast.error(isAxiosError(error) && error.response?.data?.message ? error.response.data.message : 'Could not send the test email.')
    }
  } finally {
    sending.value = false
  }
}

onMounted(() => store.fetchTemplates().catch(() => toast.error('Could not load email templates.')))
</script>
