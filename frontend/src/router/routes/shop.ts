import type { RouteRecordRaw } from 'vue-router'
import { routes as createRoutes } from '@/utils/route'

/** Shop back office. Each page needs the matching ability (see api UserAbility). */
const shop: RouteRecordRaw[] = createRoutes({ template: 'app', restricted: true, guest: false }, [
  { path: '/products', name: 'products', component: () => import('@/views/products/Products.vue'), meta: { authorize: 'manage catalog' } },
  { path: '/products/new', name: 'products.create', component: () => import('@/views/products/ProductForm.vue'), meta: { authorize: 'manage catalog' } },
  { path: '/products/:id', name: 'products.edit', props: true, component: () => import('@/views/products/ProductForm.vue'), meta: { authorize: 'manage catalog' } },
  { path: '/categories', name: 'categories', component: () => import('@/views/catalog/Categories.vue'), meta: { authorize: 'manage catalog' } },
  { path: '/brands', name: 'brands', component: () => import('@/views/catalog/Brands.vue'), meta: { authorize: 'manage catalog' } },

  { path: '/orders', name: 'orders', component: () => import('@/views/orders/Orders.vue'), meta: { authorize: 'manage orders' } },
  { path: '/orders/:number', name: 'orders.details', props: true, component: () => import('@/views/orders/OrderDetails.vue'), meta: { authorize: 'manage orders' } },
  { path: '/coupons', name: 'coupons', component: () => import('@/views/coupons/Coupons.vue'), meta: { authorize: 'manage coupons' } },
  { path: '/reviews', name: 'reviews', component: () => import('@/views/reviews/Reviews.vue'), meta: { authorize: 'manage reviews' } },

  { path: '/banners', name: 'banners', component: () => import('@/views/content/Banners.vue'), meta: { authorize: 'manage content' } },
  { path: '/blog', name: 'blog', component: () => import('@/views/content/BlogPosts.vue'), meta: { authorize: 'manage content' } },
  { path: '/blog/new', name: 'blog.create', component: () => import('@/views/content/BlogPostForm.vue'), meta: { authorize: 'manage content' } },
  { path: '/blog/comments', name: 'blog.comments', component: () => import('@/views/content/BlogComments.vue'), meta: { authorize: 'manage content' } },
  { path: '/blog/:id(\\d+)', name: 'blog.edit', props: true, component: () => import('@/views/content/BlogPostForm.vue'), meta: { authorize: 'manage content' } },
  { path: '/inbox', name: 'inbox', component: () => import('@/views/inbox/Inbox.vue'), meta: { authorize: 'manage content' } },
])

export default shop
