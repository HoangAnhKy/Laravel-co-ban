# Lệnh khởi tạo command

- File sẽ nằm tại `app/Console/Commands/`

    ```sh
    php artisan make:command NameCommand
    ```
-  Lưu ý: `signature` là lệnh chạy nó sẽ thực thi tất cả trong `handle`

    ```php
        protected $signature = 'temp:clear';

        /* sẽ thực thi bằng php artisan temp:clear
        hoặc

        Artisan::call('temp:clear');
        */

    ```
