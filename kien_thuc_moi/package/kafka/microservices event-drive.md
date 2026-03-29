[docs](https://laravelkafka.com/docs/v2.0/introduction)

[tip tham khảo](./tips.md)
### cài thư viện

```sh
composer require mateusjunges/laravel-kafka
```

### Show file config kafka

```sh
php artisan vendor:publish --tag=laravel-kafka-config
```

### Tạo sự kiện order

```sh
php artisan make:event OrderCreated
```

```php
<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, SerializesModels;

    public $orderId;
    public $product;
    public $quantity;

    public function __construct($orderId, $product, $quantity)
    {
        $this->orderId = $orderId;
        $this->product = $product;
        $this->quantity = $quantity;
    }
}
```

### khởi tạo listener

```sh
php artisan make:listener SendOrderToKafka --event=OrderCreated
```

```php
<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Junges\Kafka\Facades\Kafka;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderToKafka implements ShouldQueue
{
    /**
     * Xử lý sự kiện.
     *
     * @param  \App\Events\OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        // Gửi thông điệp đến Kafka, nó sẽ là kiểu phân tán balance ra cá partition nếu có từ 2 cái trở đi
        Kafka::publish()
            ->onTopic('orders')
            ->withKafkaKey((string) $event->orderId) // set thêm key cho message
            ->withBodyKey('order_id', $event->orderId)
            ->withBodyKey('product', $event->product)
            ->withBodyKey('quantity', $event->quantity)
            ->send();
        /*
        // set cố định gửi vào 1 partition nào đó

        // 1. Tạo nội dung tin nhắn
        $message = new Junges\Kafka\Message\Message(
            body: [
                'order_id' => $event->orderId,
                'product' => $event->product,
                'quantity' => $event->quantity,
            ],
            partition: 1 // set chỉ gửi cố định cho partition 1
        );
        // 2.  Gửi đi
        Kafka::publish()
            ->onTopic('orders')
            ->withMessage($message)
            ->send();

        */
    }
}
```

### sử dụng orderCreated

```php
Route::get('/cr-order', function () {
    $orderId = 1; // Ví dụ
    $product = 'Sản phẩm A';
    $quantity = 2;

    // Phát sự kiện OrderCreated
    event(new \App\Events\OrderCreated($orderId, $product, $quantity));

    return response()->json(['message' => 'Đơn hàng đã được tạo thành công!']);
});
```

### Tạo Consumer trong Inventory Service

```sh
php artisan make:command ConsumeKafkaOrders
```

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Contracts\MessageConsumer;
use Junges\Kafka\Contracts\ConsumerMessage;

class ConsumeKafkaOrders extends Command
{
    protected $signature = 'kafka:consume-orders';
    protected $description = 'Lắng nghe và xử lý các đơn hàng từ Kafka';

    public function handle()
    {
        // thay đổi mới do docs được cập nhật
        $consumer = Kafka::consumer(['orders'])
            ->withConsumerGroupId('inventory-service-group') // dùng đẻ nhớ đang đọc tới đâu
            ->withAutoCommit() // tự động commit xác nhận đã đọc xong
            ->withHandler(function (ConsumerMessage $message, MessageConsumer $consumer) {
                $order = $message->getBody();

                $this->info('Đã nhận đơn hàng2: ' . json_encode($order));

                // Logic xử lý...
                
                // commit bằng tay 
                // $consumer->commit();
            })
            ->build();

        $consumer->consume();
    }
}
```

### chạy ứng dụng

```sh
php artisan kafka:consume-orders
```
