# Khái niệm

Trong Laravel là cơ chế giúp phát (broadcast) các sự kiện (event) real-time đến các client, thường là trình duyệt (JavaScript client) hoặc các ứng dụng khác. Nhờ đó, người dùng có thể cập nhật dữ liệu ngay lập tức mà không cần tải lại trang, tạo ra trải nghiệm real-time tương tự các ứng dụng chat, thông báo live, hoặc cập nhật trạng thái theo thời gian thực.

# Cách hoạt động của Broadcasting

- `Event Broadcasting`: tạo một event Laravel như bình thường, sau đó đánh dấu event này có thể broadcast (thường thông qua `interface` `ShouldBroadcast` hoặc `ShouldBroadcastNow`). Khi phát (dispatch) event, Laravel sẽ broadcast sự kiện đó đến channel và dữ liệu được khai báo trong event.

  ```php
  use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

  class BroadcastingEvent implements ShouldBroadcast
  ```

- `Channels (Kênh)`: cho phép khai báo kênh `public` hoặc `private (có xác thực) trong file routes/channels.php`. Người dùng chỉ có thể lắng nghe (listen) channel private nếu họ vượt qua logic xác thực (authorization), đảm bảo tính bảo mật.

  ```php
     public function broadcastOn()
      {
          // return ['my-channel']; //public
          return new PrivateChannel('App.Models.User.' . $this->userID);
      }
  ```

- `Broadcast Driver`: hỗ trợ các driver như Pusher, Ably, Laravel WebSockets (package BeyondCode), hoặc Redis (dùng kèm socket.io hoặc tương tự). cần cấu hình trong file `.env` và `config/broadcasting.php` để kết nối với dịch vụ real-time tương ứng.

- `Frontend – Client`: dùng một thư viện như `Laravel Echo` để lắng nghe các event từ channel đã khai báo. Khi sự kiện được broadcast, client sẽ nhận ngay lập tức dữ liệu (ví dụ: chat message mới, thông báo mới, …) và cập nhật giao diện.

# Lệnh cài

```sh
php artisan install:broadcasting
# chọn no hết
```

file chỉnh sửa ở `config/broadcasting`
