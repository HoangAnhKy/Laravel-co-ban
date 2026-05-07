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

# Cách dùng

- Tạo view pdf

    ```php
    // /resources/views/invoice.blade.php
    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="utf-8">
        <style>
            body {
                font-family: 'DejaVu Sans', sans-serif;
                font-size: 14px;
            }

            .invoice-box {
                border: 1px solid #eee;
                padding: 30px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            }

            .header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
            }

            .table th,
            .table td {
                border-bottom: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }

            .total {
                text-align: right;
                margin-top: 20px;
                font-weight: bold;
                color: red;
            }
        </style>
    </head>

    <body>
        <div class="invoice-box">
            <div class="header">
                <h1>HÓA ĐƠN #{{ $order_id }}</h1>
                <p>Ngày: {{ date('d/m/Y') }}</p>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['qty'] }}</td>
                            <td>{{ number_format($item['price']) }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total">
                Tổng cộng: {{ number_format($total) }} VNĐ
            </div>
        </div>
    </body>

    </html>
    ```

- Tạo hàm để xử dụng, `demo tạo bằng function trên route`

```php
<?php

use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { // 1. Chuẩn bị dữ liệu mẫu
    $data = [
        'order_id' => 'INV-2024-001',
        'items' => [
            ['name' => 'iPhone 15 Pro Max', 'qty' => 1, 'price' => 32000000],
            ['name' => 'Ốp lưng MagSafe', 'qty' => 2, 'price' => 1500000],
            ['name' => 'Ốp lưng MagSafe', 'qty' => 2, 'price' => 1500000],
        ],
        'total' => 35000000
    ];

    // 2. Load View và cấu hình
    $pdf = SnappyPdf::loadView('invoice', $data)
        ->setOption('margin-top', '10mm')
        ->setOption('margin-bottom', '10mm')
        ->setPaper('a4');

    // 3. Trả về file PDF (xem trực tiếp hoặc tải về)
    // Dùng inline() để xem trên trình duyệt, download() để tải ngay
    return $pdf->inline('hoa-don-cua-ban.pdf');
});
```