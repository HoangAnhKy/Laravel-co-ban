<?php

use Firebase\JWT\Key;
use \Firebase\JWT\JWT;
use modal\Users;

require __DIR__ . "/Controller.php";
require __DIR__ . '/../modal/Users.php';

class UsersController extends Controller
{

    private $modal;
    private $layout;
    protected $secretKey;


    public function __construct()
    {
        parent::__construct();
        $this->secretKey = "key";

        $this->modal = new Users();
        $this->layout = __DIR__ . "/../view/users/";
    }

    public function login($email, $password)
    {
        if (!empty($email) && !empty($password)){

            $sql = sprintf("SELECT * FROM {$this->modal->alias} WHERE email = '%s' and password = '%s'", $email, $password);
            $user = $this->modal->selectOne($sql, $this->modal->alias);

            if(!empty($user)){
                $jwt = JWT::encode([
                    "code" => (time() + 86400).uniqid()
                ], $this->secretKey, "HS256");

                $user->token = $jwt;
                setcookie("auth_sys", $jwt, time() + 86400, "/", "phpschool.local.com"); //1p
            }
        }
    }

}