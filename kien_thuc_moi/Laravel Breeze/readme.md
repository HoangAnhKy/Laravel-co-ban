**`Laravel Breeze`** là một gói cài đặt được cung cấp bởi Laravel Framework, giúp thiết lập nhanh chóng và dễ dàng các chức năng xác thực (authentication) cơ bản cho một ứng dụng Laravel. Breeze là một giải pháp đơn giản, nhẹ nhàng, phù hợp với các dự án nhỏ hoặc các ứng dụng không yêu cầu quá nhiều tùy chỉnh phức tạp trong hệ thống xác thực.

# cài đặt Laravel Breeze:

- Cài đặt Laravel Breeze thông qua Composer:

    ```sh
    composer require laravel/breeze --dev
    ```

## Breeze and Blade
- Chạy lệnh để thiết lập Breeze: 

    ```sh
    php artisan breeze:install
    ```

    - Xuất hiện các lựa chọn chọn theo thứ mình cần. Ví dụ:
        - Blade
        - Yes
        - PHPUnit

- Cài đặt database và các lệnh khác nếu chưa chạy:

    ```sh
    php artisan migrate
    npm install
    npm run dev
    ```