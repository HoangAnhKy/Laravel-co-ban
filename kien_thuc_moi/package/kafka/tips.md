# xử  lý tình trạng lỗi trên kafka

- Dùng để  xử lý lỗi khi không gửi tin nhắn lên kafka được. Tránh tình trạng bị treo

- Khi nào muốn chạy lại thì gử i bằng tay lại hoặc dùng code để tạo và gửi lại, cẩn thận nó bị loop vô tận khi phục hồi

## flow

```txt
main-topic
   ↓
consumer xử lý
   ↓ fail
retry-topic (delay)
   ↓
consumer đọc lại
   ↓ fail tiếp
DLQ
```


## code

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Junges\Kafka\Contracts\ConsumerMessage;
use Junges\Kafka\Facades\Kafka;

class ConsumeKafkaOrders extends Command
{
    protected $signature = 'kafka:consume-orders';
    protected $description = 'Lắng nghe và xử lý các đơn hàng từ Kafka';

    public function handle()
    {
        $consumer = Kafka::consumer(['orders'])
            ->withConsumerGroupId('inventory-service-group')
            ->withAutoCommit()
            // Demo DLQ
            ->withHandler(function (ConsumerMessage $message) {
                $data = $message->getBody();
                $header = $message->getHeaders(); // Là một mảng key–value (associative array) chứa các header của message

                // Lấy số lần đã thử từ Headers (mặc định là 0 nếu chưa có)
                $retryCount = $header['x-retry-count'] ?? 0;
                // Dùng flag riêng để biết hàng này từ Redrive tới
                $isRedrived = $headers['x-is-redrived'] ?? false;

                try {
                    $this->info("Đang xử lý đơn hàng ID: " . ($data['order_id'] ?? 'N/A'));
                    // GIẢ LẬP LỖI
                    if ($retryCount <= 3 && !$isRedrived) {
                        throw new \Exception("Kết nối Database thất bại!");
                    }
                    $this->info("✅ Xử lý thành công!");
                } catch (\Exception $e) {
                    $this->error("❌ Lỗi: " . $e->getMessage());
                    if ($retryCount < 3) {
                        // CÁCH 1: RETRY - Bắn lại vào chính topic đó để thử lại
                        $newCount = $retryCount + 1;
                        $this->warn("⚠️ Thử lại lần $newCount...");
                        Kafka::publish()
                            ->onTopic('orders')
                            ->withBody($data)
                            ->withHeaders([
                                'x-retry-count' => $newCount,
                                'x-is-redrived' => $isRedrived // Giữ nguyên trạng thái redrive
                                ])
                            ->send();
                    } else {
                        // HẾT LƯỢT:
                        if ($isRedrived) {
                            $this->error("💀 Hàng hồi sinh vẫn lỗi. Ngắt loop để tránh 'nạp 1 nùi'!");
                            return; 
                        }
                        // CÁCH 2: DLQ - Sau 3 lần vẫn lỗi thì đưa vào "nghĩa địa"
                        $this->error("💀 Đã thử 3 lần nhưng vẫn thất bại. Chuyển vào orders-failed");
                        Kafka::publish()->onTopic('orders-failed')
                            ->withBody($data)
                            ->withHeaders([
                                'x-error-message' => $e->getMessage(),
                                'x-failed-at' => now()->toDateTimeString()
                            ])->send();
                    }
                }
            })
            ->build();

        $consumer->consume();
    }
}

/* output demo

Đang xử lý đơn hàng ID: 1
❌ Lỗi: Kết nối Database thất bại!
⚠️ Thử lại lần 1...
Đang xử lý đơn hàng ID: 1
❌ Lỗi: Kết nối Database thất bại!
⚠️ Thử lại lần 2...
Đang xử lý đơn hàng ID: 1
❌ Lỗi: Kết nối Database thất bại!
⚠️ Thử lại lần 3...
Đang xử lý đơn hàng ID: 1
❌ Lỗi: Kết nối Database thất bại!
💀 Đã thử 3 lần nhưng vẫn thất bại. Chuyển vào orders-failed
*/
```

## tạo 1 command hồi sinh message

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Junges\Kafka\Contracts\ConsumerMessage;
use Junges\Kafka\Facades\Kafka;

class RedriveFailedOrders extends Command
{
    // Lệnh chạy: php artisan kafka:redrive
    protected $signature = 'kafka:redrive';
    protected $description = 'Bốc tin nhắn từ orders-failed ném ngược lại orders';

    public function handle()
    {
        $this->warn("⚡ Đang kết nối tới topic 'orders-failed'...");

        $consumer = Kafka::consumer(['orders-failed'])
            // ID này cực quan trọng: Kafka sẽ dùng nó để nhớ bạn đã hồi sinh đến tin nhắn nào
            ->withConsumerGroupId('dlq-redrive-manager-v1')
            /* Đảm bảo đọc từ tin nhắn cũ nhất trong DLQ
                - trường hợp nó muốn đọc tiếp các cái chưa đọc thì comment code dưới
                - đọc cái mới nhất thì dùng latest
            */
            ->withOption('auto.offset.reset', 'earliest')
            ->withAutoCommit()
            ->withHandler(function (ConsumerMessage $message) {
                $data = $message->getBody();
                $offset = $message->getOffset();

                $this->info("------------------------------------------");
                $this->info("📦 Đang hồi sinh Offset [#$offset] - Order ID: " . ($data['order_id'] ?? 'N/A'));

                try {
                    // Đẩy ngược lại Topic chính
                    Kafka::publish()
                        ->onTopic('orders')
                        ->withBody($data)
                        // RESET lại số lần lỗi về 0 để không bị văng lại DLQ ngay lập tức
                        ->withHeaders([
                            'x-retry-count' => 0,
                            'x-redrived-at' => now()->toDateTimeString(),
                            'x-is-redrived' => true, // Đánh dấu để Consumer chính nhận diện
                        ])
                        ->send();

                    $this->info("✅ Đã đẩy về topic 'orders' thành công!");
                } catch (\Exception $e) {
                    $this->error("❌ Lỗi khi gửi lại: " . $e->getMessage());
                    // Nếu lỗi ở đây, Consumer sẽ không Commit, lần sau chạy lại nó sẽ thử lại đúng tin này.
                }
            })
            ->build();

        $this->comment("Đang trực chiến... Nhấn Ctrl+C để dừng khi đã hết tin nhắn.");
        $consumer->consume();
    }
}
```