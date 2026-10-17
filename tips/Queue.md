- Khi chạy queue bị down liên tục hãy thử 

    ```sh
    php artisan queue:work --memory=1024 --timeout=600 --tries=3
    ```
| Tham số | Ý nghĩa |
|---|---|
| `--memory=1024` | Worker được phép sử dụng tối đa khoảng 1024 MB RAM theo ngưỡng Laravel kiểm tra. Khi vượt ngưỡng, worker sẽ thoát để được khởi động lại. |
| `--timeout=600` | Cho phép một job chạy tối đa 600 giây (10 phút) trước khi worker bị dừng do timeout. |
| `--tries=3` | Thử xử lý một job tối đa 3 lần trước khi đánh dấu thất bại. |