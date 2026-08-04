# Muốn tránh transaction bị lock
Dùng `lockForUpdate` nhưng mà với `transaction` ở db được khai báo khác. Để  tránh trường hợp phải đợi chung `transaction` dẫn đến `timeout` hoặc `deatlock`

1. Tạo thêm 1 db trong `config/database.php`

```php
// ...
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DB_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4'),
    'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
],
'mysql2' => [
    'driver' => 'mysql',
    'url' => env('DB_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4'),
    'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
],
// ...
```

2. Dùng `lockForUpdate` và gọi `modal` trong DB khác transaction bên ngoài

```php
$connection = DB::connection('mysql2');
try {
    $connection->beginTransaction();
    
    // self::on('mysql2')->select('index_value')->lockForUpdate();
    $mstIndex = (new MstIndex())->setConnection('mysql2');

    // Logic sau khi lockForUpdate

    $connection->commit();
} catch() {
    $connection->rollBack();
}
```

# dùng tips chỉ  tốn 2 câu query, có thể tách db và dùng cách này để tiếp kiệm tối đa tài nguyên

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Reverb\Loggers\Log;

class IndexCount extends Model
{
    public $guarded = [];
    public $timestamps = false;

    public static function getCount($name)
    {
        // 1. Ép tạo mới nếu chưa có (Tốn 1 query)
        DB::table('index_counts')->insertOrIgnore([
            'index_code' => $name,
            'count' => 0
        ]);
        // 2. Cộng số và ghi nhớ số vừa cộng (Tốn 1 query)
        DB::table('index_counts')->where('index_code', $name)->update([
            'count' => DB::raw('LAST_INSERT_ID(count + 1)')
        ]);
        // 3. Trả về đúng con số đó cho giao diện (0 tốn query)
        return DB::getPdo()->lastInsertId();
    }
    /* test
    for i in {1..500}; do
        curl -s http://demo.local/generate &
    done
    wait
     */
}
```