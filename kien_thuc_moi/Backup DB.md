# Cài đặt thư viện

Nó sẽ có liên quan tới thư viện `php_zip`, cần phải chú ý mở `ext` hoặc cài đặt nó

```sh
composer require spatie/laravel-backup
```

# Xuất tệp cấu hình

```sh
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

Mở tệp `config/backup.php` và tùy chỉnh theo nhu cầu. Một số thiết lập quan trọng:

- `name`: Tên ứng dụng.
- `source.files.include`: Các thư mục và tệp sẽ được bao gồm trong bản sao lưu.
- `source.files.exclude`: Các thư mục và tệp sẽ bị loại trừ khỏi bản sao lưu (ví dụ: vendor, node_modules).
- `source.databases`: Các kết nối cơ sở dữ liệu sẽ được sao lưu (ví dụ: mysql).

# Lệnh chạy backup hoặc cài thời gian

- Lệnh

  ```sh
  php artisan backup:run # backup
  php artisan backup:clean # xóa bản ghi cũ
  ```

- Cài thời gian

  ```php
  // routes/console.php
  Schedule::command('backup:clean')->dailyAt('02:00');
  Schedule::command('backup:run')->dailyAt('02:00');
  ```
