# Dùng publish/subscribe

### Nhược điểm 
- Nếu App B đang offline khi App A publish → message sẽ mất

- Không có retry, không lưu vào đâu cả

- Không đảm bảo App B xử lý thành công
### Bên source A
Tạo một hàm xử lý hoặc một route để test

```php
Route::get("demo", function(){
    $payload = [
        'event' => 'order.created',
        'data'  => [
            'order_id' => 987,
            'total'    => 150000,
        ],
    ];

    // Publish lên channel "events:order"
    Redis::publish('events:order', json_encode($payload));

    return response()->json(['status' => 'published']);
});
```

### Bên source B

Tạo một command

```php
<?php
// app/Console/Commands/SubscribeOrderEvents.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SubscribeOrderEvents extends Command
{
    protected $signature = 'redis:subscribe-order';
    protected $description = 'Subscribe to order events via Redis Pub/Sub';

    public function handle()
    {
        $this->info('Listening to Redis channel events:order ...');

        // Redis::subscribe sẽ block, và tự gọi callback khi có message
        Redis::connection()->subscribe(['events:order'], function ($message) {
            $payload = json_decode($message, true);

            $this->info("Received event: {$payload['event']}");
            $this->info("Order ID: {$payload['data']['order_id']}, Total: {$payload['data']['total']}");

            // TODO: gọi service, xử lý tiếp...
        });
    }
}
```

Sau đó chạy lệnh `php artisan redis:subscribe-order` để khởi động


# Dùng Redis Streams xadd/xread

`xadd` là để ghi dữ liệu vào Stream (Producer).
`xread` là để đọc dữ liệu từ Stream (Consumer). 

**Lưu ý: Nhớ tắt prefix của redis**

### Bên source A (source ghi)

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $streamName = 'demo_stream';

    // Dữ liệu sẽ được ghi vào stream
    $data = [
        'event' => $request->input('event', 'default_event'),
        'payload' => $request->input('payload', 'default_payload'),
        'timestamp' => now()->toDateTimeString(),
    ];

    // Thêm dữ liệu vào stream
    Redis::xadd($streamName, '*', $data);

    return response()->json([
        'status' => 'success',
        'message' => 'Data added to Redis stream',
        'data' => $data,
    ]);
});
```

### Bên source B

Tạo job lắng nghe và cấu hình

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ConsumerCommand extends Command
{
    protected $signature = 'stream:consume';
    protected $description = 'Consume messages from the Redis stream';

    public function handle()
    {
        $streamName = 'demo_stream';
        $lastId = '$'; 
        // Đọc từ đầu stream: 0
        // Đọc từ lúc join: $

        $this->info("Listening to stream: {$streamName}");

        while (true) {
            // Đọc dữ liệu từ stream
            $messages = Redis::xread([$streamName => $lastId], null, 0);

            if (!empty($messages)) {
                foreach ($messages[$streamName] as $id => $message) {
                    // Hiển thị message
                    $this->info("Message ID: $id");
                    $this->info("Message Content: " . json_encode($message));

                    // Cập nhật last ID để tránh đọc lại
                    $lastId = $id;

                    // Giả sử xử lý xong sẽ ghi lại vào một Stream khác
                    Redis::xadd('processed_stream', '*', [
                        'original_id' => $id,
                        'processed_at' => now()->toDateTimeString(),
                        'data' => json_encode($message),
                    ]);
                }
            }

            // Nghỉ một chút để tránh vòng lặp quá nhanh
            sleep(1);
        }
    }
}
```


# Dùng queue 

- Tạo `Job` với tên giống nhau ở cả A và B

    ```php
    <?php

    namespace App\Jobs;

    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Queue\Queueable;
    use Illuminate\Support\Facades\Log;

    class CALLP2 implements ShouldQueue
    {
        use Queueable;


        protected $data = null;

        /**
         * Create a new job instance.
         */
        public function __construct($data = [])
        {
            $this->data = $data;
        }

    }

    ```

- Bên souce kia chỉ cần gọi `dispatch` còn bên `source B` chạy `queue:work redis` là được


### Nhược điểm

- Không phân biệt ai là “người nên xử lý” nếu tất cả đều nghe chung 1 queue (default)

- Rò rỉ lỗi giữa service: ví dụ job của A → B nhưng bị C “bắt nhầm” → lỗi vì thiếu class

- Gây fail job không liên quan → khó debug, khó scale


### khắc phục bằng cách thêm cờ
App A dispatch job

```php
ProcessSomething::dispatch()->onQueue('to-b');
```
App B chạy

```sh
php artisan queue:work redis --queue=to-b
```