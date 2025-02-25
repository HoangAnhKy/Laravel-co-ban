# Cài đặt

- Cài redis và source trước

- Sau khi cài source nhớ vào `config/database` để xóa `prefix` của redis

### .env

- Sửa `env` dùng redis

  ```env
  BROADCAST_CONNECTION=redis
  QUEUE_CONNECTION=redis
  ```

### cài broadcasting

- Chỉ cài `broadcasting` không cài thêm gì

  ```sh
  php artisan install:broadcasting
  ```

- Trong `config/broadcasting.php` thêm `redis`

  ```php
  'redis' => [
              'driver' => 'redis',
          ],
  ```

### Laravel Echo

- Cài laravel-echo-server

  ```sh
  npm install -g laravel-echo-server@latest
  ```

- Khởi tạo Laravel echo serve

  ```sh
  laravel-echo-server init
  ```

- Đảm bảo `laravel-echo-server.json` luôn có giữ liệu cho `redis` và `socketio`, Broadcast driver phải là `Redis`
  ```json
  // laravel-echo-server.json
  {
    "authHost": "http://localhost",
    "authEndpoint": "/broadcasting/auth",
    "clients": [],
    "database": "redis",
    "databaseConfig": {
      "redis": {
        "host": "127.0.0.1",
        "port": "6379"
      },
      "sqlite": {
        "databasePath": "/database/laravel-echo-server.sqlite"
      }
    },
    "devMode": true,
    "host": null,
    "port": "6001",
    "protocol": "http",
    "socketio": {
      "cors": {
        "origin": "*",
        "methods": ["GET", "POST"],
        "allowedHeaders": [
          "Origin",
          "X-Requested-With",
          "Content-Type",
          "Accept",
          "Authorization"
        ]
      }
    },
    "secureOptions": 67108864,
    "sslCertPath": "",
    "sslKeyPath": "",
    "sslCertChainPath": "",
    "sslPassphrase": "",
    "subscribers": {
      "http": true,
      "redis": true
    },
    "apiOriginAllow": {
      "allowCors": false,
      "allowOrigin": "",
      "allowMethods": "",
      "allowHeaders": ""
    }
  }
  ```

### Cài đặt Laravel Echo và Socket.IO trong Frontend

```sh
npm install socket.io-client laravel-echo
```

### resources/js/bootstrap.js

khai báo khởi tạo

```js
import Echo from "laravel-echo";
import io from "socket.io-client";

window.io = io;

window.Echo = new Echo({
  broadcaster: "socket.io",
  host: "http://localhost:6001",
  transports: ["websocket", "polling"],
});
```

### resources/js/app.js

```js
window.Echo.channel("chat").listen("MessageSent", (e) => {
  console.log(" Tin nhắn mới nhận được:", e.message);
});

var socket = io("http://localhost:6001");

socket.on("connect", () => console.log("WebSocket Connected!"));
socket.on("connect_error", (err) => console.log("WebSocket Error:", err));
socket.on("disconnect", (reason) =>
  console.log("WebSocket Disconnected:", reason)
);
```

### Chạy cập nhật fontend

```sh
npm run build
```

### Tạo sự kiện Laravel và cập nhật

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

### Test phát sự kiện

```php
// routes/web.php

use App\Events\MessageSent;

Route::get('/send-message', function () {
    broadcast(new MessageSent('Hello từ Laravel!'));
    return 'Message Sent!';
});
```
