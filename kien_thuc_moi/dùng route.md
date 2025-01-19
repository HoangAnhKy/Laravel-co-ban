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