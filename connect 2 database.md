# Khởi tạo khai báo

B1: vào `.env` khởi tạo 2 database

    vd:

    ```php
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=mysql_database
    DB_USERNAME=root
    DB_PASSWORD=secret

    DB_CONNECTION_PGSQL=pgsql
    DB_HOST_PGSQL=127.0.0.1
    DB_PORT_PGSQL=5432
    DB_DATABASE_PGSQL=pgsql_database
    DB_USERNAME_PGSQL=root
    DB_PASSWORD_PGSQL=secret
    ```

B2: vào `config/database.php` khai báo

vd:

```php
'mysql' => [
    'driver'    => env('DB_CONNECTION'),
    'host'      => env('DB_HOST'),
    'port'      => env('DB_PORT'),
    'database'  => env('DB_DATABASE'),
    'username'  => env('DB_USERNAME'),
    'password'  => env('DB_PASSWORD'),
],

'pgsql' => [
    'driver'    => env('DB_CONNECTION_PGSQL'),
    'host'      => env('DB_HOST_PGSQL'),
    'port'      => env('DB_PORT_PGSQL'),
    'database'  => env('DB_DATABASE_PGSQL'),
    'username'  => env('DB_USERNAME_PGSQL'),
    'password'  => env('DB_PASSWORD_PGSQL'),
],
```

# Để khởi tạo `database` bằng `Schema / Migration` ta dùng như sau

```php
Schema::connection('pgsql')->create('some_table', function($table)
{
    $table->increments('id'):
});
```

hoặc khai báo ở trên cùng file như sau

```php
    protected $connection = 'pgsql';
```

# Dùng 2 database với QueryBuilder

-   Khởi tạo `$connection` trong model

```php
class ModelName extends Model { // extend changed

    protected $connection = 'pgsql';

}
```
