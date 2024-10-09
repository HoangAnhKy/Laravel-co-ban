Kiểm tra cache ở redis

```sh
php artisan tinker
```

và kiểm tra file `env` nơi lưu 

```env
CACHE_REDIS_DB=1
QUEUE_REDIS_DB=2
SESSION_REDIS_DB=3
```
# Một số lệnh cli res

##  quản lý key

- thu thập thông tin về các database đang hoạt động và các key có trong mỗi database.
    ```sh
    INFO keyspace
    ```
- `SET key value`: Đặt giá trị cho một key. Ví dụ:

    ```sh
    SET mykey "Hello World"
    ```
- `GET key`: Lấy giá trị của một key. Ví dụ:

    ```sh
    GET mykey
    ```

- `DEL key`: Xóa một hoặc nhiều key. Ví dụ:

    ```sh
    DEL mykey
    ```
- `EXISTS key`: Kiểm tra sự tồn tại của một key, trả về 1 nếu key tồn tại, 0 nếu không tồn tại.

    ```sh
    EXISTS mykey
    ```

- `EXPIRE key seconds`: Đặt thời gian sống (TTL) cho một key, sau đó key sẽ tự động bị xóa. Ví dụ:
    ```sh
    EXPIRE mykey 10
    ```

- `TTL key`: Kiểm tra thời gian sống còn lại của một key (tính bằng giây). Trả về -2 nếu key không tồn tại và -1 nếu key không có TTL.

    ```sh
    TTL mykey
    ```

## quản lý db

- `SELECT db_number`: Chọn database mà bạn muốn làm việc (đánh số từ 0 đến 15). 

    ```sh
    select 1
    ```
- `FLUSHDB`: Xóa tất cả các key trong database hiện tại
- `FLUSHALL`: Xóa tất cả các key trong tất cả các database.
- `KEYS pattern`: Tìm kiếm các key phù hợp với một mẫu cụ thể. Ví dụ: `KEYS user:*`

# Một số phương thức cơ bản:

+ `Cache::put()`          // Lưu trữ giá trị vào cache
+ `Cache::get()`          // Lấy giá trị từ cache
+ `Cache::remember()`     // Dùng cache remember để lưu và tự động lấy giá trị nếu chưa tồn tại
+ `Cache::rememberForever()`     // Dùng để lưu vĩnh viễn
+ `Cache::forget()`       // Xóa cache
+ `Cache::has()`          // Kiểm tra tồn tại

ví dụ
```php
// Lưu trữ giá trị vào cache
Cache::put('key', 'value', 600); // 600 là số giây dữ liệu được lưu

// Lấy giá trị từ cache
$value = Cache::get('key');

// Dùng cache remember để lưu và tự động lấy giá trị nếu chưa tồn tại
$value = Cache::remember('key', 600, function () {
    return DB::table('users')->get();
});
// Xóa cache
Cache::forget('key');
```