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


# Ví dụ khởi tạo user customer


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

# Thu hồi token customer

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

# Ví dụ thực tế  với code 

sau bước cài đặt và khai báo `api` trong code, dùng `keys` triền khai môi trường. và chạy `migrate` để  lấy db

## khai báo người dùng đầu tiên cho app

```sh
php artisan passport:client --password
```

sau đó khai báo vào `env`

```env
PASSPORT_PASSWORD_CLIENT_ID=
PASSPORT_PASSWORD_SECRET=
```

## Tại route api


```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/refresh', [AuthController::class, 'refreshToken']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

});
```

## Tạo controller

```sh
php artisan make:controller Api/AuthController
```

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * User registration
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $userData = $request->validated();

        $userData['email_verified_at'] = now();
        $user = User::create($userData);

        $response = Http::post(env('APP_URL') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
            'username' => $userData['email'],
            'password' => $userData['password'],
            'scope' => '',
        ]);

        $user['token'] = $response->json();

        return response()->json([
            'success' => true,
            'statusCode' => 201,
            'message' => 'User has been registered successfully.',
            'data' => $user,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            $response = Http::post(env('APP_URL') . '/oauth/token', [
                'grant_type' => 'password',
                'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
                'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '',
            ]);

            $user['token'] = $response->json();

            return response()->json([
                'success' => true,
                'statusCode' => 200,
                'message' => 'User has been logged successfully.',
                'data' => $user,
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'statusCode' => 401,
                'message' => 'Unauthorized.',
                'errors' => 'Unauthorized',
            ], 401);
        }

    }

    /**
     * Login user
     *
     * @param  LoginRequest  $request
     */
    public function me(): JsonResponse
    {

        $user = auth()->user();

        return response()->json([
            'success' => true,
            'statusCode' => 200,
            'message' => 'Authenticated use info.',
            'data' => $user,
        ], 200);
    }

    /**
     * refresh token
     *
     * @return void
     */
    public function refreshToken(RefreshTokenRequest $request): JsonResponse
    {
        $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
            'scope' => '',
        ]);

        return response()->json([
            'success' => true,
            'statusCode' => 200,
            'message' => 'Refreshed token.',
            'data' => $response->json(),
        ], 200);
    }

    /**
     * Logout
     */
    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'statusCode' => 204,
            'message' => 'Logged out successfully.',
        ], 204);
    }
}
```

## Tạo request

### RegisterRequest

```sh
 php artisan make:request RegisterRequest
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
        ];
    }
}
```

### LoginRequest

```sh
 php artisan make:request LoginRequest
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'email|required',
            'password' => 'required',
        ];
    }
}
```

### RefreshTokenRequest

```sh
 php artisan make:request RefreshTokenRequest
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefreshTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'refresh_token' => ['required', 'string'],
        ];
    }
}
```

# Thêm vào AppServiceProvider


```php
    public function boot(): void
    {
        // ...
        //  Thiết lập **thời hạn hết hạn** của access token là **15 ngày** kể từ lúc tạo
        Passport::tokensExpireIn(now()->addDays(15));

        // Thiết lập thời hạn hết hạn của refresh token là 30 ngày.
        Passport::refreshTokensExpireIn(now()->addDays(30));
        
        // Thiết lập thời hạn hết hạn của **Personal Access Token** là **6 tháng**
        Passport::personalAccessTokensExpireIn(now()->addMonths(6)); 
        
        // Cho phép người dùng login bằng email/username và password để lấy token (grant type: password)
        Passport::enablePasswordGrant(); 
    }

```