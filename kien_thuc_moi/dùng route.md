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
