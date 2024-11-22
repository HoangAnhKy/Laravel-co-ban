# JWTAuth

## 1. Lấy Token từ Header Yêu Cầu

```php
JWTAuth::getToken();
```

**Mô tả**: Lấy **JWT token** từ request hiện tại (thường từ **header** của HTTP request).

---

## 2. Giải mã Token và Lấy Payload

```php
$payload = JWTAuth::getPayload($token);
```

**Mô tả**: Giải mã token và lấy payload chứa thông tin được mã hóa (như user ID hoặc thời gian hết hạn).

---

## 3. Tạo Token cho Người Dùng

```php
$token = JWTAuth::fromUser($user);
```

**Mô tả**: Tạo JWT token dựa trên thông tin của người dùng (instance của User model).

---

## 4. Lấy Người Dùng từ Token

```php
$user = JWTAuth::parseToken()->authenticate();
```

**Mô tả**: Phân tích token và lấy thông tin người dùng đã được xác thực.

---

## 5. Vô Hiệu Hóa Token (Logout)

```php
JWTAuth::invalidate($token);
```

**Mô tả**: Vô hiệu hóa token để người dùng không thể sử dụng nó nữa.

---

## 6. Làm Mới Token (Refresh)

```php
$newToken = JWTAuth::refresh($token);
```

**Mô tả**: Làm mới một token đã hết hạn mà không cần người dùng đăng nhập lại.

---

## 7. Kiểm Tra Token Có Hợp Lệ Không

```php
$valid = JWTAuth::parseToken()->check();
```

**Mô tả**: Kiểm tra xem token hiện tại có hợp lệ không.

---

## 8. Kiểm Tra Token Đã Hết Hạn Chưa

```php
$payload = JWTAuth::parseToken()->getPayload();

$issuedAt = $payload->get('iat'); // Thời gian phát hành
$expiresAt = $payload->get('exp'); // Thời gian hết hạn
```

**Mô tả**: Kiểm tra xem token đã hết hạn hay chưa.

--- 

## 9. Thời gian tồn tại cho token

```php
$factory = JWTAuth::factory();
$factory->setTTL(120); // Đặt thời gian sống của token là 120 phút
$factory->getTTL();     // trả về thời gian sống của token
```

---

## Ví dụ: Tạo AuthController với JWTAuth

- Thêm Guard cho JWT vào `config/auth.php`

    ```php
    'guards' => [
        // ...

        'api' => [
            'driver' => 'jwt', // Thay thế "session" thành "jwt"
            'provider' => 'users',
        ],
    ],
    ```

- Cài đặt Package JWT

    ```sh
    composer require tymon/jwt-auth
    ```

- xuất file cấu hình JWT:

    ```sh
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    ```
- secret key cho JWT trong `env`

    ```sh
    php artisan jwt:secret
    ```

- modal
    - Thêm 2 function `getJWTIdentifier` và `getJWTCustomClaims`

    - thêm 2 interface `JWTSubject` và `AuthenticatableContract`

    - thêm use `Authenticatable`

    ```php
    <?php

    namespace App\Models;

    use Illuminate\Auth\Authenticatable;
    use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
    use Tymon\JWTAuth\Contracts\JWTSubject;

    class Users extends Table implements JWTSubject, AuthenticatableContract
    {
        use Authenticatable;
    
        public function getJWTIdentifier()
        {
            return $this->getKey(); // Trả về ID của người dùng
        }
        public function getJWTCustomClaims()
        {
            return []; // Trả về mảng rỗng nếu không cần thêm claims
        }

    }

    ```

- controller

    ```php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Tymon\JWTAuth\Facades\JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;
    use Illuminate\Support\Facades\Auth;

    class AuthController extends Controller
    {
        public function login(Request $request)
        {
            $credentials = $request->only('email', 'password');

            try {
                if (!$token = Auth::guard('api')->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'Could not create token'], 500);
            }

            return response()->json(['token' => $token]);
        }

        public function logout()
        {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(['message' => 'Logged out successfully']);
        }

        public function getUser(Request $request)
        {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($user);
        }

        public function refresh()
        {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return response()->json(['token' => $newToken]);
        }
    }
    ```

---

## Tóm tắt Các Phương thức của `JWTAuth`

**Lưu ý** tất cả phải cài middleware `auth:guard` đó

| **Phương thức**                | **Mô tả**                           |
| ------------------------------ | ----------------------------------- |
| `getToken()`                   | Lấy token từ request.               |
| `getPayload($token)`           | Lấy payload từ token.               |
| `fromUser($user)`              | Tạo token từ người dùng.            |
| `parseToken()->authenticate()` | Lấy người dùng từ token.            |
| `invalidate($token)`           | Vô hiệu hóa token (logout).         |
| `refresh($token)`              | Làm mới token đã hết hạn.           |
| `parseToken()->check()`        | Kiểm tra xem token có hợp lệ không. |