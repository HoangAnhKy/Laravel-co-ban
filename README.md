<div align="center"> <h1> Cách dùng cơ bản trên Laravel </h1> </div>

-   [Cách tạo file cơ bản](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/c%C3%A1ch%20t%E1%BA%A1o%20file%20c%C6%A1%20b%E1%BA%A3n.txt)
-   [jquery](#jquery)
-   [Tạo ide helper](#sử-dụng-ide-helper)
-   [Đặt tên title cho toàn file](#đặt-tên-title-trang)
-   [Thêm, Xóa, Sửa](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/CRUD%20Lavarel.txt)
-   [Phân trang](#phân-trang)
-   [Xóa mềm](#xóa-mềm)
-   [Một số câu truy vấn Eloquent](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/kien_thuc_moi/Eloquent)
-   [Sử dụng templet](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/S%E1%BB%AD%20d%E1%BB%A5ng%20templet.txt)
-   [Sử dụng Validation](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/S%E1%BB%AD%20d%E1%BB%A5ng%20Validation.txt)
-   [tạo bảng Datathable](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/t%E1%BA%A1o%20b%E1%BA%A3ng%20Datathable.txt)
-   [select 2](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/select%202.txt)
-   [enum](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/enum.txt)
-   [Chỉnh sửa migration](#chỉnh-sửa-lại-migration-đã-tồn-tại-hoặc-đã-chạy-trên-hệ-thống-thêm-dữ-liệu-trong-sql-)
-   [faker & seeder](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/faker%20%26%20seeder.txt)
-   [relationShip](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/Relationship%20.txt)
-   [up load ảnh](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/upload%20%E1%BA%A3nh.txt)
-   [middleWare & session](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/middleware%26%20session.txt)
-   [Gửi mail]()
    -   [Gửi mail bằng Observer & Notification](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/g%E1%BB%ADi%20mail%20Observer%20%26%20Notification.txt)
    -   [Gửi mail bằng event & listener](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/event%20%26%20listener.txt)
    -   [Gửi mail bằng API Google](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/mail.md)
    
    -   [Gửi mailable](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/kien_thuc_moi/Mailable.md)
-   [Đăng nhập từ mạng xã hội, bên thứ 3](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/Socialite%20%C4%91%C4%83ng%20nh%E1%BA%ADp%20%20b%E1%BA%B1ng%20m%E1%BA%A1ng%20x%C3%A3%20h%E1%BB%99i.txt)
-   [Tạo API đăng nhập ở bên mạng xã hội](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/c%C3%A1ch%20t%E1%BA%A1o%20file%20%C4%91%C4%83ng%20nh%E1%BA%ADp%20b%E1%BA%B1ng%20facebook.txt)
-   [Sử dụng auth](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/kien_thuc_moi/AUTH.md)
-   [Up hosting](#up-host)
-   [Xử lý giá trị null](#xử-lý-giá-trị-null)
-   [Tạo lưu trữ ảnh với Drive trên Laravel qua dự án của Google](#tạo-lưu-trữ-ảnh-với-drive-trên-laravel-qua-dự-án-của-google)
    -   [Các thao tác bên google cloud](#các-thao-tác-bên-google-cloud)
    -   [Các thao tác cần thực hiện bên laravel](#các-thao-tác-cần-thực-hiện-bên-laravel)
    -   [Một số lưu ý khi thao tác](#một-số-lưu-ý-khi-thao-tác)
    -   [Demo thực tế uploadfile và lưu dữ liệu về data](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/PostIMG.zip)
-   [Laravel execl](#import-laravel-excel)
-   [Laravel with reactjs](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/reactjs%20with%20laravel/Reactjs%20with%20Laravel.md)
    -   [demo](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/reactjs%20with%20laravel/laravel-vite-reactjs.zip)
-   [kết nối 2 database](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/connect%202%20database.md)
-   [Unzip](#unzip-file)
-   [Memcached](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/kien_thuc_moi/memcache.md)
-   [Queue](https://github.com/HoangAnhKy/Laravel-co-ban/blob/main/kien_thuc_moi/Queue.md)
---

## **Lưu ý với hàm tĩnh (static function)** ##

- Nếu dùng hàm tĩnh muốn dùng biến của lớp con thì dùn `static`

- ví dụ: 

```php
// Users.php
class Users extends Table
{
    // ...
        protected static $key_students = "student_";
    // ...
}
// Table.php
class Table
{
    // ...
     public static function saveValue($request = NULL)
    {
        if (!empty(static::$key_students)) {
            self::clearCache(static::$key_students);
        }
    }
    // ...
}

```

## **Chỉnh sửa Modal**

- muốn tạo một hàm viết sẵn cho các table sau kế thừa ta phải dùng `static function` (phương thức tĩnh)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tables extends Model
{
    public static function selectALL(){
        return self::select()->paginate(LIMIT)->items();
    }
}

```

## **Sử dụng ide helper**

Chạy lần lượt các lệnh sau để cài đặt

```sh
composer require --dev barryvdh/laravel-ide-helper
```

```sh
php artisan ide-helper:models "App\Models\[model cần hỗ trợ]" -w
```

---

## **Mã hóa (hash)**

```sh
  - Hash::make(value) //dùng để mã hóa
  - Hash::check(value, hashvalue)// dùng để kiểm tra mã hóa
```

---

## **Up host**

-   Kéo cả thư mục Larave đư lên host sau đó thêm thư mục .htaccess ngang cấp với public để ẩn chữ pubilic trên url

```sh
   <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ /public/$1 [L,QSA]
   </IfModule>
```

---

## **Phân trang**

-   Sử dụng thư viện `LengthAwarePaginator`.

    ```php
    use Illuminate\Pagination\LengthAwarePaginator;
    $res = new LengthAwarePaginator(
                    $query->values(), // Dữ liệu cho trang hiện tại
                    $total_value, // Tổng số bản ghi sau khi lọc
                    LIMIT, // Số bản ghi trên mỗi trang
                    $page, // Trang hiện tại
                    ['path' => request()->url(), 'query' => request()->query()] // Đường dẫn và các query parameters cho liên kết phân trang
                );
    ```


**khác**
-   Sử dụng `response` để tạo trả về kiểu api để dễ tạo phân trang tùy ý

```js
 public function api()
    {
        $data = Student::query()->with('course')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $data->getCollection(),
            'pagination' => $data->linkCollection()
        ]);
    }
```

-   Bên view tạo `ajax jquery` để lấy dữ liệu về sau đó đổ dữ liệu sau vài success `chi tiết trong phần jquery bên dưới`

```js
//tạo div ở trên body
<div class='justify-content-center pagination' id='pagination'></div>;

//đổ dữ liệu sau vào script và gọi lại tên hàm và truyền dữ liệu trong phần success jquerry vd `renderPagination(response.pagination);`
function renderPagination(links) {
    links.forEach(function (each) {
        $('#pagination').append(
            $('<li>')
                .attr('class', `page-item ${each.active ? 'active' : ''}`)
                .append(`<a class="page-link" href="${each.url}">${each.label}</a> `)
        );
    });
}
// chỉnh lại url cho chuẩn và lưu ý thêm  data: { page: {{ request()->get('page') ?? 1 }} } ở ajax
// thêm ` let max_page; và max_page = response.pagination[response.pagination.length - 2].label;`
$(document).on('click', '#pagination > li > a', function (event) {
    event.preventDefault(); //ngăn event button hoạt động
    let page = $(this).text();
    let urlParams = new URLSearchParams(window.location.search); //lấy giá trị trên url
    let get_page = urlParams.get('page');

    if ('« Previous' !== page && page !== 'Next »') {
        urlParams.set('page', page);
        window.location.search = urlParams;
    } else if ('« Previous' === page && parseInt(get_page) - 1 >= 1) {
        page = parseInt(get_page) - 1;
        urlParams.set('page', page);
        window.location.search = urlParams;
    } else if ('Next »' === page && parseInt(get_page) + 1 <= max_page) {
        page = parseInt(get_page) + 1;
        urlParams.set('page', page);
        window.location.search = urlParams;
    }
});
```

---

## **Chỉnh sửa lại migration đã tồn tại hoặc đã chạy trên hệ thống (Thêm dữ liệu trong SQL )**

-   Tạo mới một migration `php artisan make:migration alter_table`
-   Sử dụng cú pháp sau để thực thi
    ```php
    if (!Schema:hasColumn('tên bảng', 'Tên cột')){ // kiểm tra cột có tồn tại hay không
      //dữ liệu có sẵn khi tạo migration và chỉnh sửa
      Schema::table('tên bảng', function (Blueprint $table)
       {
                // muốn thay đổi gì  đó thì thêm change()
                // vd: $table->smallInteger('role')->default('4')->change();
      });
    }
    ```

---

## **Đặt tên title trang**

-   Nên tên lại trong file `env` hoặc `config > app > name` sau đó đổi tên trong filename
-   Để dùng thì ta dùng thì ta gọi bằng cách `config('app.name')`

---

## Xử lý giá trị null

**Nếu trong bảng có hàng tồn tại giá trị null gây nên lỗi thì ta sử dụng `optional` để tránh tình trạng báo lỗi**

---

## jquery

**submit dữ liệu khi click vào option lựa chọn trong form có chứa select**

```js
$(document).ready(function () {
    $('.select-filter').change(function () {
        $('#form-filter').submit();
    });
});
```

**Tạo Thông báo notification trong jquery**

-   Đảm bảo khai báo đầy đủ các cdn sau để tiện cho việc sử dụng

```sh
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> // khai báo jquery
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> // khai báo jquerry kèm theo toastr
```

-   Sau khi khai báo xong, sử dụng câu lệnh sau để tạo thông báo

```js
$(document).ready(function () {
    toastr.options.positionClass = 'toast-bottom-right'; // điều chỉnh hướng hiển thị
    toastr.success('Nội dung thông báo', 'title'); // có thể sử dụng warning, success, error, info
});
```

-   Có thể tham khảo thêm [tại đây](https://github.com/CodeSeven/toastr)

**Import dữ liệu với jqerry**

```js
$(document).ready(function () {
                toastr.options.positionClass = 'toast-bottom-right';
                toastr.options.closeButton = true;
                toastr.options.preventDuplicates = false;

                $("#csv").change(function () {
                    let formData = new FormData();
                    formData.append('csv', $(this)[0].files[0]);
                    formData.append('_token','{{ csrf_token() }}');
                    $.ajax({
                        url: '{{route('import')}}',
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        enctype: 'multipart/form-data',
                        async: false,
                        cache: false,
                        contentType: false,
                        processData: false,

                        success: function (response) {
                            toastr.success("Your data has been imported", "Import success");

                        },
                        error: function (response) {
                            toastr.error(response.responseJSON.message, "Import error");
                        }
                    })
                });
            });
```

**Khi sử dụng ajax jquery gặp lỗi `CSRF token mismatch.` đó là do thiếu csrf, cách fix đó là thêm ở `data: {_token: '{{ csrf_token() }}'}`**

**Đổ dữ liệu vào form**

```js
 $(document).ready(function () {
            $.ajax({
                url: '{{ route('api') }}',
                datatype: 'json',
                success: function (response){
                    // console.log(response)
                    response.forEach(function (each) {
                        // console.log(each); test thử xem dữ liệu có về hay không, sử dụng append là chính
                        $('#table_student').append($('<tr>')
                        .append($('<td>').append(each.id))
                        .append($('<td>').append(each.nameStudent))
                        .append($('<td>').append(each.created_at))
                        );
                    })
                },
                error: function (response) {

                }
            })
        })
```

**Cập nhật lại trang với jquery**

```
 $("id").load(location.href + "id");
```

---

## Xóa mềm

-   Khai báo `use SoftDeletes` trong `use Illuminate\Database\Eloquent\SoftDeletes;` ở trong Model
-   tạo cột `deleted_at` ở trong sql
-   sau đó xóa như bình thường là được vd `$this->model->destroy(ID)`

---

## **Tạo lưu trữ ảnh với Drive trên Laravel qua dự án của Google**

## **Các Thao tác bên Google cloud**

B1: Khởi tạo dự án

    Tạo project: https://console.developers.google.com/projectcreate

    Tạo credentials: https://console.developers.google.com/apis/credentials

B2: Đăng kí OAuth consent screen trước rồi sau đó.

    truy cập credentials > Tạo file credentials > OAuth client ID

B3: Đặt tên miền sau đó click vào Authorized redirect URIs

**Khác với đăng nhập bằng tài khoản google chúng ta sẽ đặt điều hướng về oauthplayground**

=> https://developers.google.com/oauthplayground

B4: Vào Library > kiếm Google Drive > click vô nó

B5: Truy cập link https://developers.google.com/oauthplayground

B6: Click vô cài đặt > tích chọn use your own OAuth > nhập mã OAuth khi tạo credentials

B7: Kiếm Drive API v3 > chọn ALL > Auth...APIs

B8: Đăng nhập tài khoản google tích chọn full quyền

B9: Click Exchange auth code for tokens

B10: Copy refresh_token bên json

B11: Qua drive tạo folder lưu > copy đuôi trên url

## **Các thao tác cần thực hiện bên laravel**

B1: Cài đặt package sau đối với laravel 9

```sh
composer require masbug/flysystem-google-drive-ext
```

B2:Tạo file GoogleDriveServiceProvider trong `app > Provider` sau đó thêm dữ liệu sau

```php
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

B3: Thêm dữ liệu sau vào `providers` trong file `config/app.php`

```php
App\Providers\GoogleDriveServiceProvider::class,
```

B4: Copy file sau dán vào `config/filesystems.php`

```php
'google' => [
        'driver' => 'google',
        'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
        'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
        'folder' => env('GOOGLE_DRIVE_FOLDER'),
        //'teamDriveId' => env('GOOGLE_DRIVE_TEAM_DRIVE_ID'),
    ],
```

B5: Cập nhật file .env Thêm ClientID, ClientSecret, RefreshToken vừa thực hiện các bước ở trên vào file env.

## **Một số lưu ý khi thao tác**

-   **Để đẩy file lên drive ta dùng lệnh `Storage::disk('google')->putFile('file, nội dung, sử dụng public nếu muốn công khai file)`**
-   **Để lấy toàn bộ danh sách trên drive về ta dùng lệnh `collect(Storage::disk('google')->listContents('/', có lấy thư mục con hay không (true or false)))` lưu ý nên sử dụng mảng để truy xuất dữ liệu `array[1][path]`**

**Ví dụ code xử lý vụ đăng tải file lên sau đó lấy link về**

```php
Route::post('test', function (Request $request) {
    // thêm dữ liệu
    $path_file = $request->file('file');
    $name_folder = $request->get('name');
    $name_file = $path_file->getClientOriginalName();

    //kiểm tra tồn tại, nếu có xóa dữ liệu trong database và drive sau đó thêm lại
    if(Storage::disk('google')->exists($name_folder.'/'.$name_file))
    {
        $data =  DriveSQL::query()->where('Pathfile', $name_folder.'/'.$name_file)->first();
        DriveSQL::destroy($data->id);
        Storage::disk('google')->delete($name_folder.'/'.$name_file);
        $path = Storage::disk('google')->putFileAs($name_folder, $path_file, $name_file, 'public');
    }
    else
    {
        $path = Storage::disk('google')->putFileAs($name_folder, $path_file, $name_file, 'public');
    }

    // dùng để lấy link
    $dir = '/';
    $recursive = true; // Có lấy file trong các thư mục con không?
    $contents = collect(Storage::disk('google')->listContents($dir, $recursive));
    $res = $contents->where('path', '=', $path)->first();

    $data = [
        'Pathfile' => $path,
        'linkFile' =>'https://drive.google.com/file/d/'.$res['extra_metadata']['id'].'/view'
    ];

    DriveSQL::create($data);
    return redirect()->route('PostIMG');
})->name('put_image');
```

-   **Để xóa link trên Drive ra dùng `Storage::disk('google')->delete($res['path'])`**

**Vd: xóa path được lấy về qua request**

```php
Route::get('delete', function (Request $request){
    $path = $request->get('path');
    $dir = '/';
    $recursive = true; // Có lấy file trong các thư mục con không?
    $contents = collect(Storage::disk('google')->listContents($dir, $recursive));//lấy tất cả danh sách về
    $res = $contents->where('path', '=', $path)->first();

    Storage::disk('google')->delete($res['path']);
});
```

**Vd: downLoad dữ liệu từ drive về global**

```php
Route::post('down/{id}', function ($id) {
    // Thao tác trên Database để lấy path về
    $data =  DriveSQL::query()->find($id);
    $path = $data->Pathfile;

    // Kết nối với drive
    $dir = '/';
    $recursive = true; // Có lấy file trong các thư mục con không?
    $contents = collect(Storage::disk('google')->listContents($dir, $recursive));
    $res = $contents->where('path', '=', $path)->first();
    // lấy tên file và lấy đuôi của file
    $name = $res['extra_metadata']['filename'];
    $extension =  $res['extra_metadata']['extension'];

    // Download file
    $rawData = Storage::disk('google')->get($path);

    return response($rawData, 200)
    ->header('Content-Type',$res['mimeType'])
    ->header('Content-Disposition', "attachment; filename=$name.$extension");
})->name('get_file');

```

---

## Import Laravel excel

**Xử dụng [laravel Execel](https://docs.laravel-excel.com)**

_Để có thể sử dụng mượt mà, tối ưu hơn nên kết hợp với [jquery](#jquery)_

-   B1: Cài đặt thư viện, xử dụng câu lệnh sau để cài đặt.

```sh
composer require maatwebsite/excel
```

-   Nếu có lỗi xảy ra do cài đặt trên laravel 9 hãy xử dụng câu lệnh sau

```sh
composer require psr/simple-cache:^1.0 maatwebsite/excel
```

-   B2: Xử dụng câu lệnh này để tạo file import, nhớ đổi tên model để phù hợp

```sh
php artisan make:import UsersImport --model=Post
```

-   B3: Sửa lại file vừa tạo từ `ToModel` thành `ToArray`, sau đó đổi tên function thành array (chỗ nào báo lỗi thì cứ cho thành array :3)

-   B4: Xử dụng file controller để gọi tới file vừa tạo ở B1.

```sh
 Maatwebsite\Excel\Facades\Excel::import(new 'tên_file_ở_bước_1', 'tên_file_có_thể_lấy_qua_request');
```

-   B5: Thêm Heading row bằng cách implements nó

VD:

```php
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class PostImport implements ToArray,WithHeadingRow
```

-   B6: Lấy giá trị bằng cách sử dụng `$array['tên cột']`

Vd:
$company = $array['cong_ty'];
$Language = $array['ngon_ngu'];

-   B7: Lưu giá trị import bằng cách sử dụng Eloquent, nên sử dụng `firstOrCreate` để tránh bị trùng dữ liệu

vd:

```php
foreach ($array as $each) {
            try {
                Post::query()->firstOrCreate([
                    'company' => $each['cong_ty'],
                    'language' => $each['ngon_ngu'],
                    'location' => $each['dia_diem'],
                    'link' => $each['link'],
                ]);
            } catch (Exception $e) {
                dd($each);
            }
        }
```

---

## Export Laravel excel

```sh
composer require maatwebsite/excel
```

-   Nếu có lỗi xảy ra do cài đặt trên laravel 9 hãy xử dụng câu lệnh sau

```sh
composer require psr/simple-cache:^1.0 maatwebsite/excel
```

-   B2: Xử dụng câu lệnh này để tạo file export, nhớ đổi tên model để phù hợp. File sau khi tạo sẽ nằm trong `app/excel`

```sh
php artisan make:export UsersExport --model=User
```

-   B3: Khởi tạo function export trong controller muốn export

```php
    // UsersExport in UsersController
    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
```

-   B4: Khai báo route và sài.

### lưu ý

-   `WithHeadings` Để thêm header cho excel
-   `ShouldAutoSize` Căn chỉnh
-   `WithStyles` Thêm màu cho đẹp

```php
<?php

namespace App\Exports;

use App\Models\Course;
use Faker\Core\Color;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CoursesExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Course::all();
    }

    public function headings(): array
    {
        return [
            'NO. ',
            'NameCourse',
            'Created At',
            'Updated At',
            'Del Fag',
            'Active'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        //kẻ đường viền cho tất cả các ô
        $sheet->getStyle('A1:F'.$sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ]
        ]);
        return [
            // set riêng chữ và đổ màu giao diện cho hàng đầu tiên
            1 => [
                // set về chữ
                'font' => [
                    'bold' => true,
                    'size' => '18',
                    'color' => ['argb' => 'FFFFFF']
                ],
                // set về giao diện
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => '008000',
                    ],
                ],
            ]
        ];
    }

}


```

-   Nếu muốn chỉnh sửa lại cho hợp lý thì ta nên sử dụng `FromView`. Với cách sửa dụng y như controller bình thường là truyền data rồi gọi tới view

```php
namespace App\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoicesExport implements FromView
{
    public function view(): View
    {
        return view('exports.invoices', [
            'invoices' => Invoice::all()
        ]);
    }
}
```

-   Sử dụng `WithMapping` để lấy ra dữ liệu mình muốn

```php
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromQuery, WithMapping
{
    /**
    * @var Invoice $invoice
    */
    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->user->name,
            Date::dateTimeToExcel($invoice->created_at),
        ];
    }
}
```

---

# Transaction

```php
  public function store(ValidateCourse $request)
    {
        try{
            DB::beginTransaction(); // khai báo sự kiện
        $this->m_course->query()->create($request->validated());
        DB::commit(); // xác nhận
        return redirect()->route('courses.index');
        }catch(Exception $e){
            DB::rollBack(); // hủy
        }
    }
```

# CronJob

B1: câu lệnh khởi tạo `command`. file command sẽ được khởi tạo bên trong `/app/Console/Command/`

```sh
    php artisan make:command {namefile} --command={nameCommand}:cron
```

-   ví dụ `php artisan make:command DemoCron --command=demo:cron`

B2: vào file vừa tạo cấu hình trong `handle`

```php
public function handle()
{
    Log::info("Cron Job running at ". now());

    User::create([
        'name' => 'test2',
        'email' => 'test2@gmail.com',
        'password' => bcrypt('123456789')
    ]);

    return 0;
}
```

B3: khai báo trong `kernel`

```sh
protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('demo:cron')
            ->everyMinute();
    }
```

B4: lệnh chạy file

```sh
php artisan schedule:run        // chạy file
php artisan schedule:work       // chạy ngầm
php artisan schedule:list       // xem thời gian chạy
```

B5: cấu hình server thì chạy crontab

vd

```sh
crontab - e
# "C:\laragon\bin\php\php-8.1.0\php.exe" "artisan" demo:cron > "NUL" 2>&1
```

nếu bị lỗi 'No scheduled commands are ready to run.' thì chạy lệnh `composer require guzzlehttp/guzzle`

---
# Unzip file
```php
$zip = new \ZipArchive();
    if ($zip->open($file ) == TRUE) {
        // Giải nén tất cả các tệp tin từ tệp tin ZIP vào thư mục đích
        $zip->extractTo('dir/unzip');
        $zip->close();
        echo 'Giải nén tệp tin ZIP thành công';
    } else {
        echo 'Không thể mở tệp tin ZIP';
    }
```
