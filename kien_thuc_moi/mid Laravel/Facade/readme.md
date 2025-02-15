# Facade có các tác dụng chính sau

- `Đơn giản hóa cú pháp gọi phương thức`: Facade cho phép gọi các phương thức của một class thông qua giao diện tĩnh (static interface), giúp code trở nên ngắn gọn và dễ đọc mà không cần phải khởi tạo đối tượng hoặc inject dependencies.

- `Truy cập dễ dàng vào Service Container`: Khi sử dụng Facade, Laravel sẽ tự động resolve (lấy) instance thực sự của service từ Service Container. Điều này giúp dễ dàng quản lý và sử dụng các dịch vụ, thư viện đã được đăng ký trong container.

- `Tách biệt logic và dependency`: Facade ẩn đi chi tiết về cách khởi tạo và quản lý đối tượng, cho phép tập trung vào logic nghiệp vụ mà không lo lắng về việc quản lý dependency. Đồng thời, nó cũng hỗ trợ việc test bằng cách cho phép mock hoặc override các phương thức của service.

- `Tăng khả năng bảo trì và mở rộng`: Với Facade, nếu cần thay đổi cách hoạt động hoặc thay thế service cụ thể, chỉ cần thay đổi trong phần cấu hình hoặc trong lớp service, mà không cần phải sửa đổi nhiều chỗ trong code gọi.

- `Sử dụng khi` muốn một giao diện truy cập tĩnh tới các service trong container, giúp code gọn gàng mà không cần tự quản lý việc khởi tạo đối tượng.

# Ví dụ dùng

## Tạo một Service

```php
<?php
// File: app/Services/UserService.php
namespace App\Services;

class UserService
{
    /**
     * Tìm người dùng theo ID
     *
     * @param int $id
     * @return string
     */
    public function find($id)
    {
        // Ở đây có thể thực hiện truy vấn CSDL để lấy thông tin người dùng.
        // Ví dụ đơn giản:
        return "Thông tin người dùng có ID: " . $id;
    }
}
```

## Tạo Facade cho Service

```php
<?php
// File: app/Facades/UserService.php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UserService extends Facade
{
    /**
     * Trả về key mà Facade sử dụng để resolve service từ container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'userService';
    }
}
```

## Đăng ký Service trong Service Container và đăng ký alias cho Facade đó

```php
<?php
// File: app/Providers/AppServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UserService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Đăng ký userService dưới dạng singleton
        $this->app->singleton('userService', function ($app) {
            return new UserService();
        });
    }

    public function boot()
    {
        // Đăng ký alias cho Facade thông qua class_alias()
        class_alias(\App\Facades\UserService::class, 'UserService');
    }
}
```

## Sử dụng trong controller

```php
<?php
namespace App\Http\Controllers;

use UserService; // Sử dụng alias đã cấu hình trong boot

class UserController extends Controller
{
    public function show($id)
    {
        // Sử dụng Facade với tên đầy đủ đã import
        $userInfo = UserService::find($id);

        return view('user.show', compact('userInfo'));
    }
}
```
