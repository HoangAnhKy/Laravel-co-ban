Dùng để theo dõi job

# Lệnh cài

- cài đặt gói

  ```sh
  composer require romanzipp/laravel-queue-monitor
  ```

- file cấu hình và migration
  ```sh
  php artisan vendor:publish --provider="romanzipp\QueueMonitor\Providers\QueueMonitorProvider" --tag=config --tag=migrations
  ```
- chạy migrate

  ```sh
  php artisan migrate
  ```

# cách dùng

- Sử dụng thêm `IsMonitored`

  ```php
  use Illuminate\Bus\Queueable;
  use Illuminate\Queue\SerializesModels;
  use Illuminate\Queue\InteractsWithQueue;
  use Illuminate\Contracts\Queue\ShouldQueue;
  use Illuminate\Foundation\Bus\Dispatchable;
  use romanzipp\QueueMonitor\Traits\IsMonitored;

  class ExampleJob implements ShouldQueue
  {
      use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

      // Nội dung của job
  }
  ```

# Kích hoạt giao diện web

- Bật `ui.enabled` trong file cấu hình `config/queue-monitor.php`

  ```php
  'ui' => [
      'enabled' => true,
      // Các cấu hình khác
  ],
  ```

- xuất file fontend

  ```sh
  php artisan vendor:publish --provider="romanzipp\QueueMonitor\Providers\QueueMonitorProvider" --tag=assets
  ```

- Thêm route

  ```php
  Route::get('queue-monitor', ShowQueueMonitorController::class);
  ```
