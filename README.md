<div align="center"> <h1> Cách dùng cơ bản trên Laravel </h1> </div>

- [Cách tạo file cơ bản](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/c%C3%A1ch%20t%E1%BA%A1o%20file%20c%C6%A1%20b%E1%BA%A3n.txt)
- [jquery](#jquery)
- [Tạo ide helper](#sử-dụng-ide-helper)
- [Đặt tên title cho toàn file](#đặt-tên-title-trang)
- [Thêm, Xóa, Sửa](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/CRUD%20Lavarel.txt)
- [Xóa mềm](#xóa-mềm)
- [Một số câu truy vấn Eloquent](#một-số-câu-truy-vấn-eloquent)
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
- [Xử lý giá trị null](#xử-lý-giá-trị-null)
- [Tạo lưu trữ ảnh với Drive trên Laravel qua dự án của Google](#tạo-lưu-trữ-ảnh-với-drive-trên-laravel-qua-dự-án-của-google)
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
## Xử lý giá trị null
 **Nếu trong bảng có hàng tồn tại giá trị null gây nên lỗi thì ta sử dụng `optional` để tránh tình trạng báo lỗi**
***
## Một số câu truy vấn Eloquent
  - `create(giá trị cần thêm)` dùng để thêm dữ liệu
  - `update(giá trị cần sửa)` dùng để update dữ liệu
  - `delete()` hoặc `destroy(Giá trị cần xóa)` để xóa dữ liệu
  - `validated()` dùng để validate dữ liệu khi đã validation dữ liệu
  - `find(giá trị cần tìm)` dùng để tìm kiếm dữ liệu
  - `where(cột cần lấy, giá trị so sánh của cột đó)` dùng để kiểm tra dữ liệu
  - `paginate(số trang)` dùng để phân trang
  - `{{ $users->withQueryString()->links() }}` dùng để nối câu truy vấn để hiện số trang tiếp theo của câu truy vấn
  - `select(giá trị cần lấy)` dùng để lấy dữ liệu, `addselect(giá trị cần lấy)` dùng để lấy thêm dữ liệu
  - `join('tên bảng', 'giá trị 1', 'giá trị 2')` dùng để kết hợp nhiều bảng lại với nhau
  - `when` dùng để thay thế if else. Nó sẽ kiểm tra giá trị cần lấy có tồn tại hay không rồi sau đó kiểm tra lại một lần nữa xem giá trị có trùng với dữ liệu trong database hay không nếu, nếu có thì nó mới lấy
  ```sh
    ->when($request->has(giá trị cần lấy), function($q){
        return $q->where(cột cần lấy, giá trị so sánh);
    })
  ```
  - `latest()` dùng để lấy các giá trị ở cuối hàng
  - `clone()` dùng để tránh bị trùng khi sử dụng nhiều lần `$this->model`
  - `distinct()` dùng để lọc giá trị bị trùng trong databsse
  - `pluck('city')` dùng để lấy giá trị trong cột nào đó thôi, có thể thay thế select 
  ***
## jquery
**submit dữ liệu khi click vào option lựa chọn trong form có chứa select
```sh
$(document).ready(function () {
        $('.select-filter').change(function () {
            $('#form-filter').submit();
        });
    });
```
***
## Xóa mềm
- Khai báo `use SoftDeletes` trong `use Illuminate\Database\Eloquent\SoftDeletes;` ở trong Model
- tạo cột `deleted_at` ở trong sql
- sau đó xóa như bình thường là được vd `$this->model->destroy(ID)`
***
## Tạo lưu trữ ảnh với Drive trên Laravel qua dự án của Google 

B1: Khởi tạo dự án

	Tạo project: https://console.developers.google.com/projectcreate
	
	Tạo credentials: https://console.developers.google.com/apis/credentials

B2: Đăng kí OAuth consent screen trước rồi sau đó. 

	truy cập credentials > Tạo file credentials > OAuth client ID

B3: Đặt tên miền sau đó click vào Authorized redirect URIs 

** khác với đăng nhập bằng tài khoản google chúng ta sẽ đặt điều hướng về oauthplayground 

=> https://developers.google.com/oauthplayground

B4: vào Library > kiếm Google Drive > click vô nó

B5: truy cập link https://developers.google.com/oauthplayground

B6: click vô cài đặt > tích chọn use your own OAuth > nhập mã OAuth khi tạo credentials

B7: kiếm Drive API v3 > chọn ALL > Auth...APIs

B8: đăng nhập tài khoản google tích chọn full quyền

B9: click Exchange auth code for tokens

B10: copy refresh_token bên json

B11: qua drive tạo folder lưu > copy đuôi trên url

**Qua bên laravel**

B1: cài đặt package sau đối với laravel 9

```sh
composer require masbug/flysystem-google-drive-ext
```

B2:Tạo file GoogleDriveServiceProvider trong `app > Provider` sau đó thêm dữ liệu sau
```sh
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            Storage::extend('google', function($app, $config) {
                $options = [];
                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }
                $client = new \Google\Client();

                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                $driver  = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch(\Exception $e) {
            // your exception handling logic
        }
    }
}

```
B3:  thêm dữ liệu sau vào `providers` trong file `config/app.php`
```sh
App\Providers\GoogleDriveServiceProvider::class,
```

B4: copy file sau dán vào `config/filesystems.php`
```sh
'google' => [
        'driver' => 'google',
        'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
        'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
        'folder' => env('GOOGLE_DRIVE_FOLDER'),
        //'teamDriveId' => env('GOOGLE_DRIVE_TEAM_DRIVE_ID'),
    ],
```
B5: Cập nhật file .env Thêm ClientID, ClientSecret, RefreshToken vừa thực hiện các bước ở trên vào file env

- **Để đẩy file lên drive ta dùng lệnh `Storage::disk('google')->putFile('file, nội dung, sử dụng public nếu muốn công khai file)`**
- **Để lấy toàn bộ danh sách trên drive về ta dùng lệnh `collect(Storage::disk('google')->listContents('/', có lấy thư mục con hay không (true or false)))` lưu ý nên sử dụng mảng để truy xuất dữ liệu `array[1][path]`**

**Ví dụ code xử lý vụ đăng tải file lên sau đó lấy link về  **
```sh
Route::post('test', function (Request $request) {
    $path = (Storage::disk('google')->putFile($request->get('name'), $request->file('avatar'), 'public'));

    $dir = '/';
    $recursive = true; // Có lấy file trong các thư mục con không?
    $contents = collect(Storage::disk('google')->listContents($dir, $recursive));
    $res = $contents->where('path', '=', $path)->first();

    dd('https://drive.google.com/file/d/'.$res['extra_metadata']['id'].'/view');
})->name('put_image');
```

- **Để xóa link trên Drive ra dùng `Storage::disk('google')->delete($res['path'])` **
Vd: xóa path được lấy về qua request
```sh
Route::get('delete', function (Request $request){
    $path = $request->get('path');
    $dir = '/';
    $recursive = true; // Có lấy file trong các thư mục con không?
    $contents = collect(Storage::disk('google')->listContents($dir, $recursive));//lấy tất cả danh sách về
    $res = $contents->where('path', '=', $path)->first();

    Storage::disk('google')->delete($res['path']);
});
```
***
