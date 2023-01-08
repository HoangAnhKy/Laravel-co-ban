Laravel With ReactJS

B1: Cài đặt Laravel application

B2: Vào package.json xóa đi `laravel-vite-plugin` và `vite`

B3: Chạy câu lệnh `npm install vite laravel-vite-plugin --save-dev` đế cài lại bản mới nhất

B4: Sau đó chạy lệnh `npm i @vitejs/plugin-react --force` để cài đặt gói `@vitejs/plugin-react`

B5: Sau đó chạy lệnh `npm i react@latest react-dom@latest` để cài reactjs và react-dom bản mới nhất

kết quả sau khi thực hiện bước trên

```js
    "devDependencies": {
        "axios": "^1.1.2",
        "laravel-vite-plugin": "^0.7.3",
        "lodash": "^4.17.19",
        "postcss": "^8.1.14",
        "vite": "^4.0.4"
    },
    "dependencies": {
        "@vitejs/plugin-react": "^3.0.1",
        "react": "^18.2.0",
        "react-dom": "^18.2.0"
    }
```

Sau đó vào `vite.config.js`

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from 'react';

export default defineConfig({
    plugins: [
        react(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});

```

B6: Vào `resources/components` tạo file `App.jsx`

```js
import React from 'react';

const App = () => {
    return (
        <>
            <h1 className='color-red'>Laravel with reactjs</h1>
        </>
    );
};

export default App;
```

B7: Đổi tên `app.js` thành `app.jsx` rồi vào đó khai báo như sau

```js
import './bootstrap';

import React from 'react';
import ReactDOM from 'react-dom';

import App from './components/App';

if (document.getElementById('app')) {
    ReactDOM.render(<App />, document.getElementById('app'));
}
```

B8: qua bên view gọi ra và dùng

vd: views/welcome.blade.php

```php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    </head>
    <body>
       <div id="app"></div>
    </body>
</html>

```
