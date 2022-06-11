<div align="center"> <h1> Cách dùng cơ bản trên Laravel </h1> </div>

- [Cách tạo file cơ bản](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/c%C3%A1ch%20t%E1%BA%A1o%20file%20c%C6%A1%20b%E1%BA%A3n.txt)
- [Tạo ide helper](#sử-dụng-ide-helper)
- [Đặt tên title cho toàn file](#đặt-tên-title-trang)
- [Thêm, Xóa, Sửa](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/CRUD%20Lavarel.txt)
- [Sử dụng templet](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/S%E1%BB%AD%20d%E1%BB%A5ng%20templet.txt)
- [Sử dụng Validation](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/S%E1%BB%AD%20d%E1%BB%A5ng%20Validation.txt)
- [tạo bảng Datathable](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/t%E1%BA%A1o%20b%E1%BA%A3ng%20Datathable.txt)
- [select 2](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/select%202.txt)
- [enum](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/enum.txt)
- [Chỉnh sửa migration](#chỉnh-sửa-lại-migration-đã-tồn-tại-hoặc-đã-chạy-trên-hệ-thống-thêm-dữ-liệu-trong-sql-)
- [faker & seeder](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/faker%20%26%20seeder.txt)
- [relationShip](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/Relationship%20.txt)
- [up load ảnh](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/upload%20%E1%BA%A3nh.txt)
- [middleWare & session](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/middleware%26%20session.txt)
- [Gửi mail](#gửi-mail)
- [Đăng nhập từ mạng xã hội, bên thứ 3](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/Socialite%20%C4%91%C4%83ng%20nh%E1%BA%ADp%20%20b%E1%BA%B1ng%20m%E1%BA%A1ng%20x%C3%A3%20h%E1%BB%99i.txt)
- [Tạo API đăng nhập ở bên mạng xã hội](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/c%C3%A1ch%20t%E1%BA%A1o%20file%20%C4%91%C4%83ng%20nh%E1%BA%ADp%20b%E1%BA%B1ng%20facebook.txt)
- [Sử dụng auth](#sử-dụng-auth-trong-model)
- [Sử dụng @Auth, @Guest](#sử-dụng-auth-và-guest)
- [Up hosting](#up-host)
***
## **Sử dụng ide helper**
  Chạy lần lượt các lệnh sau để cài đặt
  ```sh
  composer require --dev barryvdh/laravel-ide-helper
  ```
  ```sh
  php artisan ide-helper:models "App\Models\[model cần hỗ trợ]" -w
  ```
## **Mã hóa (hash)**
 ```sh
   - Hash::make(value) //dùng để mã hóa
   - Hash::check(value, hashvalue)// dùng để kiểm tra mã hóa
   ```
   ___
## **Gửi mail**
  * [gửi mail bằng Observer & Notification](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/g%E1%BB%ADi%20mail%20Observer%20%26%20Notification.txt)
  * [gửi mail bằng event & listener](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/event%20%26%20listener.txt)

***

## **Up host**

- Kéo cả thư mục Larave đư lên host sau đó thêm thư mục .htaccess ngang cấp với public để ẩn chữ pubilic trên url
```sh
   <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ /public/$1 [L,QSA]
   </IfModule>
 ```
 
 ***
 
## **Chỉnh sửa lại migration đã tồn tại hoặc đã chạy trên hệ thống (Thêm dữ liệu trong SQL )**
  * Tạo mới một migration ```php artisan make:migration alter_table```
  * Sử dụng cú pháp sau để thực thi
    ```sh
    if (!Schema:hasColumn('tên bảng', 'Tên cột')){
      //dữ liệu có sẵn khi tạo migration và chỉnh sửa 
      Schema::table('tên bảng', function (Blueprint $table)
       {
                $table-> 
      });
    }
    ```
***
## **Đặt tên title trang**
  * Nên tên lại trong file `env` hoặc `config > app > name` sau đó đổi tên trong filename
  * Để dùng thì ta dùng thì ta gọi bằng cách `config('app.name')`
***
## Sử dụng auth trong model
 * Copy toàn bộ dữ liệu sau dán vào model cần dùng thì mới sử dụng auth được
```sh
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
}
```
 * Muốn lưu lại các dữ liệu đã nhập vào form có sử dụng Auth ta sẽ dùng lệnh
 ```sh
 if (auth()->check()){
  // vd: kiểm tra id dể update 
 }
 else{
  //
 }
 ```

## Sử dụng @auth và @guest
 * Dùng để kiểm tra xem khách hàng đã đăng nhập hay chưa, sử dụng trong blade khi có sài ` Auth::login($user); `
 vd:
 ```sh
 @auth
   // nếu đã đăng nhập thì xử lý gì
   // nếu muốn lấy giá gị đã truyền khi sử dụng  Auth::login($user) thì Auth()->user()->giá trị đã lưu trong biến user
 @endauth
 @guest
   // nếu chưa đăng nhập thì xử lý gì
 @endguest
 ```
***
