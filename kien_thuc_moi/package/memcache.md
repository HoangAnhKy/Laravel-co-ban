# Khái niệm memcache

là một hệ thống bộ nhớ đệm (cache) phân tán, thường được sử dụng để tăng tốc ứng dụng web bằng cách lưu trữ dữ liệu và các đối tượng trong bộ nhớ RAM nhằm giảm thời gian truy xuất dữ liệu từ cơ sở dữ liệu hoặc các dịch vụ khác. Điều này giúp giảm tải cho hệ thống, tăng hiệu năng, và giảm thời gian phản hồi của ứng dụng.

# Lệnh xóa cache

```sh
php artisan cache:clear && php artisan view:clear && php artisan config:clear && php artisan event:clear && php artisan route:clear
```
# Cấu hình memcache

B1: Cài đặt Memcached và php-memcached 

1) [windown](https://memcached.org/)

tải [php-memcached ](https://windows.php.net/downloads/pecl/releases/memcached/3.2.0/) 

sau đó copy thư mục `php-memcached.dll` qua ext và thêm nó vô `php.ini`

2) linux
```sh
# cài với linux
sudo apt-get install memcached php-memcached
```

3) docker

Sử dụng lệnh sau để kéo và chạy một container Memcached từ Docker Hub:

```sh
docker run -d --name memcached-container -p 11211:11211 memcached 
```
- chú thích:
    - -d: Chạy container ở chế độ nền (detached mode).
    - --name memcached-container: Đặt tên cho container là memcached-container.
    - -p 11211:11211: Mở cổng 11211 của container ra cổng 11211 của máy chủ để Laravel có thể kết nối tới.
memcached: Tên của image Memcached từ Docker Hub.

**Lưu ý khi truy cập docker check cache**
- Khi sử dụng docker exec để truy cập vào container, cần thêm tùy chọn `--user root` để có quyền root trong container.

```sh
docker exec -it --user root memcached-container sh
```
sau khi vào bằng root cài telnet với libmemcached-tools (nếu muốn sử dụng memcached-tool):

```sh
apt-get update
apt-get install telnet
apt-get install libmemcached-tools
```

- Kết nối telnet sau khi cài đặt

```sh
telnet 127.0.0.1 11211

# thành công sẽ hiện
# Trying 127.0.0.1...
# Connected to 127.0.0.1.
# Escape character is '^]'.

```
lệnh cơ bản
- `quit` để thoát
- `get {key}` để lấy {key}
- `stats items` hiển thị thông tin về các slab (bộ nhớ được chia thành các khối gọi là slabs) và số lượng các items (các cặp key-value) được lưu trữ trong mỗi slab.
- `stats cachedump` kiểm tra danh sách các key trong Memcached

B2: Muốn chỉnh sửa `memcache` ở laravel, vào `config/cache`tìm đến mục `stores`

B3: thiết lập `env`

```sh
MEMCACHED_HOST=127.0.0.1
MEMCACHED_PORT=11211
MEMCACHED_USERNAME=null  # Nếu bạn có thiết lập SASL authentication thì thêm username
MEMCACHED_PASSWORD=null  # Nếu có sử dụng password thì thêm vào đây
MEMCACHED_PERSISTENT_ID=null  # Để null nếu không cần
```

B4: Sử dụng Memcached làm driver mặc định

```sh
'default' => env('CACHE_DRIVER', 'memcached')
```
## Một số option của memche

1) Memcached::OPT_CONNECT_TIMEOUT

Mô tả: Thời gian tối đa (tính bằng mili giây) để kết nối với một máy chủ Memcached.

Mặc định: 1000 (1 giây).

Ví dụ:

```php
'options' => [
    Memcached::OPT_CONNECT_TIMEOUT => 2000, // 2 giây
]
```
2) Memcached::OPT_RETRY_TIMEOUT

Mô tả: Thời gian (tính bằng giây) trước khi thử kết nối lại với một máy chủ Memcached đã gặp sự cố.

Mặc định: 0 (thử kết nối lại ngay lập tức).

Ví dụ:
```php
'options' => [
    Memcached::OPT_RETRY_TIMEOUT => 2, // Thử kết nối lại sau 2 giây
]
```
3) Memcached::OPT_COMPRESSION

Mô tả: Bật hoặc tắt tính năng nén dữ liệu trước khi lưu vào Memcached.

Mặc định: true (bật).

Ví dụ:
```php
'options' => [
    Memcached::OPT_COMPRESSION => false, // Tắt nén dữ liệu
]
```
4) Memcached::OPT_BINARY_PROTOCOL

Mô tả: Bật hoặc tắt giao thức nhị phân của Memcached. Giao thức nhị phân thường có hiệu suất tốt hơn so với giao thức văn bản.

