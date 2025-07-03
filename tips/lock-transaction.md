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