import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import postcssNesting from 'postcss-nesting';

export default defineConfig({
  server: {
    host: '0.0.0.0',
  },
  plugins: [
    vue(),
    laravel({
      input: [
        'resources/assets/css/reset.scss',
        'resources/assets/css/admin/app.scss',
        'resources/assets/css/admin/content-table.scss',
        'resources/assets/css/auth/login.scss',
        'resources/js/admin/app.js'
      ],
      refresh: true
    }),
    laravel({
      input: [
        'resources/js/main/app.js'
      ],
      refresh: true
    }),
  ],
  css: {
    postcss: {
      plugins: [
        postcssNesting
      ],
    },
  },
});
