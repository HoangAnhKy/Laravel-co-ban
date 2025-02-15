**`Khi nào khởi tạo`**: Service Container được Laravel khởi tạo tự động ngay từ khi ứng dụng bắt đầu chạy (bootstrapping). Không cần phải khởi tạo nó theo cách thủ công.

**`Mục đích`**: Nó dùng để quản lý dependency injection, giúp tự động tiêm các phụ thuộc cần thiết vào class khi chúng được yêu cầu.

# Đăng ký Service

đăng ký tại `AppServiceProvider`, lưu ý [cách dùng các phương](./phương%20thức%20đăng%20ký.md) để sự dụng phù hợp

```php
public function register(): void
{
    // Dùng để đăng ký các binding, service, và thực hiện các cấu hình không phụ thuộc vào việc đã đăng ký các provider khác hay chưa.

    // Đảm bảo rằng các binding này luôn sẵn sàng khi cần resolve chúng, kể cả trong các phần khác của ứng dụng.
}

public function boot(): void
{
    // Được sử dụng để thực hiện các thao tác khởi tạo sau khi tất cả các Service Provider đã được đăng ký

    // Thích hợp cho các tác vụ như đăng ký event listeners, route model bindings, hay xử lý logic phụ thuộc vào các binding đã được đăng ký.
}
```

# Ví dụ đăng ký log

**B1: khởi tạo project**

```sh
composer create-project laravel/laravel ["name"]
```

**B2: khởi tạo interface**

Tại: `app/Contracts/`
Code:

```php
<?php

namespace App\Contracts;

interface LoggerInterface
{
    public function log(string $message);
}
```

**B3: khởi tạo Services và kế thừa Interface**

Tại: `app\Services`

Code:

```php

<?php

namespace App\Services;

use App\Contracts\LoggerInterface;

class FileLogger implements LoggerInterface
{
    public function log(string $message)
    {
        // Giả sử ghi log vào file (ở đây chỉ đơn giản in ra)
        echo "Ghi log vào file: " . $message;
    }
}
```

**B4: Đăng ký Binding trong Service Provider**

```php
 public function register()
{
    // Bind LoggerInterface với FileLogger
    $this->app->bind(LoggerInterface::class, function ($app) {
        return new FileLogger();
    });
}
```

**B5: Dependency Injection trong Controller**

sử dụng Dependency Injection trong Controller để Laravel tự động resolve LoggerInterface đã được bind

```php
<?php

namespace App\Http\Controllers;

use App\Contracts\LoggerInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $logger;

    // LoggerInterface sẽ được tự động resolve từ container
    public function __construct(LoggerInterface $logger) // phải interface hoặc abstract class
    {
        $this->logger = $logger;
    }

    public function index()
    {
        // dd(app(LoggerInterface::class)->log("hi")); // hoặc

        $this->logger->log("Hello from Laravel Service Container!");
        return view('welcome');
    }
}
```
