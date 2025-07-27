# cài đặt hệ thống, nếu cài rồi có thể  bỏ qua

```sh
sudo apt install wkhtmltopdf
```

# Cài thư viện trong source laravel

- Lệnh
    
    ```sh
    composer require barryvdh/laravel-snappy
    ```
- Nếu gặp laravel version < 9, mở file `config/app` khai báo thêm thư viện vô

    ```php
    'providers' => [
        Barryvdh\Snappy\ServiceProvider::class,
    ],

    'aliases' => [
        'SnappyPdf' => Barryvdh\Snappy\Facades\SnappyPdf::class,
    ],
    ```
- Tạo file config, mục đích chỉnh lại `path` cho `wkhtmltopdf`

    ```sh
    php artisan vendor:publish --provider="Barryvdh\Snappy\ServiceProvider"
    ```

# Demo

```php
use SnappyPdf;

$pdf = SnappyPdf::loadView('pdf.certificate', [
    'user' => $user,
    'course' => $course
]);

return $pdf->download('chung-chi.pdf');
```