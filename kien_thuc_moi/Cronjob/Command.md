# Lệnh khởi tạo command

- File sẽ nằm tại `app/Console/Commands/`

    ```sh
    php artisan make:command NameCommand
    ```
-  Lưu ý: `signature` là lệnh chạy nó sẽ thực thi tất cả trong `handle`

    ```php
        protected $signature = 'temp:clear';

        /* sẽ thực thi bằng php artisan temp:clear
        hoặc

        Artisan::call('temp:clear');
        */

    ```

## sử dụng chung với job

```sh
php artisan make:job ClearTempFilesJob
```

```php
// ClearTempFilesJob
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ClearTempFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Không cần khởi tạo gì thêm
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Chạy lệnh Artisan để xóa các file temp
            Artisan::call('files:cleartemp');
            Log::info('ClearTempFilesJob: files:cleartemp executed successfully.');
        } catch (\Exception $e) {
            Log::error('ClearTempFilesJob: Error executing files:cleartemp - ' . $e->getMessage());
        }

        // Dispatch lại job sau 1 phút để chạy tiếp
        self::dispatch()->delay(now()->addMinute());
    }
}
```