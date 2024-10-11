# Cấu hình

- Với `model` để dùng được **AUTH** phải dùng thêm 

    - `use Illuminate\Auth\Authenticatable;`
    - `use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;`
    ```php
    <?php

    namespace App\Models;

    use Illuminate\Auth\Authenticatable;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;


    class Users extends Table implements AuthenticatableContract
    {
        use HasFactory;
        use Authenticatable;

        public static $key_cache = "user_";
        public static $condition = ["del_flag" => UNDEL, "active" => ACTIVE];
        protected $fillable = ["full_name", "birth_date", "email", "password", "token", "position"];

    }
    ```

-   Muốn chỉnh lại Table khi dùng Auth thì vào `config > Auth`

    vd:

    ```php
        'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\Login::class, // chỉnh model
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],
    ```

# Sử dụng

## Tạo middeware check login

```sh
php artisan make:middleware name
```

Vào cấu hình cho nó

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()){
            return redirect()->route("login");
        }

        if (Auth::check()){
            return $next($request);
        }

        return redirect()->back();
    }
}
```

## Khai báo trên roter

```php
Route::group(['prefix' => '/users', "middleware" => CheckRole::class], function(){
    Route::get('/',[UsersController::class, 'index'] )->name("users.index");
    Route::get('/create',[UsersController::class, 'create'] )->name("users.create");
    Route::post('/store',[UsersController::class, 'store'] )->name("users.store");
    Route::get('/edit/{user}',[UsersController::class, 'edit'] )->name("users.edit");
    Route::put('/update/{user}',[UsersController::class, 'update'] )->name("users.update");
    Route::get('/delete/{user}',[UsersController::class, 'delete'] )->name("users.delete");
});
```


## Sử dụng Police với AUTH

- Khởi tạo `police`

    ```sh
    php artisan make:policy UserPolicy --model=User
    ```
    
- Cấu trúc của Policy: Policy chứa các phương thức đại diện cho các hành động mà người dùng có thể thực hiện. Mỗi phương thức trả về true nếu người dùng có quyền thực hiện hành động, và false nếu không có quyền.

    ví dụ:

    ```php
    namespace App\Policies;

    use App\Models\User;
    use Illuminate\Auth\Access\HandlesAuthorization;

    class UserPolicy
    {
        use HandlesAuthorization;

        /**
         * Determine whether the user can view any users.
         */
        public function viewAny(User $user)
        {
            // Ví dụ: Cho phép tất cả người dùng xem danh sách users
            return true;
        }

        /**
         * Determine whether the user can view the model.
         */
        public function view(User $user, User $model)
        {
            // Ví dụ: Người dùng có thể xem thông tin nếu là chính họ hoặc là admin
            return $user->id === $model->id || $user->position === 'admin';
        }

        /**
         * Determine whether the user can create users.
         */
        public function create(User $user)
        {
            // Chỉ admin mới có thể tạo user mới
            return $user->position === 'admin';
        }

        /**
         * Determine whether the user can update the model.
         */
        public function update(User $user, User $model)
        {
            // Chỉ cho phép admin hoặc chính người dùng đó sửa thông tin
            return $user->id === $model->id || $user->position === 'admin';
        }

        /**
         * Determine whether the user can delete the model.
         */
        public function delete(User $user, User $model)
        {
            // Chỉ cho phép admin xóa người dùng
            return $user->position === 'admin';
        }
    }
    ```

    có thể thêm hàm mới

    ```php

    public function before(User $user, $ability)
    {
        if ($user->position === 'super_admin') {
            return true; // Super admin có quyền thực hiện tất cả hành động
        }
    }

    public function approve(User $user, Courses $courses): bool
    {
        // Ví dụ: Chỉ admin hoặc người tạo course mới có quyền phê duyệt
        return $user->position === 'admin' || $user->id === $courses->created_by;
    }
    ```

- Đăng ký Policy: Mở file `app/Providers/AuthServiceProvider.php` và thêm trong `register`

    ```php
    protected $register = [
        // Đăng ký policy với model User
        User::class => UserPolicy::class,
    ];
    ```

- Sử dụng Policy trong Controller

    ```php
    public function update(Request $request, User $user)
    {
        // Sử dụng policy để kiểm tra quyền trước khi thực hiện cập nhật
        $this->authorize('update', $user);
        
        // ... 
    }

    ```
-  Sử dụng Policy trong Blade

    ```php
    @can('update', $user)
    <a href="{{ route('users.edit', $user) }}">Chỉnh sửa</a>
    @endcan

    @can('delete', $user)
        <form action="{{ route('users.destroy', $user) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">Xóa</button>
        </form>
    @endcan

    ```



# Một Số hàm cơ bản

### 1. **Auth::attempt()**
- **Mục đích**: Xác thực người dùng bằng cách kiểm tra email và mật khẩu.

- **Ví dụ**:
  ```php
  if (Auth::attempt(['email' => \$request->email, 'password' => \$request->password])) {
      // Đăng nhập thành công
      return redirect()->intended('dashboard');
  }
  // Auth::attempt($credentials, $remember)
  ```

### 2. **Auth::check()**
- **Mục đích**: Kiểm tra xem người dùng hiện tại đã đăng nhập hay chưa.

- **Ví dụ**:
  ```php
  if (Auth::check()) {
      // Người dùng đã đăng nhập
  } else {
      // Người dùng chưa đăng nhập
  }
  ```

### 3. **Auth::user()**
- **Mục đích**: Lấy thông tin của người dùng đã đăng nhập.

- **Ví dụ**:
  ```php
  \$user = Auth::user();
  echo \$user->name; // Hiển thị tên người dùng đã đăng nhập
  ```

### 4. **Auth::logout()**
- **Mục đích**: Đăng xuất người dùng hiện tại.

- **Ví dụ**:
  ```php
  Auth::logout();
  return redirect('/login');
  ```

### 5. **Auth::id()**
- **Mục đích**: Lấy ID của người dùng đã đăng nhập.

- **Ví dụ**:
  ```php
  \$userId = Auth::id();
  echo \$userId; // Hiển thị ID của người dùng đã đăng nhập
  ```

### 6. **Auth::viaRemember()**
- **Mục đích**: Kiểm tra xem người dùng có đăng nhập qua cookie "Remember me" hay không.

- **Ví dụ**:
  ```php
  if (Auth::viaRemember()) {
      // Người dùng đăng nhập qua "Remember me"
  }
  ```

### 7. **Auth::once()**
- **Mục đích**: Xác thực người dùng một lần mà không lưu session.

- **Ví dụ**:
  ```php
  if (Auth::once(['email' => \$request->email, 'password' => \$request->password])) {
      // Xác thực thành công nhưng không lưu session
  }
  ```

### 8. **Auth::guard()**
- **Mục đích**: Sử dụng các guard khác nhau để xác thực người dùng.

- **Ví dụ**:
  ```php
  \$user = Auth::guard('admin')->user(); // Sử dụng guard "admin"
  ```

    
    ```php
    @auth
    // nếu đã đăng nhập thì xử lý gì
    // nếu muốn lấy giá gị đã truyền khi sử dụng  Auth::login($user) thì Auth()->user()->giá trị đã lưu trong biến user
    @endauth
    @guest
    // nếu chưa đăng nhập thì xử lý gì
    @endguest
    ```


### 9. **Auth::validate()**
- **Mục đích**: Kiểm tra thông tin xác thực của người dùng mà không đăng nhập.

- **Ví dụ**:
  ```php
  if (Auth::validate(['email' => \$request->email, 'password' => \$request->password])) {
      // Thông tin xác thực chính xác
  }
  ```

### 10. **Auth::attemptWhen()**
- **Mục đích**: Xác thực người dùng với các điều kiện bổ sung.

- **Ví dụ**:
  ```php
  Auth::attemptWhen(
      ['email' => \$request->email, 'password' => \$request->password],
      function (\$user) {
          return \$user->is_active; // Chỉ xác thực nếu tài khoản đang hoạt động
      }
  );
  ```

### 11. **Auth::setUser()**
- **Mục đích**: Thủ công đăng nhập một người dùng vào hệ thống.

- **Ví dụ**:
  ```php
  Auth::setUser(\$user); // Đăng nhập người dùng thủ công
  ```

### 12. **Auth::shouldUse()**
- **Mục đích**: Chỉ định guard mặc định cần sử dụng cho yêu cầu hiện tại.

- **Ví dụ**:
  ```php
  Auth::shouldUse('admin');
  ```

### 13. **Auth::onceUsingId()**
- **Mục đích**: Đăng nhập một người dùng bằng ID chỉ một lần mà không lưu session.

- **Ví dụ**:
  ```php
  Auth::onceUsingId(1); // Đăng nhập người dùng có ID 1 một lần
  ```
