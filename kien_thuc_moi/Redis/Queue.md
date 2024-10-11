Tham khảo thêm tại [queue](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/kien_thuc_moi/Queue.md)

# chuyển đổi nơi lưu trữ queue trong env

```env
QUEUE_CONNECTION=redis
```

# Chạy worker để xử lý

```php
php artisan queue:work redis
```
