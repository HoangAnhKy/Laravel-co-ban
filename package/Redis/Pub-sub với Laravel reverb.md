# lệnh cài

cài cả reverb và cấu hình của nó

```sh
php artisan install:broadcasting
```

# tạo event

```sh
php artisan make:event MessageSent
```

```php

<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow as BroadcastingShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements BroadcastingShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('chat');
    }

    public function broadcastWith()
    {
        return ['message' => $this->message];
    }
}
```

# Thêm cấu hình vào Echo

```js
window.Echo.channel("chat").listen("MessageSent", (e) => {
  console.log(" Tin nhắn mới nhận được:", e.message);
});
```

chạy npm run build

# thêm vào route

```php
use App\Events\MessageSent;

Route::get('/send-message', function () {
broadcast(new MessageSent('Hello từ Laravel!'));
return 'Message Sent!';
});
```

# chạy source

```sh
php artisan queue:work redis
php artisan reverb:start
php artisan serve
```

# lưu ý

trong env

```env
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=redis
REVERB_SCALING_ENABLED=true
```

Mặc định nó chỉ chạy `event` và `FE` ko có chạy `redis` phải bật `REVERB_SCALING_ENABLED` trong env để nó chạy nữa
