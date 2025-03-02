[docs](https://laravelkafka.com/docs/v2.0/introduction)

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
        // Gửi thông điệp đến Kafka
        Kafka::publish()
            ->onTopic('orders')
            ->withBodyKey('order_id', $event->orderId)
            ->withBodyKey('product', $event->product)
            ->withBodyKey('quantity', $event->quantity)
            ->send();
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

class ConsumeKafkaOrders extends Command
{
    protected $signature = 'kafka:consume-orders';
    protected $description = 'Lắng nghe và xử lý các đơn hàng từ Kafka';

    public function handle()
    {
        $consumer = Kafka::createConsumer()
            ->subscribe('orders')
            ->withHandler(function ($message) {
                $order = $message->getBody();

                // Xử lý đơn hàng
                $this->info('Đã nhận đơn hàng: ' . json_encode($order));

                // Ví dụ: Cập nhật kho hàng
                // Inventory::updateStock($order['product'], $order['quantity']);
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
