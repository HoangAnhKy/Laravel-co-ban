Tham khảo thêm tại [queue](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/kien_thuc_moi/package/Queue.md)

# chuyển đổi nơi lưu trữ queue trong env

```env
QUEUE_CONNECTION=redis
```

# Chạy worker để xử lý

```php
php artisan queue:work redis
```

# ví dụ

```php
// jobClearRedisDB
<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class jobClearRedisDB implements ShouldQueue
{
    use Queueable;

    private $key;
    /**
     * Create a new job instance.
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \RedisDB::delete($this->key);
            Log::info("ok");
        }catch (\Exception $e){
            Log::error($e);
        }
        Log::info('Lệnh chạy xog lúc: ' . now());
    }
}
// Controller
// Tới 30s kể từ khi tạo sẽ bị xóa trong jobClearRedisDB
public function index()
    {
        $key = "userCache";
        if (RedisDB::exists($key)){
            $data = unserialize(RedisDB::get($key));
        }else{
            sleep(3);
            $data  = UserLibrary::getData();
            RedisDB::set($key, serialize($data), 30);
            jobClearRedisDB::dispatch($key)->delay(Carbon::now()->addSecond(30)); // dùng
        }
        $users = $data;

        return view("User.index", compact("users"));
    }
```
