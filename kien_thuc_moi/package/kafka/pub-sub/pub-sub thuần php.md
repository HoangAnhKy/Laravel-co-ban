# Mô tả quả trình

![alt text](./image/pub-sub.png)

- `Kafka Producer` gửi message đến `Topic`
- `Kafka Broker` lưu trữ tất cả các message trong các `partition được định cấu hình topic cụ thể đó`, đảm bảo rằng các message được phân phối cân bằng giữa các partition. Ví dụ, Kafka sẽ lưu trữ một message trong partition đầu tiên và message thứ 2 trong partition thứ 2 nếu producer gửi hai message và có hai partition.
- `Kafka Consumer` `subscribes` một `topic` cụ thể.
- Sau khi Consumer subscribes vào một topic, Kafka cung cấp `offset` hiện tại của topic cho Consumer và `lưu nó trong Zookeeper`
- Consumer sẽ `liên tục gửi request` đến Kafka để pull về các message mới.
- Kafka sẽ `chuyển tiếp tin` nhắn đến `Consumer` ngay khi nhận được từ `Producer`.
- Consumer sẽ nhận được message và xử lý nó.
- Kafka Broker nhận được xác nhận về message Consumer xử lý.
- Kafka cập nhật giá trị offset hiện tại ngay khi nhận được xác nhận
- Quy trình này lặp lại cho đến khi consumer dừng việc subcribes lại.

# Sử dụng thư viện

Thư viện này không cần rdkafka, dễ chạy trên Windows và Linux.

```sh
composer require longlang/phpkafka
```

# Cấu hình Kafka trong Laravel

## env

```php
KAFKA_BROKERS=localhost:29092 # Cổng Kafka chạy trong Docker.
KAFKA_TOPIC=test-topic # Topic Kafka mà Producer & Consumer sử dụng.
KAFKA_GROUP=my-group # Nhóm Consumer sẽ lắng nghe tin nhắn.
```

## Tạo Command Producer

### lệnh

```sh
 php artisan make:command SendKafkaMessage
```

### file nội dung

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Longlang\PhpKafka\Producer\Producer;
use Longlang\PhpKafka\Producer\ProducerConfig;

class SendKafkaMessage extends Command
{
    protected $signature = 'kafka:send {message}';
    protected $description = 'Gửi tin nhắn tới Kafka';

    public function handle()
    {
        $message = $this->argument('message'); // Lấy tham số truyền vào từ dòng lệnh

        $config = new ProducerConfig();
        $config->setBootstrapServers(env('KAFKA_BROKERS')); // Địa chỉ Kafka (trong .env)
        $config->setAcks(-1); // Yêu cầu Kafka xác nhận tin nhắn đã được ghi nhận, có 3 trường hợp
        /*
        0: không cần xác nhận, nhanh hơn
        1: xác nhận từ leader(broker chịu trách nhiệm quản lý và ghi nhận dữ liệu)
        -1: Kafka chỉ phản hồi khi tất cả các broker replica đã ghi tin nhắn.
        */

        $producer = new Producer($config); // Tạo Producer
        $producer->send(env('KAFKA_TOPIC'), $message); // Gửi tin nhắn đến topic Kafka

        $this->info("Sent message: $message"); // Hiển thị log khi gửi thành công
    }
}
```

## Tạo Command Consumer

### lệnh

```sh
php artisan make:command ConsumeKafkaMessage
```

### file nội dung

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Longlang\PhpKafka\Consumer\Consumer;
use Longlang\PhpKafka\Consumer\ConsumerConfig;

class ConsumeKafkaMessage extends Command
{
    protected $signature = 'kafka:consume';
    protected $description = 'Nhận tin nhắn từ Kafka';

    public function handle()
    {
        $config = new ConsumerConfig();
        $config->setBootstrapServers(env('KAFKA_BROKERS')); // Kết nối đến Kafka
        $config->setGroupId(env('KAFKA_GROUP')); // Group ID để quản lý offset
        $config->setTopic(env('KAFKA_TOPIC')); // Lắng nghe topic từ Kafka
        $config->setAutoCommit(false); // Tắt auto-commit để kiểm soát message
        // false: Developer tự quyết định khi nào commit.
        // true: Kafka tự động lưu offset(vị trí tin nhắn cuối cùng đã đọc) sau mỗi lần đọc.

        $consumer = new Consumer($config); // Tạo Consumer

        while (true) { // Chạy vòng lặp vô hạn để luôn lắng nghe Kafka
            $message = $consumer->consume(); // Nhận tin nhắn từ Kafka
            if ($message) {
                $this->info("Received message: " . $message->getValue()); // In tin nhắn ra màn hình
                $consumer->ack($message); // xác nhận xử lý xog
            }
        }
    }
}

```

# Chạy

```sh
# Consumer sẽ bắt đầu lắng nghe tin nhắn.
php artisan kafka:consume
# Gửi tin nhắn từ Producer
php artisan kafka:send "Laravel + Kafka chạy OK!"
```
