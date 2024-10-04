Queues (hàng đợi) trong Laravel cho phép xử lý các tác vụ nặng một cách bất đồng bộ, giúp cải thiện hiệu suất và trải nghiệm người dùng.

Laravel hỗ trợ nhiều driver cho queue như database, redis, sqs, beanstalkd, v.v. 


ShouldQueue là một interface được sử dụng để chỉ định rằng một job, event, hoặc mailable nên được xử lý thông qua hệ thống queue thay vì được thực thi ngay lập tức

# Tạo Queue bằng DATABASE

Cấu hình driver trong .env: `QUEUE_CONNECTION=database`

1) Lệnh khởi tạo nơi chứa các jobs

```sh
php artisan queue:table
php artisan migrate
```

2) khởi tạo jobs

```sh
php artisan make:job SendWelcomeEmail
```

sau khi khởi tạo file sẽ được tạo ở `app/Jobs/` vô đó chỉnh sửa logic mình cần.

```php
<?php

namespace App\Jobs;

use App\Mail\WelcomeMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Tạo một instance của job.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Thực hiện job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)->send(new WelcomeMail($this->user));
    }
}

```

3) Dispatch Job

- có thể dispatch job từ bất kỳ đâu trong ứng dụng, ví dụ như từ controller:

```php
<?php

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Tạo người dùng
        $user = User::create($request->all());

        // Dispatch job gửi email chào mừng
        SendWelcomeEmail::dispatch($user);

        return response()->json(['message' => 'User created successfully!']);
    }
}

```

4) Chạy Queue Worker

- Để xử lý các job trong queue, cần chạy queue worker
```sh
php artisan queue:work
```

5)  Xử Lý Các Job Thất Bại

Laravel tự động ghi nhận các job thất bại vào bảng failed_jobs. Để cấu hình, hãy đảm bảo đã chạy migration:

```sh
php artisan queue:failed-table
php artisan migrate
```

### một số lệnh check

```sh
php artisan queue:failed #Xem Các Job Thất Bại
php artisan queue:retry all # Retry Các Job Thất Bại
php artisan queue:flush #Xóa Các Job Thất Bại

```
# Tùy Chỉnh Queue

- Phân loại các job vào các queue khác nhau để ưu tiên xử lý:
    ```php
    SendWelcomeEmail::dispatch($user)->onQueue('emails');
    ```
    Chạy worker cho queue emails:
    ```sh
    php artisan queue:work --queue=emails
    ```

- Chạy Các Queue Theo Priority

    Có thể xác định thứ tự ưu tiên khi chạy worker:

    ```sh
    php artisan queue:work --queue=high,default,low
    ```
    Trong ví dụ trên, worker sẽ xử lý các job trong queue high trước, sau đó là default, và cuối cùng là low.

# Hàm cơ bản

- `dispatch()` được sử dụng để gửi một job vào queue.
    ```sh
    SendWelcomeEmail::dispatch($user);
    ```

- `dispatchNow()` thực thi job ngay lập tức mà không đưa vào queue.

    ```sh
    SendWelcomeEmail::dispatchNow($user);
    ```

- `onQueue()` cho phép chỉ định queue cụ thể để đặt job.

    ```sh
    SendWelcomeEmail::dispatch($user)->onQueue('emails');
    ```

- `delay()` cho phép trì hoãn việc xử lý job trong một khoảng thời gian nhất định.

    ```sh
    SendWelcomeEmail::dispatch($user)->delay(Carbon::now()->addMinutes(10));
    ```

- `push()` để đẩy một job vào queue.

    ```sh
    Queue::push(new SendWelcomeEmail($user));
    ```

- `later()` để đẩy một job vào queue với độ trễ.

    ```sh
        Queue::later(Carbon::now()->addMinutes(5), new SendWelcomeEmail($user));
    ```
-  `retryUntil()`xác định thời điểm mà job sẽ không được thử lại nữa.

    ```php
    public function retryUntil()
    {
        return Carbon::now()->addMinutes(10);
    }
    ```
- `Queue::before` và `Queue::after`

    Các phương thức này được sử dụng để lắng nghe sự kiện trước và sau khi job được xử lý.

    ```php
    use Illuminate\Support\Facades\Queue;
    use Illuminate\Queue\Events\JobProcessing;
    use Illuminate\Queue\Events\JobProcessed;

    public function boot()
    {
        parent::boot();

        // Sự kiện trước khi job được xử lý
        Queue::before(function (JobProcessing $event) {
            // Thực hiện một hành động nào đó
            \Log::info('Job đang được xử lý: ' . $event->job->getName());
        });

        // Sự kiện sau khi job được xử lý
        Queue::after(function (JobProcessed $event) {
            // Thực hiện một hành động nào đó
            \Log::info('Job đã được xử lý: ' . $event->job->getName());
        });
    }
    ```