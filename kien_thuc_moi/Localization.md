Trong Laravel, Localization (Đa ngôn ngữ) cho phép dễ dàng xây dựng các ứng dụng hỗ trợ nhiều ngôn ngữ bằng cách sử dụng hệ thống dịch có sẵn của Laravel. Có thể định nghĩa các tệp dịch cho từng ngôn ngữ và sử dụng chúng trong ứng dụng.

# Lệnh tạo lang để chỉnh sửa

```sh
php artisan lang:publish
```

# Chuyển đổi ngôn ngữ mặc định.

Thường ngôn ngữ mặc định sẽ nằm trong folder `config/app` chỗ `locale`. Nó sẽ lấy biến mặc định ở file `.env`

# Cú pháp

sử dụng hàm `__()` hoặc `trans()` của Laravel để lấy bản dịch từ tệp ngôn ngữ. 

```php
// Sử dụng trans()
echo trans('messages.unknown'); // null (nếu không có 'messages.unknown' trong tệp dịch)

// Sử dụng __()
echo __('messages.unknown'); // 'messages.unknown' (trả về khóa nếu không có bản dịch)
```

# Chuyển đổi ngôn ngữ trong runtime

Dùng đoạn code sau để chuyển đổi ngôn ngữ nhanh chóng. Lưu trong `session`

```php
Route::get('language/{locale}', function ($locale) {
    session(['locale' => $locale]);
    return redirect()->back();
});

// Middleware để tự động đặt ngôn ngữ
public function handle($request, Closure $next)
{
    if (session()->has('locale')) {
        App::setLocale(session('locale'));
    }

    return $next($request);
}

```

# Thiết lập đoạn message

- Mặc định

    ```php
    // resources/lang/en/auth.php
    return [
        'failed' => 'These credentials do not match our records.',
        'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    ];
    ```

- Xét thêm biến

    ```php
    // resources/lang/en/messages.php
    return [
        'greeting' => 'Hello, :name',
    ];

    // Sử dụng:
    echo __('messages.greeting', ['name' => 'John']); // Output: Hello, John
    ```

- Xét theo số nhiều số it

    ```php
    // resources/lang/en/messages.php

    // lưu ý 
    /*
        {1}: Laravel sẽ sử dụng chuỗi này khi số lượng là 1 (số ít).
        
        [2,*]: Laravel sẽ sử dụng chuỗi này khi số lượng là từ 2 trở lên (số nhiều). Dấu * đại diện cho tất cả các số lớn hơn hoặc bằng 2.
    */
    return [
        'apples' => '{0} There are no apples|{1} There is one apple|[2,*] There are :count apples',
    ];
    // sử dụng
    echo trans_choice('messages.apples', 1); // Output: "There is one apple"
    echo trans_choice('messages.apples', 2, ['count' => 5]); // Output: "There are 5 apples"

    ```