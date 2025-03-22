# Dùng CDN

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vue-Laravel Example</title>
</head>
<body>
    <div id="root">
        <h1 v-text="content"></h1>
    </div>

    <script src="https://unpkg.com/vue@3.3.4/dist/vue.global.js"></script>
    <script>
        const app = Vue.createApp({
            data() {
                return {
                    content: "Dữ liệu từ JavaScript"
                }
            },
        })

        app.mount('#root');
    </script>
</body>
</html>
```

# Chạy với vite

- cài vite vô dự án

  ```sh
  npm install @vitejs/plugin-vue
  ```

- thêm plugin vào `vite.config.js`

  ```js
  import { defineConfig } from "vite";
  import laravel from "laravel-vite-plugin";
  import vue from "@vitejs/plugin-vue";

  export default defineConfig({
    plugins: [
      laravel({
        input: ["resources/css/app.css", "resources/js/app.js"],
        refresh: true,
      }),
      vue(),
    ],
  });
  ```

- chỉnh file app

  ```js
  import "./bootstrap";

  import { createApp } from "vue";
  import App from "./components/App.vue";

  const app = createApp(App);
  app.mount("#app");
  ```

- thêm code cho `App.vue`

  ```vue
  <template>
    <h1>Hello</h1>
  </template>

  <script setup></script>
  ```

- chỉnh lại `layout`

  ```php
  <!DOCTYPE html>
  <html>
  <head>
      <meta charset="utf-8">
      <title>Laravel & Vue</title>
      @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body>
      <div id="app"></div>
  </body>
  </html>
  ```

- chạy serve

  ```sh
  php artisan serve
  npm run dev
  ```
