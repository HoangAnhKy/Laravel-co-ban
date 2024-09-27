# cài đặt jwt với composer

```sh
composer require firebase/php-jwt
```

# Cách dùng

```php
<?php

// khai báo 
use Firebase\JWT\Key;
use Firebase\JWT\JWT;


class UsersController
{
    private $secretKey = 'key';
    // ...

    public function login($username, $password)
    {
        // Giả sử bạn kiểm tra thông tin người dùng trong database
        if ($username == 'user1' && $password == 'password123') {
            // Nếu thông tin đúng, tạo JWT
            $payload = [
               "code" => time().uniqid()
            ];

            // Tạo JWT
            $jwt = JWT::encode($payload, $this->secretKey, 'HS256');
            
            // Lưu cookie
            setcookie("auth_sys", $jwt,  time() + 86400 );

            // giải mã hóa để test
            $jwt_decode = JWT::decode($jwt, new Key($this->secretKey, 'HS256'));
            // Trả về JWT cho client
            echo json_encode([
                'status' => 'success',
                'token' => $jwt,
                'decode' => $jwt_decode
            ]);
            die;
    }

}

```