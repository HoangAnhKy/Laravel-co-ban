Kiểm tra cache ở redis

```sh
php artisan tinker
```

# Một số phương thức cơ bản:

+ `Cache::put()`          // Lưu trữ giá trị vào cache
+ `Cache::get()`          // Lấy giá trị từ cache
+ `Cache::remember()`     // Dùng cache remember để lưu và tự động lấy giá trị nếu chưa tồn tại
+ `Cache::rememberForever()`     // Dùng để lưu vĩnh viễn
+ `Cache::forget()`       // Xóa cache
+ `Cache::has()`          // Kiểm tra tồn tại

ví dụ
```php
// Lưu trữ giá trị vào cache
Cache::put('key', 'value', 600); // 600 là số giây dữ liệu được lưu

// Lấy giá trị từ cache
$value = Cache::get('key');

// Dùng cache remember để lưu và tự động lấy giá trị nếu chưa tồn tại
$value = Cache::remember('key', 600, function () {
    return DB::table('users')->get();
});
// Xóa cache
Cache::forget('key');
```