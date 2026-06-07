# Các thành phần chính 

`xadd` là để ghi dữ liệu vào Stream (Producer).
`xread` là để đọc dữ liệu từ Stream (Consumer). 

### [Demo xadd/xread](./kết%20nối%202%20source%20thông%20qua%20redis.md/#Dùng%20Redis%20Streams%20xadd/xread)


# xGroup


```less
                ┌────────────┐
                │  Producer  │
                └─────┬──────┘
                      ▼
                ┌────────────┐
                │  Stream    │ (vd: orders)
                └─────┬──────┘
                      ▼
              ┌────────────────────┐
              │  Consumer Group    │ (vd: order_group)
              └───────┬────────────┘
        ┌─────────────┴───────────────┐
        ▼                             ▼
  Consumer A                     Consumer B
 (worker1)                          (worker2)
```

trong Redis được dùng để quản lý Consumer Groups cho Redis Streams. Đây là cơ chế giúp nhiều worker (hoặc consumer) chia nhau xử lý dữ liệu trong một stream mà không bị trùng lặp — rất giống với một hàng đợi nhưng hỗ trợ nhiều người tiêu dùng làm việc song song.

```php
/* 
Redis::xGroup(
    subcommand, 
    tên stream, 
    tên nhóm consumer,  
    ID bắt đầu đọc,  
    tự tạo stream nếu chưa tồn tại
);
*/
Redis::xGroup('CREATE', 'orders', 'orders_group', '0-0', true); // khởi tạo
Redis::xReadGroup($group, $consumer, [$stream => $id], $count = null, $block = null); // đọc
/*
$messages = Redis::xReadGroup(
    'order_group',       // Group
    'worker1',           // Consumer name
    ['orders' => '>'],   // >: đọc mới, 0: đọc từ đầu
    10,                  // Read tối đa 10 message
    5000                 // Block 5 giây nếu chưa có message
);
*/

```

**subcommand**

| Tham số       | Chức năng                                        |
| ------------- | ------------------------------------------------ |
| `CREATE`      | Tạo một group mới trên stream.                   |
| `SETID`       | Đặt lại "vị trí đọc" của group (đổi start ID).   |
| `DESTROY`     | Xóa toàn bộ group khỏi stream. Không xóa stream. |
| `DELCONSUMER` | Xóa một consumer cụ thể trong group.             |

**Dùng kết hợp với XPENDING, XACK, XCLAIM để quản lý message tồn đọng.**

| Lệnh         | Công dụng chính                                                                                          |
| ------------ | -------------------------------------------------------------------------------------------------------- |
| `XPENDING`   | Kiểm tra message nào bị treo, ai đang giữ                                                                |
| `XACK`       | Báo cho Redis biết message đã xử lý xong                                                                 |
| `XCLAIM`     | Chuyển quyền xử lý từ consumer này sang consumer khác                                                    |
| `XAUTOCLAIM` | Tự động cứu hộ: Quét và giật lại tin nhắn bị kẹt chỉ với 1 dòng lệnh (Thay thế combo XPENDING + XCLAIM). |

**Dùng để gửi dữ liệu lên group**

## xAdd

```php
// Gửi full group
Redis::xAdd('orders', '*', ['order_id' => $id,'status' => 'pending']);
// Gửi riêng
Redis::xAdd('orders:lazy_boy', '*', ['data' => 'Tin này chỉ dành cho ông Lười']);
```

## Hàm chạy trực tiếp

Vì lý do là Redis không có kiểu hàm cho nó hoặc một số hàm cũ không còn nên phải dùng tới 
`executeRaw`

```php
Redis::executeRaw([
    'XAUTOCLAIM', // Tên lệnh
    'orders',      // Stream
    'order_group', // Group
    'hero_worker', // Consumer
    '30000',       // Thời gian kẹt (ms)
    '0-0'          // Điểm bắt đầu quét
]);
```

***

