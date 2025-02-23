`Notification` trong Laravel là hệ thống giúp gửi thông báo đến người dùng qua nhiều kênh khác nhau như:

- [Email (Mail)](#ví-dụ-gửi-mail)
- SMS (Twilio)
- [Database (Lưu vào DB để hiển thị trên giao diện)](#database)
- [Broadcast (Realtime notification với Pusher/WebSocket)](#broadcast)

# Ví dụ gửi mail

**env**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=miecute0509@gmail.com
MAIL_PASSWORD=pass
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=kdevtest@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Modal User**
Đảm bảo phải có `Notifiable`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Notifications\Notifiable;

class Users extends Model implements AuthenticatableContract
{
    use Authenticatable, Notifiable;
}
```

**Khởi tạo và gửi thử nhanh**

- khởi tạo

  ```sh
  php artisan make:notification NotiLoginSuccsessMail
  ```

- chạy ở trong controller gửi tới `user login` với thông tin mặc định bên notifi vừa tạo. Có thể tùy chỉnh lại

  ```php
  public function login($id = null){
      try {
          if (isset($id)){
              $user = Users::query()->find($id);

              if (empty($user)) {
                  return redirect()->route("users.index")->with("fail", "Cannot login");
              }

              if (Auth::attempt(["email" => $user->email, "password" => "123"])){
                  request()->session()->regenerate();
                  $user->notify(new NotiLoginSuccsessMail());

                  return redirect()->route("users.index")->with("success", "Login success");
              }
          }
      }catch (\Exception $e){
          return redirect()->route("users.index")->with("fail", $e->getMessage());
      }
  }
  ```

- nếu ko dùng Modal

  ```php
  use Illuminate\Support\Facades\Notification;

  Notification::route('mail', 'user@example.com')
              ->notify(new LoginSuccessNotification());
  ```

# Database

- trong via thêm "Database" nó sẽ tự gửi vô database

- khởi tạo table trong db nếu chưa có

  ```sh
  php artisan notifications:table
  php artisan migrate
  ``

  ```

- thêm dữ `function` và nhớ thêm `database` ở via

  ```php
  public function toDatabase($notifiable)
  {
      return [
          'message' => 'Bạn đã đăng nhập thành công!',
          'ip_address' => request()->ip(),
          'time' => now()->toDateTimeString(),
      ];
  }
  ```

# Broadcast

Thêm via `Broadcast` và tham dữ liệu sau. Đọc thêm pusher

```php
 public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'Bạn có một thông báo mới!',
            'time' => now()->toDateTimeString(),
        ]);
    }
    public function broadcastOn()
    {
        return new PrivateChannel('private-App.Models.User.2'); // ✅ Phát trên kênh private
    }


    // 🔴 Đổi tên sự kiện giống `MyEvent`
    public function broadcastAs()
    {
        return 'my-event'; // ✅ Laravel sẽ phát với tên `my-event`
    }
```
