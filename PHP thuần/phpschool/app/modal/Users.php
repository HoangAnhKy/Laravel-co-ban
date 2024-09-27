<?php

namespace modal;
require_once __DIR__."/Table.php";
class Users extends Table
{
    public $alias = "users";
    public $id = null;

    public $full_name = null;
    public $email = null;
    public $password = null;
    public $token = null;

    public function __construct($user = [])
    {
        if (!empty($user)){
            $this->id = $user['id'] ?? "";
            $this->full_name = $user['full_name'] ?? "";
            $this->email = $user['email'] ?? "";
            $this->password = $user['password'] ?? "";
            $this->token = $user['token'] ?? "";
        }
    }
}