## Ví dụ xGroup/xReadGroup

###  Tạo stream + consumer group (xGroup)
```php
<?php
namespace app\Console\Commands;
// app/Console/Commands/CreateStreamGroup.php
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CreateStreamGroup extends Command
{
    protected $signature = 'stream:create-group';
    protected $description = 'Tạo Redis stream và consumer group';

    public function handle()
    {
        $stream = 'orders';
        $group = 'order_group';

        try {
            // Tham số cuối là 'true' tương đương với MKSTREAM (tự tạo stream nếu chưa có)
            
            // Tham số kế cuối
            // Dùng 0 (hoặc 0-0): quét lại từ đầu chí cuối cuốn sổ. Có bao nhiêu đơn hàng cũ chưa xử lý, nó sẽ "hốt" sạch về làm lại từ đầu.
            // Dùng $: bỏ qua tất cả các tin nhắn đã có trong quá khứ. Nó chỉ bắt đầu "ngóng" và xử lý những tin nhắn nào được bắn vào sau khi lệnh này chạy xong.
            Redis::xGroup('CREATE', $stream, $group, '0-0', true);
            $this->info("Group '{$group}' created on stream '{$stream}'.");
        } catch (\Exception $e) {
            $this->error("Group may already exist: " . $e->getMessage());
        }
    }
}
```
Lệnh tạo
```sh
php artisan stream:create-group
```

### Lưu vào trong steam vừa tạo

```php
use Illuminate\Support\Facades\Redis;

Route::get('/send-order', function () {
    Redis::xAdd('orders', '*', [
        'order_id' => rand(1000, 9999),
        'user_id' => rand(1, 10),
        'status' => 'pending'
    ]);

    return 'Order pushed to stream!';
});
```

### Dùng readGroup để đọc bên source khác hoặc chỗ khác

```php
// app/Console/Commands/ConsumeOrders.php
namespace app\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ConsumeOrders extends Command
{
    protected $signature = 'stream:consume-orders {consumer}';
    protected $description = 'Consumer Redis stream via xReadGroup';

    public function handle()
    {
        $stream = 'orders';
        $group = 'order_group';
        $consumer = $this->argument('consumer');

        $this->info("Consumer '{$consumer}' is listening...");

        while (true) {
            $messages = Redis::xReadGroup($group, $consumer, [
                $stream => '>'
            ], 10, 5000); // max 10 messages, block 5s

            if (!$messages) {
                $this->info("No new messages");
                continue;
            }

            foreach ($messages[$stream] ?? [] as $id => $message) {
                $this->info("[$consumer] Handling message ID $id: " . json_encode($message));

                // ✅ xử lý message...

                Redis::xAck($stream, $group, [$id]); // Đánh dấu đã xử lý
            }
        }
    }
}
```

Lệnh tạo
```sh
php artisan stream:consume-orders consumer1
php artisan stream:consume-orders consumer2
```

# Lấy danh sách

```php
Route::get('/send-order', function () {
    $messages = Redis::XREVRANGE('orders', '+', '-', 10); // lấy 10 thằng cuối
    $messages = Redis::xRange('orders', '-', '+', 10); // lấy 10 thằng đầu

    return response()->json($messages);

    foreach ($messages as $id => $fields) {
        echo "ID tin nhắn: " . $id;
        echo PHP_EOL;
        echo "Khách hàng: " . $fields['user_id'];
        echo "<br />";
    }
});
```

# Demo trường hợp bị treo

Khi dùng Consumer Group, Redis có một cái "túi riêng" cho mỗi Worker gọi là PEL (Pending Entries List).
- Lazy Worker thò tay vào Stream lấy đơn hàng #1001.
- Redis ghi chú: "Đơn #1001 đang nằm trong túi của Lazy Worker".
- Lazy Worker bị sập (mất điện, lỗi code) mà chưa kịp gọi XACK.
- Hậu quả: Đơn #1001 bị kẹt trong cái túi đó mãi mãi. Những Worker khác không thấy đơn này vì nó không còn là "tin nhắn mới" (>) nữa.


