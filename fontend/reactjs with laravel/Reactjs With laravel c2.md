1: Khởi tạo `app Laravel`

2: Vào `recources` khởi tạo reactjs

3: Xóa `node_modules` trong reactjs và copy `dependencies` trong `packege của reacjs` vào `devDependencies` của `package của laravel`

4: Chạy lện `npm install`

5: Vào package.json xóa đi laravel-vite-plugin và vite. Chạy câu lệnh npm install vite laravel-vite-plugin --save-dev đế cài lại bản mới nhất

Các bước còn lại giống cách 1

chỉ cần viết lại vite như sau

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        react(),
        laravel({
            input: ['resources/css/app.css', 'react-app/src/index.js', 'public/js/app.js'],
            refresh: true,
        }),
    ],
});
```
