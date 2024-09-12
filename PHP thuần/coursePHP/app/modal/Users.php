<?php

namespace modal;
class Users
{
    public $id;
    public $email;
    public $password;
    public $created_at;
    public $updated_at;

    public function __construct($user = [])
    {
        $this->id = $user["id"] ?? "";
        $this->email = $user["email"] ?? "";
        $this->password = $user["password"] ?? "";
        $this->created_at = $user["created_at"] ?? "";
        $this->updated_at = $user["updated_at"] ?? "";
    }

    public function getId()
    {
        return $this->id ?? "";
    }

    public function getEmail()
    {
        return $this->email ?? "";
    }

    public function getPassword()
    {
        return $this->password ?? "";
    }

    public function getCreatedAt()
    {
        return $this->created_at ?? "";
    }

    public function getUpdatedAt()
    {
        return $this->updated_at ?? "";
    }

    public function setId($id)
    {
        return $this->id = $id;
    }

    public function setEmail($email)
    {
        return $this->email = $email;
    }

    public function setPassword($password)
    {
        return $this->password = $password;
    }

    public function setCreatedAt($created_at)
    {
        return $this->created_at = $created_at;
    }

    public function setUpdatedAt($updated_at)
    {
        return $this->updated_at = $updated_at;
    }
}