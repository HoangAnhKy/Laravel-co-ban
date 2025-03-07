# Cài đặt mongodb

- Win
  - [download](https://pecl.php.net/package/mongodb/1.21.0/windows)
  - Thêm vào ext

# Cài đặt thư viện

```sh
composer require jenssegers/mongodb
```

# Thêm cấu hình vào `config/database`

```php
'mongodb' => [
    'driver'   => 'mongodb',
    'host'     => env('DB_HOST', '127.0.0.1'),
    'port'     => env('DB_PORT', 27017),
    'database' => env('DB_DATABASE', 'your_database'),
    'username' => env('DB_USERNAME', ''),
    'password' => env('DB_PASSWORD', ''),
    'options'  => []
],

```

# Cấu hình modal nếu cần

```php
use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class Users extends EloquentModel
{
    protected $connection = 'mongodb';
    protected $collection = 'users'; // tên collection trong MongoDB

    protected $fillable = ['name', 'email', 'password'];
}
```