Giải pháp là tạo ra một Worker đặc biệt (hoặc chính là các Worker bình thường nhưng có thêm "mắt thần") dùng lệnh `XAUTOCLAIM`

Lệnh này hoạt động giống như một Máy quét Radar:
- Nó không quét những tin nhắn mới.
- Nó quét tất cả cái túi (PEL) của cái Group đó.
- Nó tìm những tin nhắn nào đã nằm im (idle) quá lâu mà không có ai động vào.

<span style="color:red">Lưu ý:</span> 
Khi một message đã được một consumer đọc, nó sẽ nằm trong PEL và thuộc về consumer đó.

Các consumer khác:
- ❌ Không thể đọc message này bằng `xReadGroup`
- ✅ Nhưng vẫn có thể "giành lại" để xử lý bằng `XCLAIM` hoặc `XAUTOCLAIM`

## khởi tạo group và tạo comsumer lazy (lắng nghe 1 lần và không xAck)

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSetUp extends Command
{
    protected $signature = 'redis:setup';
    protected $description = 'Command description';
    public function handle()
    {
        try {
            Redis::xGroup('CREATE', 'orders', 'order_group', '0', true);
            $this->info("✅ Đã tạo Stream 'orders' và Group 'order_group'!");
        } catch (\Exception $e) {
            $this->warn("ℹ️ Group đã tồn tại hoặc có lỗi: " . $e->getMessage());
        }
    }
}
```

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisLazyWorker extends Command
{
    protected $signature = 'redis:lazy';
    public function handle()
    {
        $this->warn("🚧 Worker Lười Biếng đang rình mò...");

        while (true) {
            // Đọc tin mới ('>')
            $data = Redis::xReadGroup('order_group', 'lazy_boy', ['orders' => '>'], 1, 2000);
            if ($data) {
                foreach ($data['orders'] as $id => $fields) {
                    $this->error("📥 Nhận đơn #{$fields['order_id']} nhưng tôi... ĐI NGỦ ĐÂY! (Không ACK)");
                    return; // Thoát luôn, tin nhắn rơi vào PEL
                }
            }
        }
    }
}
```

## Khởi tạo Route gửi product vào steam

```php
Route::get("/product/{id}", function ($id) {
    Redis::xAdd('orders', '*', [
        'order_id' => $id,
        'customer' => 'Kyha',
        'status' => 'pending'
    ]);
    $this->info("🚀 Đã bắn đơn hàng #$id vào Stream!");
});
```

## Tạo cái command cứu hộ

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisRescueWorker extends Command
{
    protected $signature = 'redis:rescue';

    public function handle()
    {
        $this->info("🦸 Worker Anh Hùng đang đi tuần tra...");
        while (true) {
            // XAUTOCLAIM <stream> <group> <consumer> <min-idle-time> <start-id> [COUNT count]
            // min-idle-time: 10000ms (10 giây). Nếu tin kẹt > 10s thì cứu.
            $res = Redis::executeRaw([
                'XAUTOCLAIM', 'orders', 'order_group', 'hero_worker', '10000', '0-0', 'COUNT', 1
            ]);
            // Cấu trúc trả về: [ID tiếp theo, [mảng các tin cứu được]]
            if (!empty($res[1])) {
                foreach ($res[1] as $entry) {
                    $id = $entry[0];
                    $fields = $entry[1];

                    $this->warn("⚡ CỨU HỘ: Đã giựt lại đơn #{$fields[1]} (ID: $id)");

                    // Xử lý lại và ACK
                    Redis::xAck('orders', 'order_group', [$id]);
                    $this->info("✅ Đã xử lý lại và ACK thành công!");
                }
            }
            usleep(500000); // Nghỉ 0.5s rồi tuần tra tiếp
        }
    }
}
```