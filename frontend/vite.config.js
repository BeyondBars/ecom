import { fileURLToPath, URL } from 'url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// Log the resolved alias
console.log('Resolved alias for @:', fileURLToPath(new URL('./src', import.meta.url)))

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    host: true,
    port: 8080,
    fs: {
      strict: false,
    },
  },
})
