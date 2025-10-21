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