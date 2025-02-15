`Service Provider` là thành phần cốt lõi để đăng ký và cấu hình các dịch vụ của ứng dụng. Nói một cách đơn giản, nó giúp quản lý và "nạp" các phần mềm (services) mà ứng dụng cần sử dụng.

Cụ thể, Service Provider có các vai trò sau:

- `Đăng ký dịch vụ (Binding Services)`: Chúng dùng để liên kết các lớp, đối tượng hoặc hàm vào trong service container của Laravel, có thể gọi chúng ở bất cứ đâu trong ứng dụng thông qua dependency injection hoặc facade.
- `Khởi tạo (Bootstrapping)`: Trong phương thức boot(), có thể thực hiện các công việc như đăng ký các sự kiện, middleware, hoặc cấu hình route, view,... sau khi tất cả các dịch vụ khác đã được đăng ký.
- `Tổ chức mã nguồn`: Bằng cách sử dụng Service Provider, có thể tách riêng các chức năng, module của ứng dụng thành các phần độc lập, dễ quản lý và bảo trì hơn.

Khi nào nên dùng:

- khi có các thành phần hoặc dịch vụ mới cần đăng ký vào container, hoặc khi cần thực hiện các công việc bootstrapping (ví dụ: đăng ký event listeners, cấu hình package, …).

# lệnh khởi tạo

```sh
php artisan make:provider ServiceProviderName
```

# khai báo Provider

mặc định là nó tự khai báo trong này

```php
// bootstrap/provider
<?php

return [
    App\Providers\AppServiceProvider::class,
];
```
