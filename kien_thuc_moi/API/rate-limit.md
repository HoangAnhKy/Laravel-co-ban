# Giới thiệu

`Rate limit`: Giới hạn tần suất gọi API/request trong một khoảng thời gian.

Mục đích:
- Chống spam request (DDoS, brute force login).
- Bảo vệ server không bị quá tải.
- Giữ trải nghiệm ổn định cho tất cả user.

Ví dụ: 

```php
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/user', function () {
        return "User info";
    });
});
```
# Các hàm hỗ trợ 

## Giới hạn theo số request / thời gian
- `Limit::perMinute(3)` → 3 request mỗi phút.
- `Limit::perSecond(10)` → 10 request mỗi giây (Laravel 11 mới thêm).
- `Limit::perHour(100)` → 100 request mỗi giờ.
- `Limit::perDay(1000)` → 1000 request mỗi ngày.

## Đặt key định danh (ai bị giới hạn)
- `->by($request->ip())` → theo IP.
- `->by(optional($request->user())->id ?: $request->ip()) `→ nếu login thì theo user ID, nếu chưa thì theo IP.

## Thay đổi thông báo khi bị chặn

```php
Limit::perMinute(3)->response(function () {
    return response()->json([
        'message' => 'Bạn gọi API quá nhiều lần rồi, thử lại sau 1 phút!',
    ], 429);
});
```

# nâng cao hơn (RateLimiter API)

Trong `app/Providers/RouteServiceProvider.php` thêm:
```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

public function boot()
{
    $this->configureRateLimiting();

    parent::boot();
}

protected function configureRateLimiting()
{
    RateLimiter::for('login', function ($request) {
        return Limit::perMinute(3)->by($request->ip());
    });
}
```
Sử dụng lại middleware giới hạn api đó trong `route/web`
```php
// route
Route::middleware('throttle:login')->post('/login', function () {
    return response()->json(['message' => 'Custom login limit']);
});
```
test để thấy mỗi IP chỉ được 3 lần / phút.

## Không áp dụng cho một số user (ví dụ admin)

```php
RateLimiter::for('api', function ($request) {
    if ($request->user()?->isAdmin()) {
        return Limit::none(); // admin không bị giới hạn
    }
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```
## Trả về nhiều limit cho cùng 1 route

```php
RateLimiter::for('uploads', function ($request) {
    return [
        Limit::perMinute(5)->by($request->ip()),   // 5 request/phút
        Limit::perDay(100)->by($request->ip()),    // 100 request/ngày
    ];
});
```

## Clear số  lần gọi

```php
RateLimiter::clear($key);
```

## Kiểm tra & ghi nhận số lần gọi

```php
// ngoài middleware
$key = 'login:' . $request->ip();

// kiểm tra có vượt ngưỡng không.
if (RateLimiter::tooManyAttempts($key, 5)) {
    return response()->json(['message' => 'Too many attempts'], 429);
}

// ghi nhận 1 lần gọi
RateLimiter::hit($key, 60); // 60 giây decay
