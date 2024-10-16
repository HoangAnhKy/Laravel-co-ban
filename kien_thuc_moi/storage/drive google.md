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

B4: Vào `Library` > kiếm Google Drive > click vô nó

B5: Truy cập link https://developers.google.com/oauthplayground

B6: Click vô cài đặt > tích chọn use your own OAuth > `nhập mã OAuth khi tạo credentials`

B7: Kiếm Drive API v3 > chọn ALL > Auth...APIs

B8: Đăng nhập tài khoản google tích chọn quyền

B9: Click Exchange auth code for tokens

B10: Copy `refresh_token` bên json

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

B3: Copy file sau dán vào `config/filesystems.php`

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

B4: Cập nhật file `.env` Thêm ClientID, ClientSecret, RefreshToken vừa thực hiện các bước ở trên vào file env.
