# Khái niệm

Giúp lập lịch chạy các tác vụ tự động mà không cần tạo cronjob riêng cho từng lệnh. Chỉ cần thiết lập Schedule trong Laravel và chạy một cronjob duy nhất để Laravel tự quản lý.

# Cách hoạt động của Laravel Schedule

- Định nghĩa lịch trình trong `routes/console.php`
- Thiết lập một cronjob duy nhất trên server để chạy Laravel Scheduler
  ```sh
  * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
  ```
- Laravel sẽ tự động kiểm tra và thực hiện các công việc đã được lên lịch.

# Các phương thức Schedule phổ biến

### Chạy command Artisan

```php
$schedule->command('task:run')->everyMinute(); // Chạy mỗi phút
```

### call function

```php
$schedule->call(function () {
    \Log::info('Task chạy lúc: ' . now());
})->everyFiveMinutes(); // Chạy mỗi 5 phút


Schedule::call(function (){
    Log::info("test schedule");
    Artisan::call("task:run"); // command
})->everyMinute();
```

### Các tần suất chạy Schedule

| Lệnh Schedule                  | Mô tả                                  |
| :----------------------------- | :------------------------------------- |
| ->everyMinute()                | Chạy mỗi phút                          |
| ->everyTwoMinutes()            | Chạy mỗi 2 phút                        |
| ->everyFiveMinutes()           | Chạy mỗi 5 phút                        |
| ->everyTenMinutes()            | Chạy mỗi 10 phút                       |
| ->everyFifteenMinutes()        | Chạy mỗi 15 phút                       |
| ->everyThirtyMinutes()         | Chạy mỗi 30 phút                       |
| ->hourly()                     | Chạy mỗi giờ                           |
| ->hourlyAt(30)                 | Chạy vào phút 30 của mỗi giờ           |
| ->daily()                      | Chạy mỗi ngày vào lúc 00:00            |
| ->dailyAt('13:00')             | Chạy mỗi ngày vào 13:00                |
| ->twiceDaily(1, 13)            | Chạy 2 lần mỗi ngày vào 01:00 và 13:00 |
| ->weekly()                     | Chạy mỗi tuần (chủ nhật 00:00)         |
| ->weeklyOn(1, '8:00')          | Chạy vào thứ 2 hàng tuần lúc 08:00     |
| ->monthly()                    | Chạy vào ngày đầu tiên của tháng       |
| ->monthlyOn(5, '15:00')        | Chạy vào ngày 5 hàng tháng lúc 15:00   |
| ->quarterly()                  | Chạy mỗi quý                           |
| ->yearly()                     | Chạy mỗi năm                           |
| ->timezone('Asia/Ho_Chi_Minh') | Chạy theo múi giờ Việt Nam             |
