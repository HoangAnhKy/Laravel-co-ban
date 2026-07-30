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
- `dispatch_sync()` Job chạy ngay trong request, request chờ job xong phù hợp job nhẹ
    ```php
    Route::get('/', function () {
        try {
            $result = dispatch_sync(new \App\Jobs\DemoJob());
            return 'Job ran successfully!';
        } catch (\Throwable $e) {
            return 'Job failed: ' . $e->getMessage();
        }
    });
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

    # trait hay dùng 

    | **Trait**                        | **Dùng trong**                | **Chức năng chính**                                                                             | **Ví dụ sử dụng thực tế**                                |
    | -------------------------------- | ----------------------------- | ----------------------------------------------------------------------------------------------- | -------------------------------------------------------- |
    | 🧱 `Dispatchable`                 | **Job / Event / Listener**    | Cho phép gọi `::dispatch()` để đẩy job hoặc event vào queue.                                    | `SendEmailJob::dispatch($user);`                         |
    | 🔁 `InteractsWithQueue`           | **Job / Listener**            | Cung cấp các method tương tác trực tiếp với job trong hàng đợi (retry, fail, release, delete…). | `$this->release(30); // chạy lại sau 30s`                |
    | 🚚 `Queueable`                    | **Job / Listener / Mailable** | Cấu hình thuộc tính queue: tên queue, delay, connection…                                        | `$this->onQueue('emails')->delay(now()->addMinutes(5));` |
    | 📦 `SerializesModels`             | **Job / Listener / Mailable** | Tự động serialize (biến thành ID) và unserialize (load lại model) khi job qua queue.            | `$this->user = $user; // Model được serialize an toàn`   |
    | 💌 `Queueable` *(trong Mailable)* | **Mailable**                  | Cho phép mail được xếp hàng khi gọi `Mail::to()->queue()`.                                      | `Mail::to($email)->queue(new WelcomeMail($user));`       |


# Xử lý jobs nặng

- nên tạo thêm 1 bảng database để  lưu jobs đang chạy, sau đó cập nhật status và error nếu có
- tạo thêm `listener` lắng nghe từ `event JobFailed`, nó sẽ tự động chạy vào nếu job chạy lỗi hoặc timeout
```php
<?php

namespace App\Listeners;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateBatchJobOnFail
{
    public function handle(JobFailed $event): void
    {
        try {
            $payload = $event->job->payload();

            if (!isset($payload['data']['command'])) return;

            $command = unserialize($payload['data']['command']);

            if ($command instanceof \App\Jobs\DemoJob && isset($command->idSession)) {

                // Lấy message lỗi từ event (nếu có)
                $error = $event->exception->getMessage();
                $statusMsg = 'Job failed';
                if (strpos($error, 'timeout') !== false) {
                    $statusMsg = 'Job timeout';
                }

                DB::table('batchJob')
                    ->where('id_session', $command->idSession)
                    ->update([
                        'status' => 2, // failed
                        'error' => $error ?: $statusMsg,
                        'updated_at' => now(),
                    ]);

                Log::info("BatchJob updated to failed, idSession: " . $command->idSession . ", error: " . ($error ?: $statusMsg));
            }

        } catch (\Throwable $e) {
            Log::error("Failed to update batchJob status: " . $e->getMessage());
        }
    }
}
```
### CheckBatch
Dùng với bảng batch riêng để theo dõi bath chạy rồi trả về giao diện

```php
<?php

namespace App\Service;

use App\Models\Batch;

class BatchService
{
    public static function addBatch($name, $batch_Code)
    {
        $batch = Batch::query()->where('batch_code', $batch_Code)->lockForUpdate()->first();

        // Chưa có batch => tạo mới và chạy
        if (!$batch) {
            Batch::query()->create([
                'name' => $batch_Code,
                'batch_code' => $batch_Code,
                'status' => 1,
            ]);
            return true;
        }
        // Đang chạy
        if ($batch->status == 1) { return false;}
        // Đã tồn tại nhưng đang tắt => bật lại
        $batch->update(['status' => 1]);
        return true;
    }

    public function updateBatch($batch_Code, $status)
    {
        return Batch::query()
            ->where("batch_code", $batch_Code)
            ->update(['status' => $status]);
    }

    public function checkBatch($batch_Code)
    {
        $query = Batch::query()
            ->where("batch_code", $batch_Code)
            ->where("status", "<>", config("define.BATCH_STATUS_RUN"))
            ->first();
        if (empty($query)) return null;
        return $query->status;
    }
}
```
Khởi tạo controller để giao diện call API check

```php
public function checkBatch(Request $request)
{
    try {
        $validated = $request->validate([
            'batch_code' => 'required|string|max:255|exists:batches,batch_code'
        ]);
        // 8.1 trở đi dùng match gọn hơn switch case
        [$flag, $msg] = match ($this->service->checkBatch($validated['batch_code'])) {
            null => [false, 'Batch is available'],
            0    => [true, 'Batch is done'],
            2    => [true, 'Batch is failed'],
            default => [false, 'Unknown batch status'],
        };
        return $this->responseData(
            ["done" => $flag],
            "Check batch success, " . $msg
        );
    }
    catch (ValidationException $e) {
        return $this->responseValidated($e);
    }catch (\Exception $e) {
        return $this->responseError($e->getMessage());
    }
}
```
về  phía giao diện, khởi tạo 1 hàm để check dùng chung

```js
<script>
    const checkBatch = (batch_code, callback) => {
        // truyền tên batch cho code check
        axios.post('{{ route("check-batch") }}', { batch_code })
            .then(res => {
                // dùng callback để nhận xử lý riêng cho từng nghiệp vụ khi batch hoàn thành
                if (callback && res.data.data.done) {
                    callback(res);
                } else {
                    // nếu nó chưa xog thì 5s sau chạy lai check
                    setTimeout(() => {
                        checkBatch(batch_code, callback);
                    }, 5000);
                }
            })
            .catch(err => {
                if (callback) {
                    callback(null, err);
                }
            });
    }
</script>
```