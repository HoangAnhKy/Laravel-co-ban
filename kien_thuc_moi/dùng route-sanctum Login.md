# dùng route kiểu `Route::resource`

```php
Route::resource("/", IdeasController::class);  // Đăng ký resource route
Route::resource('posts', IdeasController::class)->only(['index', 'show']); // chỉ sử dụng
Route::resource('posts', IdeasController::class)->except(['destroy']); // trừ

// đổi tên
Route::resource('posts', PostController::class)->names([
    'index' => 'posts.list',
    'show' => 'posts.view',
]);

// tùy chỉnh url. Điều này sẽ thay đổi {article} thành {article} thay vì {articles} trong URI.
Route::resource('articles', ArticleController::class)->parameters([
    'articles' => 'article',
]);

// Dùng middleware
Route::resource('posts', PostController::class)->middleware('auth');
```

**chỉ dùng được cho controller có các action sau: index, create, store, show, edit, destroy, update**

# bỏ qua csrf

```php
// bootstrap/app.php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
            'api/create-idea', // cho route vô
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

```

# Lấy CSRF bằng sanctum

### Khái niệm sancturm

Là một package của Laravel được thiết kế để đơn giản hóa quá trình xác thực API và các ứng dụng SPA (Single Page Application). Dưới đây là một số tác dụng chính của Sanctum:

- `API Token Authentication`: cho phép người dùng tạo ra các token API cá nhân để xác thực các request đến API
- `Xác thực cho SPA`: có thể xác thực các ứng dụng `SPA` sử dụng `cookie-based authentication`. Điều này cho phép `SPA` có thể thực hiện các yêu cầu đến API một cách an toàn mà không cần phải xây dựng một hệ thống xác thực phức tạp.

- `Quản lý Token`:dễ dàng tạo, thu hồi và quản lý các API token. Mỗi token được liên kết với người dùng, cho phép theo dõi và kiểm soát việc sử dụng API của từng người dùng.

### Khởi tạo sanctum

```sh
php artisan install:api
```

### sử dụng

Khi dùng nó sẽ tạo ra một chỗ để lấy `cookie` token

```txt
GET http://127.0.0.1:8000/sanctum/csrf-cookie
```

Cookie `XSRF-TOKEN` sẽ được gửi từ server và lưu vào trình duyệt hoặc Postman

### ví dụ dùng POST REACTJS

```js
import axios from "axios";

// Bật gửi cookie trong các yêu cầu
axios.defaults.withCredentials = true;

// Gọi /sanctum/csrf-cookie trước khi login
axios.get("http://127.0.0.1:8000/sanctum/csrf-cookie").then(() => {
  // Sau khi lấy CSRF cookie, gửi request POST
  axios
    .post("http://127.0.0.1:8000/api/login", {
      email: "test@example.com",
      password: "password",
    })
    .then((response) => {
      console.log("Login success:", response.data);
    })
    .catch((error) => {
      console.error("Login failed:", error.response.data);
    });
});
```

### Lưu ý phải kiểm trả file config/corf.php

- nếu chưa public

  ```sh
  php artisan config:publish cors
  ```

Ví dụ:

```php
return [
    'paths' => ['api/*', 'login', 'sanctum/csrf-cookie'], // Các endpoint áp dụng CORS
    'allowed_methods' => ['*'], // Cho phép tất cả phương thức HTTP (GET, POST, PUT, DELETE, ...)
    'allowed_origins' => ['http://localhost:3000'], // Domain của frontend (React/Vue)
    'allowed_origins_patterns' => [], // Không dùng pattern phức tạp
    'allowed_headers' => ['*'], // Cho phép tất cả header
    'exposed_headers' => [], // Không expose thêm header nào
    'max_age' => 0, // Không giới hạn thời gian cache của trình duyệt
    'supports_credentials' => true, // Bật để hỗ trợ cookie và CSRF
];
```

### Dùng sanctum để xác thực

Lưu ý nên [đọc thêm token-abilities](https://laravel.com/docs/11.x/sanctum#token-abilities) để thêm xem có quyền sử dụng tính năng đó hay không

- Thêm interface cho Modal

  ```php
  <?php

  namespace App\Models;

  use Illuminate\Auth\Authenticatable;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
  use Laravel\Sanctum\HasApiTokens; // thêm 2 đoạn này

  class Users extends Model  implements AuthenticatableContract
  {
      use Authenticatable, HasApiTokens; // thêm 2 đoạn này
  }
  ```

- Ở controller khởi tạo

  ```php
   public function Login(Request $req){
      if (!empty($req->all())){
          $validate = $req->validate([
              "email" => "required|email|exists:App\Models\Users,email",
              "password" => "required|min:6",
          ]);

          if (Auth::attempt($validate)){
              $token = \auth()->user()->createToken('api-token')->plainTextToken;
              dd($token);
          }
      }
  }
  ```

- Sử dụng đối với reactJS

  ```js
  import axios from "axios";

  // Lấy token từ localStorage (hoặc bất cứ nơi nào bạn lưu trữ token)
  const token = localStorage.getItem("access_token");

  // Cấu hình Axios để thêm header Authorization
  axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

  // Cấu hình Axios gửi cookie cùng với request (nếu cần Sanctum)
  axios.defaults.withCredentials = true;

  // Ví dụ gửi một request đến API
  axios
    .get("http://127.0.0.1:8000/api/user")
    .then((response) => {
      console.log("User data:", response.data);
    })
    .catch((error) => {
      console.error("Error:", error.response.data);
    });
  ```

### Một số lệnh cơ bản

```php
// Tạo token
$token = $user->createToken('api-token-name');
$plainTextToken = $token->plainTextToken;


// Gán quyền
$token = $user->createToken('api-token-name', ['view-profile', 'edit-settings']);
$plainTextToken = $token->plainTextToken;


// kiểm tra quyền
if ($request->user()->tokenCan('view-profile')) {
    return response()->json(['message' => 'You can view the profile']);
} else {
    return response()->json(['message' => 'Access denied'], 403);
}


// Thu hồi quyền (xóa)
$user->tokens()->where('id', $tokenId)->delete(); // xóa 1 bỏ where xóa all


// tokern hiện tại
$currentToken = $request->user()->currentAccessToken();

```
