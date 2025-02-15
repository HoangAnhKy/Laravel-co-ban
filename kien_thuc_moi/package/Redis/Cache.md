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
- `HDEL key`: xóa các trường nhỏ

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

# Làm việc với cache


# Tóm Tắt Các Cờ Lệnh SET

| Cờ      | Ý Nghĩa                       | Mô Tả                                                                 |
|---------|-------------------------------|----------------------------------------------------------------------|
| EX      | Expire Time in Seconds        | Đặt TTL (thời gian sống) cho key tính bằng giây                       |
| PX      | Expire Time in Milliseconds   | Đặt TTL cho key tính bằng mili giây                                   |
| NX      | Set If Not Exists             | Chỉ thiết lập key nếu chưa tồn tại                                    |
| XX      | Set If Exists                 | Chỉ thiết lập key nếu đã tồn tại                                      |
| KEEPTTL | Keep Time-to-Live             | Giữ nguyên TTL hiện tại khi cập nhật giá trị                          |
| GET     | Set and Return Old Value      | Đặt giá trị mới và trả về giá trị cũ của key nếu có (từ Redis 6.2+)    |

## Ví dụ sử dụng các cờ lệnh:

### 1. Sử dụng cờ EX
```redis
SET mykey "value" EX 10
```
Thiết lập key `mykey` với giá trị `"value"` và TTL là 10 giây.

### 2. Sử dụng cờ PX
```redis
SET mykey "value" PX 5000
```
Thiết lập key `mykey` với giá trị `"value"` và TTL là 5000 mili giây.

### 3. Sử dụng cờ NX
```redis
SET mykey "value" NX
```
Chỉ thiết lập `mykey` nếu nó chưa tồn tại.

### 4. Sử dụng cờ XX
```redis
SET mykey "value" XX
```
Chỉ thiết lập `mykey` nếu nó đã tồn tại.

### 5. Sử dụng cờ KEEPTTL
```redis
SET mykey "newvalue" KEEPTTL
```
Cập nhật giá trị của `mykey` nhưng giữ nguyên TTL hiện tại.

### 6. Sử dụng cờ GET
```redis
SET mykey "newvalue" GET
```
Thiết lập `mykey` với giá trị mới `"newvalue"` và trả về giá trị cũ.

## Một số phương thức cơ bản với Redis

```php
use Illuminate\Support\Facades\Redis;

// Xóa hết ở tất cả db
Redis::connection("cache")->flushall()

// Xóa ở db hiện tại
Redis::select(2);
Redis::flushdb();

// Lưu giá trị vào Redis

/*
    dùng serialize và unserialize khi set dữ liệu là object hay mảng nó sẽ giữ nguyên 

    $res =  $query->paginate(LIMIT);
    Redis::set($cacheKey, serialize($res));

    $res = unserialize(Redis::get($cacheKey));

*/
Redis::set('key', 'value');

// Lưu nhiều
Redis::mset(['key1' => 'value1', 'key2' => 'value2']);

// Lưu trữ một giá trị vào trong hash với một field cụ thể.
Redis::hset('user:1000', 'name', 'John Doe');

// Lưu giá trị vào Redis với thời gian hết hạn (600 giây)
Redis::setex('temp_key', 600, 'Temporary Value');

// Thêm thời gian cho key có sẵn
Redis::expire('key', 300); // Key sẽ hết hạn sau 300 giây

// Lấy giá trị từ Redis
$value = Redis::get('key');

// Lấy nhiều
Redis::mget(['key1', 'key2', 'key3']);

// lấy một giá trị vào trong hash với một field cụ thể.
Redis::hget('user:1000', 'name')

Redis::hgetall('user:1000');

// Kiểm tra xem giá trị có tồn tại không
if (Redis::exists('key')) {
    echo "The key exists!";
}

// Xóa giá trị khỏi Redis, Xóa chữ laravel_database_
Redis::del('key');

// Tăng giá trị của một khóa
Redis::incr('counter');

// Giảm giá trị của một khóa
Redis::decr('counter');

// Lưu viễn viên một key đã tồn tại không hết hạn
Redis::persist('key');

// Lấy toàn bộ keys
Redis::connection('cache')->keys("*")

// scan
Redis::connection('cache')->scan('0') // key ở index 1
Redis::connection('cache')->scan('0', ['MATCH' => "laravel_database_student_"]) // Tìm kiếm key bắt đầu bằng laravel_database_student_
/*
: ['MATCH' => "search_key"] // Tìm kiếm
: ['COUNT' => $count] // lấy số lượng key khi quét
*/
```

## Với mảng dữ liệu

```php
use Illuminate\Support\Facades\Redis;

// cách thao tác phải có chuyển đổi như là json_encode/json_decode hay serialize/unserialize

// Thêm vào trái hoặc phải
Redis::rpush('queue_name', 'value1', 'value2', ...); // right
Redis::lpush('queue_name', 'value1', 'value2', ...); // left

// Lấy và xóa danh sách
Redis::lpop('task_queue') // lấy đầu
Redis::rpop('task_queue') // lấy cuối

// blocking: chặn và chờ khi hàng đợi trống, tức là không phải liên tục kiểm tra queue mà chỉ cần chờ cho đến khi có phần tử mới xuất hiện

Redis::blpop($key, $timeout); // Tên danh sách, Thời gian chờ
Redis::brpop($key, $timeout); // Tên danh sách, Thời gian chờ
```

## Một số phương thức cơ bản vơi cache:

+ `Cache::put()`          // Lưu trữ giá trị vào cache
+ `Cache::get()`          // Lấy giá trị từ cache
+ `Cache::remember()`     // Dùng cache remember để lưu và tự động lấy giá trị nếu chưa tồn tại
+ `Cache::rememberForever()`     // Dùng để lưu vĩnh viễn
+ `Cache::forget()`       // Xóa cache
+ `Cache::has()`          // Kiểm tra tồn tại
+ `Cache::increment()`          // Tăng key lên
+ `Cache::decrement()`          // Giảm key đi

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
// Tăng counter lên 5
Cache::increment('counter', 5);
// giảm đi
Cache::decrement('counter', 2);
```