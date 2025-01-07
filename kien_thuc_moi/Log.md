# Tạo mới kênh Log

mở file `config/logging.php`

```php
'channels' => [
    // Các channels mặc định
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single'],
        'ignore_exceptions' => false,
    ],
    // Channel log mới
    'custom_log' => [
        'driver' => 'single', // Hoặc 'daily', 'syslog', v.v.
        'path' => storage_path('logs/custom_log.log'), // Đường dẫn tới file log
        'level' => 'debug', // Mức log (debug, info, notice, warning, error, critical, alert, emergency)
    ],
],

```

| **Attribute**          | **Ý nghĩa**                                                                                     | **Ví dụ giá trị**                          |
|-------------------------|-----------------------------------------------------------------------------------------------|-------------------------------------------|
| `driver`               | Loại driver được sử dụng để ghi log (single, daily, slack, syslog, monolog, stack, v.v.).       | `'daily'`, `'single'`, `'slack'`          |
| `path`                 | Đường dẫn tới file log mà driver sẽ ghi log vào.                                                | `storage_path('logs/laravel.log')`        |
| `level`                | Mức độ log được ghi lại (debug, info, warning, error, critical, alert, emergency).              | `'debug'`, `'error'`, `'critical'`        |
| `days`                 | Số ngày giữ lại file log (áp dụng cho driver `daily`).                                          | `14`, `30`, `null`                        |
| `channels`             | Danh sách các kênh log được sử dụng đồng thời (chỉ áp dụng với `stack`).                        | `['single', 'slack']`                     |
| `url`                  | URL Webhook cho driver `slack` hoặc các dịch vụ bên ngoài.                                       | `'https://hooks.slack.com/services/...`'` |
| `username`             | Tên hiển thị khi gửi log qua Slack.                                                             | `'Laravel Log'`                           |
| `emoji`                | Biểu tượng (emoji) đi kèm khi gửi log qua Slack.                                                | `':boom:'`, `':warning:'`                 |
| `formatter`            | Định dạng log đầu ra (dùng trong Monolog).                                                      | `'json'`, `null`                          |
| `handler`              | Lớp xử lý log trong Monolog (áp dụng cho driver `monolog`).                                     | `StreamHandler::class`, `SyslogUdpHandler::class` |
| `handler_with`         | Tham số truyền cho handler (như host, port).                                                    | `['host' => 'example.com', 'port' => 514]`|
| `ignore_exceptions`    | Bỏ qua ngoại lệ trong quá trình ghi log (chỉ áp dụng với `stack`).                              | `true`, `false`                           |
| `facility`             | Xác định facility syslog khi dùng driver `syslog`.                                              | `LOG_USER`, `LOG_AUTH`                    |
| `replace_placeholders` | Tự động thay thế các placeholder trong message log.                                             | `true`, `false`                           |
| `processors`           | Danh sách các processors được thêm vào để xử lý message log trước khi ghi.                     | `[PsrLogMessageProcessor::class]`         |

- drive


    | **Driver**   | **Mục đích**                                              | **Khi nào dùng**                                   |
    |--------------|----------------------------------------------------------|--------------------------------------------------|
    | `single`     | Ghi log vào một file duy nhất.                            | Ứng dụng nhỏ, môi trường phát triển.             |
    | `daily`      | Ghi log vào file mới mỗi ngày.                            | Ứng dụng lớn, cần quản lý log tốt hơn.           |
    | `syslog`     | Gửi log đến hệ thống logging của máy chủ.                 | Hệ thống tập trung hóa log (Linux/Unix).         |
    | `errorlog`   | Gửi log tới hệ thống log mặc định của PHP.                | Hosting hạn chế quyền ghi file.                  |
    | `stack`      | Ghi log vào nhiều nơi cùng lúc.                           | Hệ thống cần log đồng thời nhiều kênh.           |
    | `slack`      | Gửi log đến kênh Slack.                                   | Cảnh báo lỗi nghiêm trọng cho team.              |
    | `papertrail` | Gửi log tới dịch vụ Papertrail.                           | Sử dụng Papertrail để quản lý log.               |

- level

    | **Level**     | **Mô tả**                                                                               | **Khi nào sử dụng**                                                                                     |
    |---------------|-----------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------|
    | `debug`       | Dùng để ghi log thông tin chi tiết về lỗi, dùng chủ yếu cho việc gỡ lỗi (debugging).    | Khi cần theo dõi thông tin kỹ thuật chi tiết hoặc chẩn đoán lỗi trong môi trường phát triển.          |
    | `info`        | Ghi lại các sự kiện thông thường hoặc trạng thái của ứng dụng.                          | Khi muốn ghi nhận sự kiện hệ thống hoặc hành động của người dùng, ví dụ: "Người dùng đã đăng nhập".  |
    | `notice`      | Các thông báo quan trọng nhưng không phải lỗi.                                          | Khi cần ghi nhận những vấn đề không khẩn cấp, ví dụ: "Dịch vụ X đang chạy chậm".                     |
    | `warning`     | Cảnh báo về vấn đề có thể dẫn đến lỗi trong tương lai.                                  | Khi có các vấn đề tiềm ẩn, ví dụ: "Đĩa lưu trữ gần đầy".                                              |
    | `error`       | Lỗi xảy ra nhưng không làm ứng dụng ngừng hoạt động.                                    | Khi xảy ra lỗi cụ thể, ví dụ: "Không thể kết nối tới cơ sở dữ liệu".                                 |
    | `critical`    | Vấn đề nghiêm trọng yêu cầu can thiệp ngay lập tức.                                     | Khi ứng dụng bị lỗi nghiêm trọng, ví dụ: "Không tải được cấu hình quan trọng".                      |
    | `alert`       | Yêu cầu hành động ngay lập tức để khắc phục sự cố.                                      | Khi xảy ra lỗi có thể gây hại đến toàn bộ hệ thống, ví dụ: "Mật khẩu admin bị thay đổi trái phép".   |
    | `emergency`   | Hệ thống không thể hoạt động hoặc sắp ngừng hoạt động.                                  | Khi toàn bộ hệ thống gặp lỗi nghiêm trọng, ví dụ: "Cơ sở dữ liệu bị sập hoàn toàn".                |


# Sử dụng channel log trong code

dùng `Log::channel()` ví dụ

```php
use Illuminate\Support\Facades\Log;

Log::channel('custom_log')->info('This is a custom log message.');
Log::channel('custom_log')->error('This is an error log in custom log file.');
```