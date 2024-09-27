<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Controller
{

    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = "key";
        if (strpos($_SERVER["REQUEST_URI"], "/users/login")){
            $this->verify();
        }
    }

    public function index()
    {
        require __DIR__ . "/../view/hello.php";
    }

    public function verify()
    {
        try {

            if (isset($_COOKIE["auth_sys"])) {
                $jwt = JWT::decode($_COOKIE["auth_sys"], new Key($this->secretKey, "HS256"));
                if (!empty($jwt) && isset($jwt->code) && (int)substr($jwt->code, 0, 10) > time()) {
                    echo "Token còn hạn.";
                } else {
                    echo "Token đã hết hạn.";
                }
            }else {
                header("location: " . BASE_URL . "/users/login/kha@gmail.com/123");
                exit;
            }
        } catch (\Exception $e) {
            echo "Token không hợp lệ: " . $e->getMessage();
            exit;
        }
    }
}