Mặc định: false (sử dụng giao thức văn bản).

Ví dụ:

```php
'options' => [
    Memcached::OPT_BINARY_PROTOCOL => true, // Sử dụng giao thức nhị phân
]
```
5) Memcached::OPT_LIBKETAMA_COMPATIBLE

Mô tả: Bật chế độ phân phối dữ liệu consistent hashing. Điều này giúp tối ưu phân phối dữ liệu khi sử dụng nhiều máy chủ Memcached và tránh mất dữ liệu khi một máy chủ bị loại khỏi hệ thống.

Mặc định: false.

Ví dụ:
```php

'options' => [
    Memcached::OPT_LIBKETAMA_COMPATIBLE => true, // Bật chế độ consistent hashing
]
```
6) Memcached::OPT_SERIALIZER

Mô tả: Đặt kiểu serializer (công cụ tuần tự hóa) để lưu trữ dữ liệu. Laravel hỗ trợ các kiểu như JSON, IGBINARY, hoặc PHP.

Mặc định: Memcached::SERIALIZER_PHP.

Ví dụ:
```php
'options' => [
    Memcached::OPT_SERIALIZER => Memcached::SERIALIZER_JSON, // Dùng JSON serializer
]
```
7) Memcached::OPT_NO_BLOCK

Mô tả: Bật hoặc tắt chế độ non-blocking I/O (I/O không đồng bộ). Khi bật, Memcached sẽ không chờ đợi kết nối hoàn thành mà tiếp tục xử lý ngay lập tức.

Mặc định: false.

Ví dụ:

```php
'options' => [
    Memcached::OPT_NO_BLOCK => true, // Bật non-blocking I/O
]
```
8) Memcached::OPT_TCP_NODELAY

Mô tả: Bật hoặc tắt tùy chọn TCP_NODELAY để tối ưu hóa việc truyền dữ liệu thông qua TCP.

Mặc định: false.

Ví dụ:
```php
'options' => [
    Memcached::OPT_TCP_NODELAY => true, // Bật TCP_NODELAY để giảm độ trễ
]
```
9) Memcached::OPT_SOCKET_RECV_SIZE & Memcached::OPT_SOCKET_SEND_SIZE

Mô tả: Cấu hình kích thước buffer khi nhận và gửi dữ liệu thông qua socket.

Ví dụ:
```php

'options' => [
    Memcached::OPT_SOCKET_RECV_SIZE => 4096, // Kích thước buffer nhận
    Memcached::OPT_SOCKET_SEND_SIZE => 4096, // Kích thước buffer gửi
]
```
10) Memcached::OPT_SERVER_FAILURE_LIMIT

Mô tả: Số lần thử kết nối thất bại với một máy chủ trước khi máy chủ đó được đánh dấu là không khả dụng.

Mặc định: 0 (vô hiệu hóa).

Ví dụ:

```php

'options' => [
    Memcached::OPT_SERVER_FAILURE_LIMIT => 2, // Đánh dấu không khả dụng sau 2 lần kết nối thất bại
]
```
11) Memcached::OPT_REMOVE_FAILED_SERVERS

Mô tả: Tự động loại bỏ các máy chủ Memcached bị lỗi khỏi danh sách server.

Mặc định: false.

Ví dụ:
```php

'options' => [
    Memcached::OPT_REMOVE_FAILED_SERVERS => true, // Tự động loại bỏ máy chủ bị lỗi
]
```
12) Memcached::OPT_PREFIX_KEY

Mô tả: Đặt một tiền tố cho tất cả các khóa cache. Điều này giúp phân biệt các cache được tạo từ các ứng dụng khác nhau hoặc môi trường khác nhau.

Ví dụ:

```php

'options' => [
    Memcached::OPT_PREFIX_KEY => 'my_app_', // Thêm tiền tố 'my_app_' vào các khóa cache
]
```

# Một số phương thức cơ bản:

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

+ `Cache::tags('key')` nhóm các cache liên quan đến một tag
    - `Cache::tags('key')->flush()` xóa toàn bộ cache liên quan tới key
    - `Cache::tags('key')->put('key', 'value', 'time')` lưu
    - `Cache::tags('key')->get('key')` lấy
    - `Cache::tags('key')->forget('key')` xóa cache cụ thể
    - `Cache::tags('key')->has('key')` check tồn tại
    - `Cache::tags('key')->remember()` và `Cache::tags('key')->rememberForever()`

+  `Cache::getMemcached()` trả về một đối tượng Memcached, từ đó có thể gọi trực tiếp các phương thức của Memcached như `getAllKeys()`, `get()`, `set()`, `delete()`, `flush()`, `getStats()`, `increment()`, và `decrement()`.

    ví dụ `Cache::getMemcached()->getAllKeys()` Lấy tất cả các keys