# dùng route kiểu `Route::resource`

```php
Route::resource("/", IdeasController::class);  // Đăng ký resource route
Route::resource('posts', IdeasController::class)->only(['index', 'show']); // chỉ sử dụng
Route::resource('posts', IdeasController::class)->except(['destroy']); // trừ

// đổi tên
Route::resource('posts', PostController::class)->names([
    'index' => 'posts.list',
    'show' => 'posts.view',
]);

// tùy chỉnh url. Điều này sẽ thay đổi {article} thành {article} thay vì {articles} trong URI.
Route::resource('articles', ArticleController::class)->parameters([
    'articles' => 'article',
]);

// Dùng middleware
Route::resource('posts', PostController::class)->middleware('auth');
```

**chỉ dùng được cho controller có các action sau: index, create, store, show, edit, destroy, update** 


# bỏ qua csrf

```php
// bootstrap/app.php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
            'api/create-idea', // cho route vô
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

```

# Lấy CSRF 

### Khởi tạo sanctum


```sh
php artisan install:api
```

### sử dụng

Khi dùng nó sẽ tạo ra một chỗ để lấy `cookie` token

```txt
GET http://127.0.0.1:8000/sanctum/csrf-cookie
```

Cookie `XSRF-TOKEN` sẽ được gửi từ server và lưu vào trình duyệt hoặc Postman

### ví dụ dùng POST REACTJS

```js
import axios from 'axios';

// Bật gửi cookie trong các yêu cầu
axios.defaults.withCredentials = true;

// Gọi /sanctum/csrf-cookie trước khi login
axios.get('http://127.0.0.1:8000/sanctum/csrf-cookie')
    .then(() => {
        // Sau khi lấy CSRF cookie, gửi request POST
        axios.post('http://127.0.0.1:8000/api/login', {
            email: 'test@example.com',
            password: 'password',
        }).then(response => {
            console.log('Login success:', response.data);
        }).catch(error => {
            console.error('Login failed:', error.response.data);
        });
    });
```
### Lưu ý phải kiểm trả file config/corf.php

- nếu chưa public 

    ```sh
    php artisan config:publish cors
    ```

Ví dụ:
```php
return [
    'paths' => ['api/*', 'login', 'sanctum/csrf-cookie'], // Các endpoint áp dụng CORS
    'allowed_methods' => ['*'], // Cho phép tất cả phương thức HTTP (GET, POST, PUT, DELETE, ...)
    'allowed_origins' => ['http://localhost:3000'], // Domain của frontend (React/Vue)
    'allowed_origins_patterns' => [], // Không dùng pattern phức tạp
    'allowed_headers' => ['*'], // Cho phép tất cả header
    'exposed_headers' => [], // Không expose thêm header nào
    'max_age' => 0, // Không giới hạn thời gian cache của trình duyệt
    'supports_credentials' => true, // Bật để hỗ trợ cookie và CSRF
];
```