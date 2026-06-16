# Nếu dùng để  custom data để dùng chung 

## Bước 1: Khởi tạo Trait

```php
<?php

namespace App\Trait;

trait Resposition
{
    public static function responseData($data = [], $message = "", $status = 200)
    {
        if (empty($data)) {$message = "Dữ liệu trống";}
        return response()->json(["data" => $data, "message" => $message, "status" => $status], $status);
    }
}
```


## Bước 2: khai báo bên Controller
sử  dụng `use Resposition` và dùng `self::responseData()`

```php
<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Trait\Resposition;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    use Resposition;
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function getData()
    {
        $users = $this->service->getData();
        return self::responseData($users, "Lấy dữ liệu thành công");
    }

    public function createUser()
    {
        $user = $this->service->createUser();
        return self::responseData($user, "Tạo mới user thành công");
    }
}
```

# Nếu muốn tinh chỉnh câu lệnh SQL các kiểu thì

Phải xử dụng `**scope**` của laravel để truy vấn và truyền vào trait

```php
<?php

namespace App\Trait;

trait CustomQuery
{
    public function scopeCustomQuery($query, array $columns = [])
    {
        if (!empty($columns)) {
            return $query->select($columns);
        }

        return $query;
    }
}
```

Tại `Model` phải khai báo xử dụng `trait`


```php
<?php

namespace App\Models;

use App\Trait\CustomQuery;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use CustomQuery;
    public $guarded = [];
}
```

Khi truy vấn chỉ cần gọi là dùng

```php
public function getData()
{
    return Users::query()->customQuery(["email"])->get();
}
```