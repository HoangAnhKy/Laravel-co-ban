# chỉnh lại thời gian chạy của `cronjob`
- Dùng cache hoặc db để thay đổi thời gian chạy của cron
- Nếu chạy cache nhớ clear cahce

## Ví dụ

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class demofile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('demo: ' . now()->format("Y-m-d H:i:s") . " - run demo file five");
        /*
        [2025-09-01 17:42:01] local.INFO: demo: 2025-09-01 17:42:01 - run demo file five  
        [2025-09-01 17:43:01] local.INFO: demo: 2025-09-01 17:43:01 - run demo file five  
        [2025-09-01 17:44:01] local.INFO: demo: 2025-09-01 17:44:01 - run demo file five  
        [2025-09-01 17:45:02] local.INFO: demo: 2025-09-01 17:45:02 - run demo file five  
        [2025-09-01 17:48:01] local.INFO: demo: 2025-09-01 17:48:01 - run demo file five  
        */
    }
}

```

```php
// Route:console
$time_run = cache('define.CRON', config('define.CRON'));
Schedule::command('app:run-demo-five')->cron($time_run);
```