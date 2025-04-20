Laravel Passport là một package chính thức của Laravel để triển khai OAuth2 server, cho phép xác thực API một cách bảo mật với các loại token như:

- Password Grant
- Personal Access Token
- Client Credentials
- Authorization Code (ít phổ biến trong API server riêng)

# Cài đặt và cấu hình cơ bản

```sh
php artisan install:api --passport
```

Lệnh này sẽ:​

- Tạo file `routes/api.php` nếu chưa có.

- Chạy các `migration` cần thiết.

- Tạo các khóa mã hóa cho `Passport`.

- Đăng ký các `route` cần thiết cho `OAuth2`.​

# Cấu hình `cho modal User hoặc modal khác nếu đã chỉnh` thêm trait `HasApiTokens`:​

```php
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // ...
}
```

# Cấu hình Guard cho API

```sh
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

# Tạo API Routes, thêm middleware

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
```

# Tạo Personal Access Token

```php
$user = User::find(1);
$token = $user->createToken('Token Name')->accessToken;
```

Sau đó, sử dụng token này để xác thực các request API bằng cách thêm vào header: `Authorization: Bearer {token}`


# Triển khai lên môi trường production

```sh
php artisan passport:keys --force
```


# Các mô tả model

- `oauth_access_tokens:` 
   - Chức năng: Lưu trữ các access token được cấp cho người dùng hoặc client.
   - Sử dụng: Xác thực các yêu cầu API bằng cách kiểm tra token trong bảng này.
  
- `oauth_clients`
  - Chức năng: Lưu trữ thông tin về các client (ứng dụng) có thể yêu cầu token từ server.
  - Sử dụng: Xác định các ứng dụng có quyền yêu cầu token và quản lý các loại client khác nhau.
- `oauth_refresh_tokens`
  - Chức năng: Lưu trữ các refresh token cho phép client lấy access token mới sau khi token cũ hết hạn.
  - Sử dụng: Cho phép client duy trì phiên làm việc mà không cần người dùng đăng nhập lại.​
- `oauth_auth_codes`
  - Chức năng: Lưu trữ các authorization code tạm thời được cấp trong quá trình xác thực.
  - Sử dụng: Trong quá trình xác thực Authorization Code Grant, trước khi cấp access token.
- `oauth_personal_access_clients` 
    - Chức năng: Lưu trữ thông tin về các personal access client, thường được sử dụng để cấp token cho người dùng mà không cần qua quá trình xác thực đầy đủ.
    - Sử dụng: Cấp token trực tiếp cho người dùng, thường được sử dụng trong các ứng dụng nội bộ hoặc khi không cần xác thực phức tạp.​


# Một số  hàm cơ bản

## Tạo token

```php
$token = $user->createToken('MyAppToken')->accessToken;
```

## Thêm thời gian hến hạn đối với các nhân

```php
Passport::personalAccessTokensExpireIn(Carbon::now()->addMonths(6)); // 6 tháng
Passport::personalAccessTokensExpireIn(Carbon::now()->addHours(2)); // 2 giờ
```

- Nếu để  trong `boot` của `service` thì sẽ set cho toàn bộ user

```php
Passport::tokensExpireIn($time);
```

## Lấy thông tin người dùng qua token

```php
$request->user()
```

## Lấy đối tượng token hiện tại của người dùng.

```php
$request->user()->token() // lấy token
$request->user()->token()->revoke(); // thu hồi token
```

## Định nghĩa các scope (quyền hạn) cho token.

```php
Passport::tokensCan([
    'place-orders' => 'Place orders',
    'check-status' => 'Check order status',
]);
```

## kiểm tra quyền

```php
$request->user()->tokenCan('scope-name')
```


# Ví dụ khởi tạo user


```php
oute::get('/register', function (){
    $user = \App\Models\User::create([
        "name" => "dev",
        "email" => "dev@yopmail.com",
        "password" => bcrypt("123"),
    ]);

    // Tạo oauth_clients
    $client_oauth = \Laravel\Passport\Client::create([
        "user_id" => $user->id,
        "name" => "dev",
        "secret" => \Illuminate\Support\Str::random(40),
        "redirect" => "http://localhost:8000",
        "personal_access_client" => 1,
        "password_client" => 0,
        "revoked" => 0,
    ])->first();

    // tạo oauth_personal_access_clients
    $client = \Laravel\Passport\PersonalAccessClient::query()->create([
        "client_id" => $client_oauth->id,
    ]);

    $res = [];

    // tạo token oauth_access_tokens
    $res['token'] = $user->createToken($user->name)->accessToken;
    $res['client_id'] = $client->id;
    $res['client_secret'] = $client_oauth->secret;
    return response()->json($res);
});
```

# Thu hồi token

```php

Route::get('/logout', function (Request $request) {
    $accessToken = $request->user()->token();
    $tokenRepository = app(\Laravel\Passport\TokenRepository::class);
    $refreshTokenRepository = app(\Laravel\Passport\RefreshTokenRepository::class);
    $tokenRepository->revokeAccessToken($accessToken->id);
    $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($accessToken->id);

    // thu hồi tất cả token
    // foreach ($user->tokens as $token) {
    //      $tokenRepository->revokeAccessToken($token->id);
    // }
    return response()->json(['message' => 'Đăng xuất thành công']);
})->middleware('auth:api');
```