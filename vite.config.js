import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    react(),
    tailwindcss(),
  ],
  server: {
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
      },
      '/contpaq-docs': {
        target: 'http://189.206.185.236:5088',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/contpaq-docs/, ''),
      },
      '/contpaq-mty': {
        target: 'http://189.206.185.236/api/Mty',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/contpaq-mty/, ''),
      },
    },
  },
})
