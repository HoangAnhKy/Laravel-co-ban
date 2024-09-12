<?php

namespace controller;

use modal\Users;

require "Controller.php";
require __DIR__ . "/../modal/Users.php";

class UsersController extends Controller
{
    private $modal;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        require __DIR__ . "/../view/login.php";
    }

    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            header('Content-Type: application/json');
            $user = new Users($_POST);
            $email = $user->getEmail();
            $password = md5($user->getPassword());
            $sql = sprintf("SELECT * FROM users WHERE email = '%s' and password = '%s'", $email, $password);
            $result = $this->db->seclect($sql, "Users");
            http_response_code(200);
            echo json_encode(["data" => $result]);
        }
    }

    public function register()
    {
        $sql = "INSERT INTO users (email, password) VALUES ('kdev@gmail.com','202cb962ac59075b964b07152d234b70')";
        $this->db->exec($sql);
    }
}