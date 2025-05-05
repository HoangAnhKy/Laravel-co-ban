# Các thành phần chính 

`xadd` là để ghi dữ liệu vào Stream (Producer).
`xread` là để đọc dữ liệu từ Stream (Consumer). 

### [Demo xadd/xread](./kết%20nối%202%20source%20thông%20qua%20redis.md/#Dùng%20Redis%20Streams%20xadd/xread)


# xGroup
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
|---------------|--------------------------------------------------|
| `CREATE`      | Tạo một group mới trên stream.                   |
| `SETID`       | Đặt lại "vị trí đọc" của group (đổi start ID).   |
| `DESTROY`     | Xóa toàn bộ group khỏi stream. Không xóa stream. |
| `DELCONSUMER` | Xóa một consumer cụ thể trong group.             |

**Dùng kết hợp với XPENDING, XACK, XCLAIM để quản lý message tồn đọng.**

| Lệnh       | Công dụng chính                                       |
|------------|-------------------------------------------------------|
| `XPENDING` | Kiểm tra message nào bị treo, ai đang giữ             |
| `XACK`     | Báo cho Redis biết message đã xử lý xong              |
| `XCLAIM`   | Chuyển quyền xử lý từ consumer này sang consumer khác |


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