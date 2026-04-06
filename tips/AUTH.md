# cơ chế

Sau này, khi session hết hạn (cookie laravel_session mất),
Laravel vẫn thấy cookie remember_web_xxx tồn tại,
→ Nó tự động login lại user đó, dựa vào remember_token trong DB.
Tức là:

“Không cần đăng nhập lại, vì tôi đã nhớ bạn.”

# migration

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email');
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

# ghi rememberToken

```php
$validate = Validator::make($data, [
    'email' => 'required|string|email|max:255',
    'password' => 'required|string|min:6',
]);
if (!Auth::attempt($validate->validated(), remember: true)){
    return throw new \Exception("Invalid Credentials");
}
```

# xóa remember

```php
// chỗ logout
$user->setRememberToken(null);
```

# ghi session 1 ngày khi login

```php
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

protected function sendLoginResponse(Request $request)
{
    $request->session()->regenerate();

    if ($request->filled('remember')) {
        Cookie::queue(
            Auth::getRecallerName(),
            Cookie::get(Auth::getRecallerName()),
            60 * 24 // 1 ngày
        );
    }

    return redirect()->intended($this->redirectPath());
}
/*
Auth::getRecallerName()     → tên cookie, ví dụ remember_web_9b3d9d5f...
Cookie::get(...)            → lấy lại giá trị token đã Laravel tạo
60 * 24                     → thời gian sống mới (1 ngày)
Cookie::queue(...)          → Laravel sẽ ghi đè cookie đó trong response
*/
```

# redirect khi middleware bắt

## tạo middleware check xem có login chưa

ghi nhớ trang hiện tại cần tới

```php
<?php

namespace App\Http\Middleware;

use App\Trait\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            session(['url.intended' => url()->current()]);
            return redirect()->route('user.login');
        }

        if (config("define.ROLE_LIST")[$request->user()->role] !== "ADMIN") {
            auth()->logout();
            return redirect()->route('user.login    ')->with("error", "Bạn không có quyền truy cập");
        }
        return $next($request);
    }
}
```

## redirect tới session lưu ban nãy

```php
public function responseValidateFrom($url, $status = true , $msg = '')
{
    $url = session()->pull('url.intended', route($url)); // lấy xong xóa
    return redirect($url)->with($status ? $this->STATUS_SUCCESS : $this->STATUS_ERROR, $msg);
}
```