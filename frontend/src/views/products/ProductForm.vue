<template>
  <PageHeader :title="isNew ? 'Add product' : form.name || 'Edit product'">
    <RouterLink :to="{ name: 'products' }" class="link-muted">← Back to products</RouterLink>
  </PageHeader>

  <p v-if="!isLoaded" class="text-sm text-slate-500">Loading…</p>
  <form v-else class="grid grid-cols-1 gap-6 xl:grid-cols-3" @submit.prevent="save">
    <div class="space-y-6 xl:col-span-2">
      <section class="card space-y-4 p-5">
        <div>
          <label class="form-label" for="name">Name *</label>
          <input id="name" v-model="form.name" class="form-input" :class="{ 'is-invalid': errors.name }" required />
          <span v-if="errors.name" class="form-error">{{ errors.name }}</span>
        </div>
        <div>
          <label class="form-label" for="description">Description</label>
          <textarea id="description" v-model="form.description" rows="5" class="form-input" />
        </div>
      </section>

      <section class="card space-y-3 p-5">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold text-slate-900">Images</h2>
          <button type="button" class="link text-sm" @click="form.images.push({ url: '', alt: null })">Add image</button>
        </div>
        <p class="text-sm text-slate-500">The first image is used on product cards.</p>
        <div v-for="(image, index) in form.images" :key="index" class="flex items-end gap-3">
          <div class="flex-1">
            <ImageUploadField :id="`image-${index}`" v-model="image.url" :label="`Image ${index + 1}`" />
          </div>
          <button type="button" class="btn btn-secondary px-3" :disabled="index === 0" aria-label="Move up" @click="move(index, -1)">↑</button>
          <button type="button" class="btn btn-secondary px-3" aria-label="Remove image" @click="form.images.splice(index, 1)">✕</button>
        </div>
        <span v-if="errors.images" class="form-error">{{ errors.images }}</span>
      </section>

      <section class="card space-y-3 p-5">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold text-slate-900">Options</h2>
          <button type="button" class="link text-sm" @click="form.options.push({ name: '', values: [] })">Add option</button>
        </div>
        <p class="text-sm text-slate-500">e.g. Size: XS, S, M, L. Customers must choose one value of each option.</p>
        <div v-for="(option, index) in form.options" :key="index" class="grid grid-cols-1 gap-3 sm:grid-cols-[10rem_1fr_auto]">
          <input v-model="option.name" class="form-input" placeholder="Size" :aria-label="`Option ${index + 1} name`" />
          <input
            :value="option.values.join(', ')"
            class="form-input"
            placeholder="XS, S, M, L, XL"
            :aria-label="`Option ${index + 1} values`"
            @change="option.values = splitValues(($event.target as HTMLInputElement).value)"
          />
          <button type="button" class="btn btn-secondary px-3" aria-label="Remove option" @click="form.options.splice(index, 1)">✕</button>
        </div>
        <span v-if="errors.options" class="form-error">{{ errors.options }}</span>
      </section>

      <section class="card space-y-3 p-5">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold text-slate-900">Specifications</h2>
          <button type="button" class="link text-sm" @click="form.specifications.push({ name: '', value: '' })">Add row</button>
        </div>
        <div v-for="(spec, index) in form.specifications" :key="index" class="grid grid-cols-1 gap-3 sm:grid-cols-[10rem_1fr_auto]">
          <input v-model="spec.name" class="form-input" placeholder="Material" :aria-label="`Specification ${index + 1} name`" />
          <input v-model="spec.value" class="form-input" placeholder="100% Cotton" :aria-label="`Specification ${index + 1} value`" />
          <button type="button" class="btn btn-secondary px-3" aria-label="Remove row" @click="form.specifications.splice(index, 1)">✕</button>
        </div>
      </section>
    </div>

    <div class="space-y-6">
      <section class="card space-y-4 p-5">
        <div>
          <label class="form-label" for="status">Status</label>
          <select id="status" v-model="form.status" class="form-input">
            <option value="active">Active (visible in shop)</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
          </select>
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-700">
          <input v-model="form.is_featured" type="checkbox" class="form-check" /> Featured on the home page
        </label>
      </section>

      <section class="card space-y-4 p-5">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="form-label" for="price">Price (₹) *</label>
            <input id="price" v-model.number="form.price" type="number" min="0" step="1" class="form-input" :class="{ 'is-invalid': errors.price }" required />
            <span v-if="errors.price" class="form-error">{{ errors.price }}</span>
          </div>
          <div>
            <label class="form-label" for="sale_price">Sale price (₹)</label>
            <input id="sale_price" v-model.number="salePrice" type="number" min="0" step="1" class="form-input" :class="{ 'is-invalid': errors.sale_price }" />
            <span v-if="errors.sale_price" class="form-error">{{ errors.sale_price }}</span>
          </div>
          <div>
            <label class="form-label" for="cost">Cost (₹)</label>
            <input id="cost" v-model.number="cost" type="number" min="0" step="1" class="form-input" />
          </div>
          <div>
            <label class="form-label" for="stock">Stock *</label>
            <input id="stock" v-model.number="form.stock" type="number" min="0" step="1" class="form-input" :class="{ 'is-invalid': errors.stock }" required />
            <span v-if="errors.stock" class="form-error">{{ errors.stock }}</span>
          </div>
        </div>
        <p v-if="margin !== null" class="text-xs text-slate-500">Margin: {{ margin }}%</p>
        <div>
          <label class="form-label" for="sku">SKU *</label>
          <input id="sku" v-model="form.sku" class="form-input" :class="{ 'is-invalid': errors.sku }" required />
          <span v-if="errors.sku" class="form-error">{{ errors.sku }}</span>
        </div>
      </section>

      <section class="card space-y-4 p-5">
        <div>
          <label class="form-label" for="category">Category *</label>
          <select id="category" v-model.number="form.category_id" class="form-input" :class="{ 'is-invalid': errors.category_id }" required>
            <option :value="null" disabled>Choose a category</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.parent ? `${category.parent.name} › ` : '' }}{{ category.name }}
            </option>
          </select>
          <span v-if="errors.category_id" class="form-error">{{ errors.category_id }}</span>
        </div>
        <div>
          <label class="form-label" for="brand">Brand</label>
          <select id="brand" v-model.number="form.brand_id" class="form-input">
            <option :value="null">No brand</option>
            <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
          </select>
        </div>
        <div>
          <label class="form-label" for="slug">URL slug</label>
          <input id="slug" v-model="form.slug" class="form-input" placeholder="Generated from the name" />
          <span v-if="errors.slug" class="form-error">{{ errors.slug }}</span>
        </div>
      </section>

      <div class="flex gap-3">
        <button type="submit" class="btn btn-primary flex-1" :disabled="isSaving">{{ isSaving ? 'Saving…' : 'Save product' }}</button>
        <button v-if="!isNew" type="button" class="btn btn-danger" @click="remove">Delete</button>
      </div>
    </div>
  </form>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toast-notification'
