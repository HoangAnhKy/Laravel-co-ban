# Package

có thể dùng `rapide/laravel-queue-kafka` hoặc `glushkovds/laravel-kafka-queue`

```sh
composer require rapide/laravel-queue-kafka
```

```sh
composer require glushkovds/laravel-kafka-queue.
```

### So sánh `rapide/laravel-queue-kafka` vs `glushkovds/laravel-kafka-queue`

Cả hai package đều giúp Laravel sử dụng **Kafka làm Queue Driver**, thay thế cho Redis, Database hoặc SQS. Dưới đây là bảng so sánh chi tiết:

| Tiêu chí                            | `rapide/laravel-queue-kafka`  | `glushkovds/laravel-kafka-queue` |
| ----------------------------------- | ----------------------------- | -------------------------------- |
| **Cập nhật thường xuyên**           | ❌ Không (cuối 2022)          | ✅ Có (2024)                     |
| **Hỗ trợ Laravel mới (9,10,11...)** | ⚠️ Có thể lỗi với Laravel 10+ | ✅ Hoạt động tốt với Laravel 9+  |
| **Dễ sử dụng**                      | ✅ Dễ cấu hình                | ✅ Dễ cấu hình                   |
| **Hỗ trợ commit offset tự động**    | ❌ Không có sẵn               | ✅ Có sẵn                        |
| **Hỗ trợ Kafka Consumer Groups**    | ✅ Có                         | ✅ Có                            |
| **Độ phổ biến**                     | ✅ Nhiều hướng dẫn            | ❌ Ít tài liệu hơn               |
| **Hiệu suất**                       | ⚠️ Tốt nhưng có thể lỗi       | ✅ Ổn định hơn                   |

### Kết luận - Nên chọn cái nào?

**Dùng `glushkovds/laravel-kafka-queue`** nếu:

- muốn **sử dụng Kafka Queue lâu dài** với Laravel.
- cần **hỗ trợ tốt cho Laravel 9+**.
- muốn **commit offset tự động**, tránh xử lý trùng dữ liệu.

**Dùng `rapide/laravel-queue-kafka`** nếu:

- đang làm dự án với **Laravel 8 hoặc cũ hơn**.
- cần package **được thử nghiệm nhiều hơn**, dù không còn cập nhật.

## Gợi ý tốt nhất:

- Nếu đang dùng **Laravel 9+** → **Dùng `glushkovds/laravel-kafka-queue`**.
- Nếu đang làm việc với **Laravel 8 trở xuống** → **Dùng `rapide/laravel-queue-kafka`**.

## code demo laravel 11

### Luồng chạy của Job qua Kafka

- Producer (Laravel App) → Gửi Job vào Kafka. [chỗ thực thi](#đẩy-job-vào-queue)
- Kafka Broker → Lưu trữ message trong queue.
- Consumer (Laravel Queue Worker) → Lắng nghe queue, nhận job. [chỗ thực thi](#chạy-queue)
- Job Processor (ProcessOrderJob) → Xử lý logic của job. [chỗ thực thi](#tạo-job-mới-trong-laravel)

### Cài đặt

```sh
composer require glushkovds/laravel-kafka-queue
```

### Thêm cấu hình vô env

```env
KAFKA_BROKERS=localhost:9092
KAFKA_QUEUE=orders
KAFKA_CONSUMER_GROUP=laravel-group
KAFKA_AUTO_COMMIT=true
```

### Thêm kafka vào config/queue.php

```php
'connections' => [
     'kafka' => [
        'driver' => 'kafka', // Khai báo Kafka làm queue driver
        'broker_list' => env('KAFKA_BROKERS', 'localhost:9092'), // Địa chỉ Kafka broker, trong docker thì là kafka:9092
        'queue' => env('KAFKA_QUEUE', 'default'), // Tên queue trong Kafka
        'consumer_group' => env('KAFKA_CONSUMER_GROUP', 'laravel'), // Nhóm consumer xử lý message

        'group_name' => env('KAFKA_GROUP_NAME', 'laravel-group'), // Tên nhóm của consumer trong Kafka

        'retry_after' => 90, // Thời gian chờ trước khi thử lại job thất bại
        'auto_commit' => env('KAFKA_AUTO_COMMIT', true), // Tự động commit offset sau khi xử lý message

        // Thông tin xác thực Kafka (nếu có)
        'auth_login' => env('KAFKA_AUTH_LOGIN', null), // Username Kafka
        'auth_password' => env('KAFKA_AUTH_PASSWORD', null), // Password Kafka
        'auth_mechanism' => env('KAFKA_AUTH_MECHANISM', 'PLAIN'), // Phương thức auth (PLAIN, SCRAM-SHA-256, SCRAM-SHA-512)

        'producer_timeout' => env('KAFKA_PRODUCER_TIMEOUT', 10000), // Timeout khi producer gửi message (10 giây)
        'consumer_timeout' => env('KAFKA_CONSUMER_TIMEOUT', 12000), // Timeout khi consumer lắng nghe message (12 giây)
        'heartbeat' => env('KAFKA_HEARTBEAT', 3000), // Khoảng thời gian gửi heartbeat đến Kafka (3 giây)
    ],
],

```

### Tạo Job mới trong Laravel

```sh
php artisan make:job ProcessOrderJob
```

```php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        \Log::info("Đang xử lý đơn hàng: " . json_encode($this->order));

        // Xử lý đơn hàng tại đây (ví dụ: lưu vào database, gửi email...)
    }
}
```

### đẩy job vào queue

```php

Route::get("/queue-demo", function () {

    $orderData = ['order_id' => 123, 'customer' => 'John Doe', 'total' => 1000];

    \App\Jobs\ProcessOrderJob::dispatch($orderData)->onQueue('kafka');
    return response()->json(['message' => 'Message sent to Kafka']);
});

```

### chạy queue

```sh
php artisan queue:work --queue=kafka
```
