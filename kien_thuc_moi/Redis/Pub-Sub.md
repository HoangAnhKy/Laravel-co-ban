# Các Lệnh Redis Pub/Sub

- **`PUBLISH`**: Gửi (phát) thông điệp tới một kênh.

    ```php
    use Illuminate\Support\Facades\Redis;

    Redis::publish($channel, $message)
    // Gửi thông báo tới kênh 'notifications'
    Redis::publish('notifications', 'Hello, World!');
    ```

- **`SUBSCRIBE`**: Đăng ký (lắng nghe) thông báo từ một hoặc nhiều kênh cụ thể.

    ```php
    use Illuminate\Support\Facades\Redis;

    Redis::subscribe($channels, $callback)

    // Đăng ký lắng nghe kênh 'notifications'
    Redis::subscribe(['notifications'], function ($message) {
        echo "Received message: " . $message;
    });

    // Đăng ký lắng nghe nhiều kênh 'updates' và 'news'
    Redis::subscribe(['updates', 'news'], function ($message) {
        echo "Received message: " . $message;
    });
    ```

- **`PSUBSCRIBE`**: Đăng ký với pattern để nhận thông báo từ các kênh phù hợp với `pattern` đó.

    ```php
    use Illuminate\Support\Facades\Redis;

    // Đăng ký lắng nghe tất cả các kênh có tên bắt đầu bằng 'news:'
    Redis::psubscribe(['news:*'], function ($message, $channel) {
        echo "Received message from $channel: " . $message;
    });
    ```

- **`UNSUBSCRIBE`**: Ngừng đăng ký một hoặc nhiều kênh cụ thể.

    ```sh
    UNSUBSCRIBE notifications
    ```

- **`PUNSUBSCRIBE`**: Ngừng đăng ký với các kênh theo pattern.

    ```sh
    PUNSUBSCRIBE notifications
    ```

# Demo

B1: vào cmd chạy lện

```sh
redis-cli

SUBSCRIBE tạo-kênh
# UNSUBSCRIBE tạo-kênh để đóng
```

B2: tạo command laravel

```sh
php artisan make:command RedisSubscriber
```
- Dữ liệu trong file

```php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscriber extends Command
{
    protected $signature = 'app:redis-subscriber'; // tên để chạy php artisan app:redis-subscriber
    protected $description = 'Subscribe to Redis channel notifications';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Redis::subscribe(['notifications'], function ($message) {
            $this->info("Received message: " . $message);
            // Bạn có thể xử lý thông báo ở đây, ví dụ: lưu vào cơ sở dữ liệu hoặc phát thông báo real-time
        });
    }
}
```

B3: chạy lệnh comnand vừa tạo để lắng nghe

```sh
php artisan app:redis-subscriber
```

B4:  Tạo route và test thử

```php

Route::get("test-pub", function (){
    Redis::publish('notifications', 'Hello, this is a test message!');
});

```