import PageHeader from '@/components/app/PageHeader.vue'
import ImageUploadField from '@/components/shop/ImageUploadField.vue'
import confirm from '@/utils/alert'
import { http } from '@/utils/http'
import { errorMessage, validationErrors } from '@/utils/money'
import type { Brand, Category, Product, ProductImage, ProductOption, ProductSpecification, ProductStatus } from '@/types/shop'

const props = defineProps<{ id?: string }>()
const isNew = computed(() => !props.id)
const router = useRouter()
const toast = useToast()

const categories = ref<Category[]>([])
const brands = ref<Brand[]>([])
const errors = ref<Record<string, string>>({})
const isSaving = ref(false)
// Edit forms stay hidden until the record loads, so typing isn't overwritten by the load
const isLoaded = ref(!props.id)
const salePrice = ref<number | ''>('')
const cost = ref<number | ''>('')

const form = reactive({
  name: '',
  slug: '' as string | null,
  sku: '',
  description: '' as string | null,
  price: 0,
  stock: 0,
  status: 'draft' as ProductStatus,
  is_featured: false,
  category_id: null as number | null,
  brand_id: null as number | null,
  images: [] as ProductImage[],
  options: [] as ProductOption[],
  specifications: [] as ProductSpecification[],
})

const margin = computed(() => {
  const price = salePrice.value !== '' ? salePrice.value : form.price
  return cost.value !== '' && price ? Math.round(((price - cost.value) / price) * 100) : null
})

const splitValues = (text: string) => [...new Set(text.split(',').map((v) => v.trim()).filter(Boolean))]

const move = (index: number, direction: number) => {
  const [image] = form.images.splice(index, 1)
  form.images.splice(index + direction, 0, image)
}

const payload = () => ({
  ...form,
  slug: form.slug || null,
  sale_price: salePrice.value === '' ? null : salePrice.value,
  cost: cost.value === '' ? null : cost.value,
  images: form.images.filter((image) => image.url),
  options: form.options.filter((option) => option.name && option.values.length),
  specifications: form.specifications.filter((spec) => spec.name && spec.value),
})

const save = async () => {
  isSaving.value = true
  errors.value = {}
  try {
    const { data } = isNew.value
      ? await http.post<{ data: Product }>('admin/products', payload())
      : await http.put<{ data: Product }>(`admin/products/${props.id}`, payload())
    toast.success('Product saved.')
    if (isNew.value) router.replace({ name: 'products.edit', params: { id: data.data.id } })
  } catch (error) {
    errors.value = validationErrors(error)
    // Collapse nested errors (options.0.values, images.1.url…) onto their section
    for (const key of Object.keys(errors.value)) {
      const section = key.split('.')[0]
      if (section !== key && !errors.value[section]) errors.value[section] = errors.value[key]
    }
    toast.error(errorMessage(error, 'Please check the highlighted fields.'))
  } finally {
    isSaving.value = false
  }
}

const remove = async () => {
  if (!(await confirm({ title: 'Delete this product?', text: 'Past orders keep their details. This cannot be undone.' }))) return
  await http.delete(`admin/products/${props.id}`)
  toast.success('Product deleted.')
  router.push({ name: 'products' })
}

onMounted(async () => {
  const [categoryResponse, brandResponse] = await Promise.all([
    http.get<{ data: Category[] }>('admin/categories'),
    http.get<{ data: Brand[] }>('admin/brands'),
  ])
  categories.value = categoryResponse.data.data
  brands.value = brandResponse.data.data

  if (props.id) {
    const { data } = await http.get<{ data: Product }>(`admin/products/${props.id}`)
    const product = data.data
    Object.assign(form, {
      name: product.name,
      slug: product.slug,
      sku: product.sku,
      description: product.description,
      price: product.price,
      stock: product.stock,
      status: product.status,
      is_featured: product.is_featured,
      category_id: product.category?.id ?? null,
      brand_id: product.brand?.id ?? null,
      images: product.images.map(({ url, alt }) => ({ url, alt })),
      options: product.options.map((option) => ({ ...option, values: [...option.values] })),
      specifications: product.specifications.map((spec) => ({ ...spec })),
    })
    salePrice.value = product.sale_price ?? ''
    cost.value = product.cost ?? ''
    isLoaded.value = true
  }
})
</script>
