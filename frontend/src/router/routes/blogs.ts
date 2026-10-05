import type { RouteRecordRaw } from 'vue-router'
import { routes as createRoutes } from '@/utils/route'

// Must match App\Policies\BlogPolicy::EDITORS on the API.
const BLOG_EDITORS = ['super_admin', 'blogger']

const blogs: RouteRecordRaw[] = createRoutes({ template: 'app', restricted: true, guest: false }, [
  {
    path: '/blogs',
    name: 'blogs',
    component: () => import('@/views/blogs/Blogs.vue'),
  },
  {
    path: '/blogs/create',
    name: 'create-blog',
    component: () => import('@/views/blogs/BlogForm.vue'),
    meta: { roles: BLOG_EDITORS },
  },
  {
    path: '/blogs/:id/edit',
    name: 'edit-blog',
    component: () => import('@/views/blogs/BlogForm.vue'),
    meta: { roles: BLOG_EDITORS },
  },
])

export default blogs
