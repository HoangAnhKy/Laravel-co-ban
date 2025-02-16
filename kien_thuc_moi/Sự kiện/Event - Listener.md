# Event

### khái niệm

Là thành phần trung tâm của kiến trúc lập trình dựa trên sự kiện (event-driven). Nó có nhiệm vụ:

- `Đại diện cho một hành động hay sự kiện xảy ra trong ứng dụng`: Ví dụ, khi người dùng đăng ký, có thể phát ra event `UserRegistered` chứa thông tin liên quan.
- `Truyền dữ liệu`: Event thường chứa các dữ liệu cần thiết để các listener có thể xử lý (như thông tin người dùng, dữ liệu liên quan, …).

- `Kích hoạt các listener`: Khi event được phát ra (dispatch), các listener đã đăng ký với event đó sẽ tự động được gọi để thực hiện các tác vụ tương ứng, như gửi email, ghi log, cập nhật dữ liệu, v.v.

### Lệnh tạo

```sh
php artisan make:event

// php artisan make:event PodcastProcessed
```

# Listener

### khái niệm

Được dùng để lắng nghe và xử lý `các sự kiện (events)` xảy ra trong ứng dụng. Khi một event được phát ra, các listener tương ứng sẽ nhận sự kiện đó và thực hiện các hành động cần thiết, như:

- Gửi email
- Ghi log
- Cập nhật cơ sở dữ liệu
- Hoặc bất kỳ thao tác nào khác

### lệnh tạo

```sh
php artisan make:listener name --event=nameEvent

// php artisan make:listener SendPodcastNotification --event=PodcastProcessed
```

# Sử dụng

- Gọi event để sử dụng
  ```php
  event((new writeLogUser($user))->handle());
  ```
- Thêm dữ liệu để truyền qua listener

  ```php
  <?php

  namespace App\Events;

  use App\Models\User;
  use Illuminate\Broadcasting\InteractsWithSockets;
  use Illuminate\Foundation\Events\Dispatchable;
  use Illuminate\Queue\SerializesModels;

  class writeLogUser
  {
      use Dispatchable, InteractsWithSockets, SerializesModels;


      public $user = null;
      /**
      * Create a new event instance.
      */
      public function __construct(User $user)
      {
          $this->user = $user;
      }
  }
  ```

- Khởi tạo listener và dùng

  - **Chú ý nếu muốn nó sử dụng nhiều listener thì khai báo thêm tên even ở chỗ handle**

    ```php
    <?php

    namespace App\Listeners;

    use App\Events\writeLogUser;

    class writeLogListener
    {
        /**
         * Create the event listener.
         */
        public function __construct()
        {
            //
        }

        /**
         * Handle the event.
         */
        public function handle(writeLogUser $event): void // chỗ này nè
        {
            echo ("test fc handle ");
            dd($event->user->full_name);
        }
    }

    ```

  - **Cũng có thể khái báo trong `boot` của `AppServiceProvider`**

    ```php
    public function boot(): void
    {
        Event::listen(
            writeLogUser::class,
            OrderWriteLog::class,
        );
        Event::listen(
            writeLogUser::class,
            writeLogListener::class,
        );
    }
    ```
